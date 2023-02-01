<?php

namespace App\Controller\Front;

use App\Entity\BettingGroup;
use App\Entity\GroupRequest;
use App\Form\BettingGroupType;
use App\Form\JoinBettingGroupType;
use App\Repository\BettingGroupRepository;
use App\Repository\GroupRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/betting/group')]
class BettingGroupController extends AbstractController
{

    #[Route('/', name: 'app_betting_group_index', methods: ['GET'])]
    public function index(BettingGroupRepository $bettingGroupRepository): Response
    {
        return $this->render('betting_group/index.html.twig', [
            'betting_groups' => $bettingGroupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_betting_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BettingGroupRepository $bettingGroupRepository): Response
    {
        $bettingGroup = new BettingGroup();
        $form = $this->createForm(BettingGroupType::class, $bettingGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // add user connected as administrator
            $bettingGroup->addAdministrator($this->getUser());

            //add user as member
            $bettingGroup->addMember($this->getUser());

            $bettingGroupRepository->save($bettingGroup, true);

            return $this->redirectToRoute('front_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('betting_group/new.html.twig', [
            'betting_group' => $bettingGroup,
            'form' => $form,
        ]);
    }

    #[Route('/my-bettings-groups', name: 'app_betting_group_test', methods: ['GET', 'POST'])]
    public function getMyBettingGroups(BettingGroupRepository $bettingGroupRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $bettingGroups = $user->getBettingGroups();


        return $this->render('betting_group/_my_betting_groups.html.twig', [
            'betting_groups' => $bettingGroups,
        ]);
    }

    #[Route('/{id}', name: 'app_betting_group_show', methods: ['GET'])]
    public function show(BettingGroup $bettingGroup): Response
    {
        return $this->render('betting_group/show.html.twig', [
            'betting_group' => $bettingGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_betting_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BettingGroup $bettingGroup, BettingGroupRepository $bettingGroupRepository): Response
    {
        $form = $this->createForm(BettingGroupType::class, $bettingGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bettingGroupRepository->save($bettingGroup, true);

            return $this->redirectToRoute('front_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('betting_group/edit.html.twig', [
            'betting_group' => $bettingGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_betting_group_delete', methods: ['POST'])]
    public function delete(Request $request, BettingGroup $bettingGroup, BettingGroupRepository $bettingGroupRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bettingGroup->getId(), $request->request->get('_token'))) {
            $bettingGroupRepository->remove($bettingGroup, true);
        }

        return $this->redirectToRoute('front_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/join', name: 'app_betting_group_join', methods: ['GET', 'POST'])]
    public function join(Request $request, BettingGroup $bettingGroup, GroupRequestRepository $groupRequestRepository): Response
    {

        $form = $this->createForm(JoinBettingGroupType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            //check if the code is the same as the one in the database
            if($form->get('code')->getData() === $bettingGroup->getCode()){
                //add user as member

                //verifiy if the user is already a member
                if($bettingGroup->getMembers()->contains($this->getUser())){
                    $this->addFlash('error', 'You are already a member of this group');
                    return $this->redirectToRoute('front_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
                }

                $groupRequest = new GroupRequest();
                $groupRequest->setUser($this->getUser());
                $groupRequest->setBettingGroup($bettingGroup);
                $groupRequest->setIsApproved(false);
                $groupRequest->setCreatedAt(new \DateTimeImmutable());


                $groupRequestRepository->save($groupRequest, true);

                $this->addFlash('success', 'You have joined the group');
            }
            else{
                $this->addFlash('error', 'The code is not correct');
            }

            return $this->redirectToRoute('front_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('betting_group/join.html.twig', [
            'betting_group' => $bettingGroup,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/group-requests', name: 'app_betting_group_group_requests', methods: ['GET'])]
    public function getGroupRequests(BettingGroup $bettingGroup): Response
    {
        $groupRequests = $bettingGroup->getGroupRequests();

        if(!$bettingGroup->getAdministrators()->contains($this->getUser())){
            $this->addFlash('error', 'You are not an administrator of this group');
            return $this->redirectToRoute('front_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
        }

        if(!$groupRequests){
            $this->addFlash('error', 'No requests for this group');
        }

        return $this->render('group_request/index.html.twig', [
            'group_requests' => $groupRequests,
        ]);
    }







}
