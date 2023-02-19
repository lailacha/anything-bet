<?php

namespace App\Controller\Front;

use App\Entity\BettingGroup;
use App\Entity\Bet;
use App\Entity\Event;
use App\Form\BetType;
use App\Form\EventType;
use App\Repository\BetRepository;
use App\Repository\EventRepository;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event')]
class EventController extends AbstractController
{


    #[Route('/my-groups', name: 'app_event_my_groups', methods: ['GET'])]
    public function myGroups(EventRepository $eventRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $query = $eventRepository->createQueryBuilder('e')
            ->where('e.bettingGroup IN (:groups)')
            ->setParameter('groups', $this->getUser()->getBettingGroups())
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

        $query = $eventRepository->createQueryBuilder('e')
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

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventRepository $eventRepository, BetRepository $betRepository, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->get('startAt')->setData(new \DateTimeImmutable());
        $form->get('finishAt')->setData(new \DateTimeImmutable('+1 day'));

        $form->handleRequest($request);


        if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Le formulaire n\'est pas valide');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                if ($event->getFinishAt() < $event->getStartAt()) {
                    $this->addFlash('danger', 'La date de fin doit être supérieure à la date de début');
                } else {

                    $event->setCreatedAt(new \DateTimeImmutable());
                    $event->setTheUser($this->getUser());
                    $event->setName($form->get('name')->getData());
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

                    // persist bets
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
                return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
            }
            //return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository, BetRepository $betRepository): Response
    {
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
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $eventRepository->remove($event, true);
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

}
