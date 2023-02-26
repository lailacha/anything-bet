<?php

namespace App\Controller\Front;

use App\Entity\BettingGroup;
use App\Entity\Bet;
use App\Entity\Betting;
use App\Entity\Event;
use App\Entity\Notification;
use App\Entity\Points;
use App\Form\BetType;
use App\Form\EndBetEventType;
use App\Form\EventType;
use App\Repository\BetRepository;
use App\Repository\BettingGroupRepository;
use App\Repository\BettingRepository;
use App\Repository\EventRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Security\Voter\EventVoter;
use App\Repository\PointsRepository;
use App\Security\Voter\GroupAdminVoter;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Mailjet\Client;
use Mailjet\Resources;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use function PHPUnit\Framework\isEmpty;

#[IsGranted('ROLE_USER')]
#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/my-groups', name: 'app_event_my_groups', methods: ['GET'])]
    public function myGroups(EventRepository $eventRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $query = $eventRepository->createQueryBuilder('e')
            ->where('e.bettingGroup IN (:groups)')
            ->andWhere('e.finishAt > :now')
            ->setParameter('groups', $this->getUser()->getBettingGroups())
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery();


        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('event/index.html.twig', [
            'events' => $pagination,
        ]);
    }


    #[Route('/publics', name: 'app_event_publics', methods: ['GET'])]
    public function getPublicEvents(EventRepository $eventRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $query = $eventRepository->createQueryBuilder('e')
            ->where('e.bettingGroup IS NULL')
            ->andWhere('e.finishAt > :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('event/index.html.twig', [
            'events' => $pagination,
            'isPublic' => true,
        ]);
    }

    #[Route('/group/{id}', name: 'app_event_group', methods: ['GET'])]
    public function getByGroup(BettingGroup $bettingGroup): Response
    {
        return $this->render('event/group.html.twig', [
            'events' => $bettingGroup->getEvents(),
            'bettingGroup' => $bettingGroup,
        ]);
    }

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository, Request $request, PaginatorInterface $paginator): Response
    {

        //les events où finiAt est passé
        $query = $eventRepository->createQueryBuilder('e')
            ->where('e.finishAt > :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('event/index.html.twig', [
            'events' => $pagination,
        ]);
    }



    #[Route('/search', name: 'app_event_search', methods: ['GET'])]
    public function search(Request $request, EventRepository $eventRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search');
        $fromMyEvents = $request->query->get('fromMyEvents');
        $results = $eventRepository->search($search, $fromMyEvents ? $this->getUser() : null);


        $pagination = $paginator->paginate(
            $results,
            $request->query->getInt('page', 1),
            10
        );



        return $this->render('event/index.html.twig', [
            'events' => $pagination,
        ]);
    }

    #[Route('/new/{group}', name: 'app_event_new', defaults: ['group' => null], methods: ['GET', 'POST'])]
    public function new(Request $request, EventRepository $eventRepository, BetRepository $betRepository, BettingGroup $group = null, NotificationRepository $notifyRepository): Response
    {

        $event = new Event();
        $form = $this->createForm(EventType::class, $event, [
            'betting_group' => $group,
        ]);
        $form->get('startAt')->setData(new \DateTimeImmutable());
        $form->get('finishAt')->setData(new \DateTimeImmutable('+1 day'));

        $form->handleRequest($request);


        if($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                if ($event->getFinishAt() < $event->getStartAt()) {
                    $this->addFlash('danger', 'La date de fin doit être supérieure à la date de début');
                } else {

                    $event->setCreatedAt(new \DateTimeImmutable());
                    $event->setTheUser($this->getUser());
                    $event->setName($form->get('name')->getData());
                    if($form->get('betting_group')) {
                        $event->setBettingGroup($form->get('betting_group')->getData());
                    }
                    $coverImage = $form->get('coverImage')->getData();
                    if ($coverImage) {
                        $fileUploader = new FileUploader($this->getParameter('cover_image_directory'));
                        $newFilename = $fileUploader->upload($coverImage);
                        if($newFilename) {
                            $event->setCoverImage($newFilename);
                        } else {
                            $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                        }
                    }

                    // remove bets from event because we will persist them later
                    $event->setBets(new ArrayCollection());

                    $eventRepository->save($event, true);

                    if($group){
                        //get members of group
                        $members = $group->getMembers();

                        // notify members of group
                        foreach($members as $member)
                        {
                            try {
                                $notification = new Notification();
                                $notification->setUser($member);
                                $notification->setMessage('Un nouvel événement a été créé dans le groupe ' . $group->getName());
                                $notification->setCreatedAt(new \DateTimeImmutable());
                                $notifyRepository->save($notification, true);
                            }
                            catch (\Exception $e) {
                                throw $e;
                            }

                            $data = [
                                'betting_group' => $group->getName(),
                                'event_label' => $event->getName(),
                                'bet_link' => $_ENV['APP_URL'].'/event/' . $event->getId(),
                            ];


                            // to object


                            $jsonString = json_encode($data, JSON_THROW_ON_ERROR);

                            $mj = new Client($_ENV['MAILJET_APIKEY'],$_ENV['MAILJET_SECRET_KEY'], true, ['version' => 'v3.1']);
                            $body = [
                                'Messages' => [
                                    [
                                        'From' => [
                                            'Email' => "laila.charaoui@outlook.fr",
                                            'Name' => "Un nouvel évenement a été créé dans le groupe " . $group->getName()
                                        ],
                                        'To' => [
                                            [
                                                'Email' => $member->getEmail(),
                                                'Name' => $member->getPseudo(),
                                            ]
                                        ],
                                        'TemplateLanguage' => true,
                                        'TemplateID' => 4612916,
                                        'Subject' => "Un nouvel évenement a été créé dans le groupe " . $group->getName(),
                                        'Variables' => json_decode($jsonString, false, 512, JSON_THROW_ON_ERROR)
                            ]
                                ]
                            ];

                            $response = $mj->post(Resources::$Email, ['body' => $body]);
                            $response->success() && var_dump($response->getData());

                        }

                    }

                    $betsData = $request->get('event')['bets'];
                    $betsCollection = new ArrayCollection();
                    foreach ($betsData as $betData) {
                        $bet = new Bet();
                        $bet->setLabel($betData['label']);
                        $bet->setEvent($event);

                        $betRepository->save($bet, true);
                        $betsCollection->add($bet);
                        $event->addBet($bet);
                    }
                }
            } catch (\Exception $e) {
                throw $e;
            } finally {

                $this->addFlash('success', 'L\'événement a bien été créé');
            }
            return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
            'bettingGroup' => $group,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event, BettingRepository $bettingRepository): Response
    {

        // verify if user has already bet on this event
        $user = $this->getUser();
        $hasBet = $bettingRepository->findUsersByEvent($event->getId(), $user->getId());

        $ids = [];
        foreach($hasBet as $bet) {
            $ids[] = $bet["betId"];
        }


        return $this->render('event/show.html.twig', [
            'event' => $event,
            'alreadyBet' => $hasBet === null ? false : $ids,
            'amount' =>  $hasBet[0]['amount'] ?? false,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository, BetRepository $betRepository): Response
    {
        $this->denyAccessUnlessGranted(EventVoter::EDIT, $event);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // get bets from request that are not in the database yet

            $betsData = $request->get('event')->getBets();
            foreach ($betsData as $betDatas) {

                if($betDatas->getId() === null) {
                    $bet = new Bet();
                    $bet->setLabel($betDatas->getLabel());
                    $bet->setEvent($event);
                    $betRepository->save($bet, true);
                }

            }

            if($request->get('event')->getCoverImage() !== null) {
                $coverImage = $form->get('coverImage')->getData();
                if ($coverImage) {
                    $fileUploader = new FileUploader($this->getParameter('cover_image_directory'));
                    $newFilename = $fileUploader->upload($coverImage);
                    if($newFilename !== false) {
                        $event->setCoverImage($newFilename);

                    } else {
                        $this->addFlash('danger', $newFilename);

                        // redirect to edit page with HTTP error
                        return $this->redirectToRoute('front_app_event_edit', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);

                    }
                }
            }

            $eventRepository->save($event, true);

            return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $this->denyAccessUnlessGranted(EventVoter::DELETE, $event);
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $eventRepository->remove($event, true);
        }

        return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/bet/submit', name: 'app_event_bet', methods: ['POST'])]

    public function submitBet(Request $request, BetRepository $betRepository, BettingRepository $bettingRepository, PointsRepository $pointsRepository ): JsonResponse
    {


        // we get json data from request
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $data['somme'] = (int) $data['somme'];

        //check csrf token
        if (!$this->isCsrfTokenValid('bet', $data['csrf_token'])) {
            throw new InvalidCsrfTokenException();
        }

        if($data['bet'] === null || $data['somme'] === null){
            $this->addFlash('erreur', 'Une erreur est survenue');
             return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        $user = $this->getUser();
        $bet = $betRepository->find($data['bet']);

        if($bet === null){
            $this->addFlash('erreur', 'Une erreur est survenue');
             return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        // verify if the event is related to a betting group
        $bettingGroup = $bet->getEvent()->getBettingGroup();

        //points de l'utilisateur
        $points = $pointsRepository->findOneBy(['idUser' => $user->getId(), 'idBettingGroup' => $bettingGroup === null ? null : $bettingGroup->getId()]);

        if($points === null){
            $points = new Points();
            $points->setUser($user);
            $points->setScore(0);
            $pointsRepository->save($points, true);
        }

        $scoreAfterBet = $points->getScore() - $data['somme'];


        $thisEventId = $betRepository->findOneBy(['id' => $data['bet']])->getEvent()->getId();


        $usersEvent = $bettingRepository->findUsersByEvent($thisEventId, $user->getId());

        //si le score de l'utilisateur est inférieur à la somme qu'il veut miser
        if($scoreAfterBet < 0) {
            $this->addFlash('error', 'Vous n\'avez pas assez de points pour miser cette somme');
            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }else if ($data['somme'] < 0) {
            $this->addFlash('error', 'Vous ne pouvez pas miser une somme négative');

            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }
        //si l'utilisateur a déjà parié sur cet event
        else if($usersEvent){
            $this->addFlash('error', 'Vous avez déjà parié sur cet événement');

            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);

        }

        else{
            //le choix de l'utilisateur


            $points->setScore($scoreAfterBet);
            $pointsRepository->save($points, true);



            //creation betting
            $betting = new Betting();
            $betting->setIdUser($user);
            $betting->setIdBet($bet);
            $betting->setAmount($data['somme']);


            //sauvegarde betting
            $bettingRepository->save($betting, true);
            $this->addFlash('success', 'Votre pari a bien été pris en compte');
        }



        return new JsonResponse(['success' => true], Response::HTTP_OK);

    }


    #[Route('/event/end/{id}', name: 'app_event_end_show', methods: ['GET'])]
    public function endShow(Event $event, EventRepository $eventRepository, BetRepository $betRepository, BettingRepository $bettingRepository, PointsRepository $pointsRepository, UserRepository $userRepository, BettingGroupRepository $bettingGroupRepository, ): Response
    {

        $bets = $betRepository->findBy(['event' => $event->getId()]);

        return $this->renderForm('event/end.html.twig', [
            'event' => $event,
            'bets' => $bets,
        ]);

    }



    #[Route('/event/end/{id}', name: 'app_event_endevent', methods: ['GET', 'POST'])]
    public function endEvent(Request $request, Event $event, EventRepository $eventRepository, BetRepository $betRepository, BettingRepository $bettingRepository, PointsRepository $pointsRepository, UserRepository $userRepository, BettingGroupRepository $bettingGroupRepository, ): Response
    {


        if (!$this->isCsrfTokenValid('end', $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException();
        }

        //get the good bet id
        $bet = $request->request->get('bet');


        $amountAllBets = $bettingRepository->findAmountByEventId($event->getId());


        $thisEvent = $eventRepository->find($event->getId());
        if ($bet){
            $winners = $bettingRepository->findUsersScores($event->getId(), $bet);
            $nbWinners = count($winners);


            $now = new \DateTimeImmutable();


            //pour chaque winner, augmenté son score en se basant sur le montant total des mises et le montant de sa mise et le nombre de gagnants
            foreach ($winners as $winner) {
                $points=$pointsRepository->findOneBy(['idUser' => $winner['id']]);
                $gain = ($amountAllBets / $nbWinners) * ($winner['amount'] / $amountAllBets * $nbWinners);
                $points->setScore($points->getScore() + $gain);
                $pointsRepository->save($points, true);
            }
            $bettingsWon = $bettingRepository->findBy(['idBet'=>$bet]);
            foreach ($bettingsWon as $betting) {
                $betting->setIsWon(true); // Modifier la valeur de la propriété "isWon"
                $bettingRepository->save($betting, true);
            }

            //bettings lost
            $queryBettingsLost = $bettingRepository->createQueryBuilder('b')
                ->innerJoin('b.idBet', 'bet')
                ->where('b.idBet != :idBet')
                ->andWhere('bet.event = :idEvent')
                ->setParameter('idBet', $bet)
                ->setParameter('idEvent', $event->getId())
                ->getQuery()
                ->getResult();

            foreach ($queryBettingsLost as $betting) {
                $betting->setIsWon(false); // Modifier la valeur de la propriété "isWon"
                $bettingRepository->save($betting, true);
            }


            $this->addFlash('succes','les gains ont été ajoutés au score de l\'utilisateur');
            $thisEvent->setFinishAt($now);
            // set result
            $thisEvent->setResult($bet);
            $eventRepository->save($thisEvent, true);
            $this->addFlash('success', 'L\'événement est terminé');
        }else{
            $this->addFlash('error', 'Vous devez d\'abord définir le résultat de l\'événement');
        }



        return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
    }


}
