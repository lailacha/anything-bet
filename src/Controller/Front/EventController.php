<?php

namespace App\Controller\Front;

use App\Entity\BettingGroup;
use App\Entity\Bet;
use App\Entity\Betting;
use App\Entity\Event;
use App\Form\BetType;
use App\Form\EventType;
use App\Repository\BetRepository;
use App\Repository\BettingRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event')]
class EventController extends AbstractController
{

    #[Route('/group/{id}', name: 'app_event_group', methods: ['GET'])]
    public function getByGroup(BettingGroup $bettingGroup): Response
    {
        return $this->render('event/group.html.twig', [
            'events' => $bettingGroup->getEvents(),
            'bettingGroup' => $bettingGroup,
            ]);
    }

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventRepository $eventRepository, BetRepository $betRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->get('startAt')->setData(new \DateTimeImmutable());
        $form->get('finishAt')->setData(new \DateTimeImmutable( '+1 day' ));
        $form->handleRequest($request);

        $bets = [];
        for ($i = 0; $i < 5; $i++) {
            $bet = new Bet();
            $formBet = $this->createForm(BetType::class, $bet);
            $formBet->handleRequest($request);
            $bets[] = [
                'bet' => $bet,
                'formBet' => $formBet->createView(),
            ];
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if ($event->getFinishAt() < $event->getStartAt()) {
                $this->addFlash('danger', 'La date de fin doit être supérieure à la date de début');
                return $this->redirectToRoute('front_app_event_new');
            }else{
                $event->setCreatedAt(new \DateTimeImmutable());
                $event->setTheUser($this->getUser());
                $eventRepository->save($event, true);
                $betObjects = [];
                foreach ($bets as $betData) {
                    $formBet = $this->createForm(BetType::class, $betData['bet']);
                    $formBet->handleRequest($request);

                    if ($formBet->isSubmitted() && $formBet->isValid()) {
                        $bet = $betData['bet'];
                        $bet->setIdEvent($event);
                        $betObjects[] = $bet; // Ajouter l'objet Bet créé dans le tableau
                    }
                }

                // Enregistrer chaque objet Bet dans la base de données
                foreach ($betObjects as $bet) {
                    $betRepository->save($bet, true);
                }


                return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'bets' => $bets,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event, BetRepository $betRepository): Response
    {
        $bets = $betRepository->findBy(['idEvent' => $event->getId()]);

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'bets' => $bets,
        ]);
    }

    //mybets
    #[Route('/mybets', name: 'app_event_test', methods: ['GET'])]
    public function mybets(BettingRepository $bettingRepository): Response
    {
        dd('haha');

        return $this->render('event/mybets.html.twig', [
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/bet/submit', name: 'app_event_bet', methods: ['POST'])]
    public function submitBet(Request $request, BetRepository $betRepository, BettingRepository $bettingRepository): Response
    {
        //l'ulisateur connecté
        $user = $this->getUser();

        //le choix de l'utilisateur
        $bet = $betRepository->find($request->request->get('bet'));

        //creation betting
        $betting = new Betting();
        $betting->setIdUser($user);
        $betting->setIdBet($bet);

        //sauvegarde betting
        $bettingRepository->save($betting, true);


        return $this->redirectToRoute('front_app_event_index', [], Response::HTTP_SEE_OTHER);
    }

}
