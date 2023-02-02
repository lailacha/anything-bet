<?php

namespace App\Controller\Front;

use App\Entity\BettingGroup;
use App\Entity\GroupRequest;
use App\Form\BettingGroupType;
use App\Form\JoinBettingGroupType;
use App\Repository\BettingGroupRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\UserRepository;
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
        if ($this->isCsrfTokenValid('delete' . $bettingGroup->getId(), $request->request->get('_token'))) {
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
            if ($form->get('code')->getData() === $bettingGroup->getCode()) {
                //add user as member

                //verifiy if the user is already a member
                if ($bettingGroup->getMembers()->contains($this->getUser())) {
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
            } else {
                $this->addFlash('error', 'The code is not correct');
            }

            return $this->redirectToRoute('front_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('betting_group/join.html.twig', [
            'betting_group' => $bettingGroup,
            'form' => $form,
        ]);
    }


    #[Route('/group-requests/{id}', name: 'app_betting_group_group_requests', methods: ['GET'])]
    public function getGroupRequests(BettingGroup $bettingGroup): Response
    {

        $groupRequests = $bettingGroup->getGroupRequests();

        if ($groupRequests) {
            $groupRequests = $groupRequests->filter(function ($groupRequest) {
                return $groupRequest->getIsApproved() === false;
            });

            if (!$bettingGroup->getAdministrators()->contains($this->getUser())) {
                $this->addFlash('error', 'You are not an administrator of this group');
                return $this->redirectToRoute('front_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
            }

            if (!$groupRequests) {
                $this->addFlash('error', 'No requests for this group');
            }

            return $this->render('group_request/index.html.twig', [
                'group_requests' => $groupRequests,
            ]);
        }

    }


    #[Route('/members/{id}', name: 'app_betting_group_members', methods: ['GET'])]
    public function getMembers(BettingGroup $bettingGroup): Response
    {

        $members = $bettingGroup->getMembers();


        if ($members) {
            return $this->render('betting_group/members.html.twig', [
                'members' => $members,
                'bettingGroup' => $bettingGroup,
            ]);
        }


    }


    #[Route('/delete/members/{id}', name: 'app_betting_group_delete_member', methods: ['POST'])]
    public function deleteMember(BettingGroup $bettingGroup, Request $request, UserRepository $userRepository)
    {

        if ($this->isCsrfTokenValid('delete' . $bettingGroup->getId(), $request->request->get('_token'))) {

            $user = $userRepository->find($request->request->get('user_id'));

            if (!$user) {
                $this->addFlash('error', 'User not found');
                return new Response('User not found', Response::HTTP_NOT_FOUND);
            }

            if (!$bettingGroup->getAdministrators()->contains($this->getUser())) {
                $this->addFlash('error', 'You are not an administrator of this group');
                return new Response('You are not an administrator of this group', Response::HTTP_UNAUTHORIZED);

            }

            if ($bettingGroup->getAdministrators()->contains($user)) {
                $this->addFlash('error', 'You cannot delete an administrator of the group');
                return new Response('You cannot delete an administrator of the group', Response::HTTP_UNAUTHORIZED);


            }

            $bettingGroup->removeMember($user);
            $userRepository->save($user, true);

        }

        $this->addFlash('success', 'User deleted');
        return $this->redirectToRoute('front_app_betting_group_members', ['id' => $bettingGroup->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/bets/{id}', name: 'app_betting_group_events', methods: ['GET'])]
    public function getEventsByGroup(bettingGroup $bettingGroup, BettingGroupRepository $bettingGroupRepository): Response
    {

      $events = $bettingGroup->getEvents();

        if ($events) {
            return $this->render('betting_group/events.html.twig', [
                'events' => $events,
                'bettingGroup' => $bettingGroup,
            ]);
        }

    }
}