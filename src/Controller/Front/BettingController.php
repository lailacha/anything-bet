<?php

namespace App\Controller\Front;
use App\Entity\Betting;
use App\Form\BettingType;
use App\Repository\BettingRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/betting')]
class BettingController extends AbstractController
{
    #[Route('/', name: 'app_betting_index', methods: ['GET'])]
    public function index(BettingRepository $bettingRepository): Response
    {
        return $this->render('betting/index.html.twig', [
            'bettings' => $bettingRepository->findAll(),
        ]);
    }

    #[Route('/my-bettings', name: 'app_betting_my_bettings', methods: ['GET'])]
    public function myBettings(Request $request, BettingRepository $bettingRepository, PaginatorInterface $paginator): Response
    {
        $bettings = $bettingRepository->findBy(['idUser' => $this->getUser()]);
        $events = [];
        foreach ($bettings as $betting) {
            $events[] = $betting->getBet()->getEvent();
        }

        $pagination = $paginator->paginate(
            $bettings,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('betting/index.html.twig', [
            'bettings' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_betting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BettingRepository $bettingRepository): Response
    {
        $betting = new Betting();
        $form = $this->createForm(BettingType::class, $betting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bettingRepository->save($betting, true);

            return $this->redirectToRoute('app_betting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('betting/new.html.twig', [
            'betting' => $betting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_betting_show', methods: ['GET'])]
    public function show(Betting $betting): Response
    {
        return $this->render('betting/show.html.twig', [
            'betting' => $betting,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_betting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Betting $betting, BettingRepository $bettingRepository): Response
    {
        $form = $this->createForm(BettingType::class, $betting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bettingRepository->save($betting, true);

            return $this->redirectToRoute('app_betting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('betting/edit.html.twig', [
            'betting' => $betting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_betting_delete', methods: ['POST'])]
    public function delete(Request $request, Betting $betting, BettingRepository $bettingRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$betting->getId(), $request->request->get('_token'))) {
            $bettingRepository->remove($betting, true);
        }

        return $this->redirectToRoute('app_betting_index', [], Response::HTTP_SEE_OTHER);
    }
}
