<?php

namespace App\Controller\Front;

use App\Entity\DailyRecompense;
use App\Form\DailyRecompenseType;
use App\Repository\DailyRecompenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/daily/recompense')]
class DailyRecompenseController extends AbstractController
{
    #[Route('/', name: 'app_daily_recompense_index', methods: ['GET'])]
    public function index(DailyRecompenseRepository $dailyRecompenseRepository): Response
    {
        return $this->render('daily_recompense/index.html.twig', [
            'daily_recompenses' => $dailyRecompenseRepository->findAll(),
        ]);
    }


    /**
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/my', name: 'app_daily_recompense_my_daily_recompense', methods: ['GET'])]
    public function myDailyRecompense(DailyRecompenseRepository $dailyRecompenseRepository): Response
    {
        $hasNotAlreadyRecompenseToday = $dailyRecompenseRepository->getGroupCanReceiveRecompenses($this->getUser());

        return $this->render('daily_recompense/my-recompense.html.twig', [
            'betting_groups' => $hasNotAlreadyRecompenseToday,
        ]);
    }

    #[Route('/new', name: 'app_daily_recompense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DailyRecompenseRepository $dailyRecompenseRepository): Response
    {
        $dailyRecompense = new DailyRecompense();
        $form = $this->createForm(DailyRecompenseType::class, $dailyRecompense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dailyRecompenseRepository->save($dailyRecompense, true);

            return $this->redirectToRoute('app_daily_recompense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('daily_recompense/new.html.twig', [
            'daily_recompense' => $dailyRecompense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_daily_recompense_show', methods: ['GET'])]
    public function show(DailyRecompense $dailyRecompense): Response
    {
        return $this->render('daily_recompense/show.html.twig', [
            'daily_recompense' => $dailyRecompense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_daily_recompense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DailyRecompense $dailyRecompense, DailyRecompenseRepository $dailyRecompenseRepository): Response
    {
        $form = $this->createForm(DailyRecompenseType::class, $dailyRecompense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dailyRecompenseRepository->save($dailyRecompense, true);

            return $this->redirectToRoute('app_daily_recompense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('daily_recompense/edit.html.twig', [
            'daily_recompense' => $dailyRecompense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_daily_recompense_delete', methods: ['POST'])]
    public function delete(Request $request, DailyRecompense $dailyRecompense, DailyRecompenseRepository $dailyRecompenseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dailyRecompense->getId(), $request->request->get('_token'))) {
            $dailyRecompenseRepository->remove($dailyRecompense, true);
        }

        return $this->redirectToRoute('app_daily_recompense_index', [], Response::HTTP_SEE_OTHER);
    }



}
