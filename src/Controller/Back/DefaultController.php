<?php

namespace App\Controller\Back;


use App\Repository\UserRepository;
use App\Repository\BetRepository;
use App\Repository\EventRepository;
use App\Repository\PointsRepository;
use Knp\Component\Pager\PaginatorInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_index')]
    public function index(UserRepository $userRepository, BetRepository $betRepository, PaginatorInterface $paginator, EventRepository $eventRepository, PointsRepository $pointsRepository ,Request $request): Response
    {   

        // get the count of users
        $users = $userRepository->count([]);

        // get the count of bets
        $bets = $betRepository->count([]);

        //get the count of events
        $events = $eventRepository->count([]);

        // get the count of points
        $points = $pointsRepository->count([]);
        
        $query = $userRepository->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );



        return $this->render('back/default/index.html.twig', 
        [
            'users' => $users,
            'bets' => $bets,
            'userss' => $pagination,
            'events' => $events,
            'points' => $points
        ]);
    }
}
