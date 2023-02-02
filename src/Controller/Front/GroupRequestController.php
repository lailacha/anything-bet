<?php

namespace App\Controller\Front;

use App\Entity\BettingGroup;
use App\Entity\GroupRequest;
use App\Form\GroupRequestType;
use App\Repository\BettingGroupRepository;
use App\Repository\GroupRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/group/request')]
class GroupRequestController extends AbstractController
{
    #[Route('/', name: 'app_group_request_index', methods: ['GET'])]
    public function index(GroupRequestRepository $groupRequestRepository): Response
    {
        return $this->render('group_request/index.html.twig', [
            'group_requests' => $groupRequestRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_group_request_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GroupRequestRepository $groupRequestRepository): Response
    {
        $groupRequest = new GroupRequest();
        $form = $this->createForm(GroupRequestType::class, $groupRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupRequestRepository->save($groupRequest, true);

            return $this->redirectToRoute('app_group_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group_request/new.html.twig', [
            'group_request' => $groupRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_request_show', methods: ['GET'])]
    public function show(GroupRequest $groupRequest): Response
    {
        return $this->render('group_request/show.html.twig', [
            'group_request' => $groupRequest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_group_request_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupRequest $groupRequest, GroupRequestRepository $groupRequestRepository): Response
    {
        $form = $this->createForm(GroupRequestType::class, $groupRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupRequestRepository->save($groupRequest, true);

            return $this->redirectToRoute('app_group_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group_request/edit.html.twig', [
            'group_request' => $groupRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_request_delete', methods: ['POST'])]
    public function delete(Request $request, GroupRequest $groupRequest, GroupRequestRepository $groupRequestRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupRequest->getId(), $request->request->get('_token'))) {
            $groupRequestRepository->remove($groupRequest, true);
        }

        return $this->redirectToRoute('front_app_group_request_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/accept', name: 'app_group_request_approve', methods: ['POST'])]
    public function approve(Request $request, GroupRequest $groupRequest, GroupRequestRepository $groupRequestRepository, BettingGroupRepository $bettingGroupRepository): Response
        {

            if (!$this->isCsrfTokenValid('approve'.$groupRequest->getId(), $request->request->get('_token'))) {
                $this->addFlash('error', 'Invalid token');
                return $this->redirectToRoute('front_app_group_request_index', [], Response::HTTP_SEE_OTHER);
            }

            $groupRequest->setIsApproved(true);
            $groupRequestRepository->save($groupRequest, true);

            //add user to group
            $group = $groupRequest->getGroup();

            if(!$group instanceof BettingGroup){
               $this->addFlash('error', 'Group not found');
                return $this->redirectToRoute('front_app_group_request_index', [], Response::HTTP_SEE_OTHER);
            }

            $group->addMember($groupRequest->getUser());
            $bettingGroupRepository->save($group, true);

            return $this->redirectToRoute('front_app_group_request_index', [], Response::HTTP_SEE_OTHER);
        }

}