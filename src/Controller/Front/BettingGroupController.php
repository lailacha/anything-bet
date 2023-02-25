<?php

namespace App\Controller\Front;

use App\Entity\BettingGroup;
use App\Entity\GroupRequest;
use App\Entity\Points;
use App\Form\BettingGroupType;
use App\Form\JoinBettingGroupType;
use App\Form\PointsType;
use App\Repository\BettingGroupRepository;
use App\Repository\DailyRecompenseRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\PointsRepository;
use App\Repository\UserRepository;
use App\Security\Voter\GroupAdminVoter;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/betting-group')]
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
    public function new(Request $request, BettingGroupRepository $bettingGroupRepository, PointsRepository $pointsRepository): Response
    {
        $bettingGroup = new BettingGroup();
        $form = $this->createForm(BettingGroupType::class, $bettingGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // add user connected as administrator
            $bettingGroup->addAdministrator($this->getUser());

            //add user as member
            $bettingGroup->addMember($this->getUser());

            $coverImage = $form->get('cover')->getData();

            if ($coverImage) {
                $fileUploader = new FileUploader($this->getParameter('logo_image_directory'));
                $newFilename = $fileUploader->upload($coverImage);
                if($newFilename) {
                    $bettingGroup->setCover($newFilename);
                } else {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $bettingGroupRepository->save($bettingGroup, true);

            $points = new Points();
            $points->setUser($this->getUser());
            $points->setBettingGroup($bettingGroup);
            $points->setScore(0);

            $pointsRepository->save($points, true);

            return $this->redirectToRoute('front_app_betting_group_by_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('betting_group/new.html.twig', [
            'betting_group' => $bettingGroup,
            'form' => $form,
        ]);
    }

    #[Route('/my-bettings-groups', name: 'app_betting_group_by_user', methods: ['GET', 'POST'])]
    public function getMyBettingGroups(DailyRecompenseRepository $dailyRecompenseRepository, BettingGroupRepository $bettingGroupRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $hasNotAlreadyRecompenseToday = $dailyRecompenseRepository->getGroupCanReceiveRecompenses($this->getUser());

       $scores = $bettingGroupRepository->getGroupsByUserWithScore($this->getUser());

        // get ids of groups has not already recompense today
        $ids = [];
        foreach($hasNotAlreadyRecompenseToday as $group) {
            $ids[] = $group['id'];
        }


        $pagination = $paginator->paginate(
            $scores,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('betting_group/_my_betting_groups.html.twig', [
            'betting_groups' => $pagination,
            'hasNotAlreadyRecompenseToday' => $ids
       ]);
    }

    #[Route('/my-bettings-groups-admin', name: 'app_betting_group_by_user_admin', methods: ['GET', 'POST'])]
    public function getMyBettingGroupsAdmin(Request $request, PaginatorInterface $paginator ): Response
    {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $bettingGroups = $user->getBettingAdminGroups();


        $pagination = $paginator->paginate(
            $bettingGroups,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('betting_group/_my_betting_groups.html.twig', [
            'betting_groups' => $pagination,
        ]);
    }

    #[Route('/{id}', name: 'app_betting_group_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(BettingGroup $bettingGroup): Response
    {

        $members = $bettingGroup->getMembers();
        $events = $bettingGroup->getEvents();
        return $this->render('betting_group/show.html.twig', [
            'betting_group' => $bettingGroup,
            'members' => $members,
            'events' => $events,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_betting_group_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, BettingGroup $bettingGroup, BettingGroupRepository $bettingGroupRepository): Response
    {
        $form = $this->createForm(BettingGroupType::class, $bettingGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $coverImage = $form->get('cover')->getData();

            if ($coverImage) {
                $fileUploader = new FileUploader($this->getParameter('logo_image_directory'));
                $newFilename = $fileUploader->upload($coverImage);
                if($newFilename) {
                    $bettingGroup->setCover($newFilename);
                } else {
                    $this->addFlash('danger',$newFilename );
                    return $this->redirectToRoute('front_app_betting_group_edit', ['id' => $bettingGroup->getId()], Response::HTTP_SEE_OTHER);
                }
            }

            $bettingGroupRepository->save($bettingGroup, true);

            return $this->redirectToRoute('front_app_betting_group_show', ['id' => $bettingGroup->getId()], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('front_app', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{slug}/join', name: 'app_betting_group_join', methods: ['GET', 'POST'])]
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

                $this->addFlash('success', 'Votre demande a bien été envoyée');
            } else {
                $this->addFlash('error', 'Le code est incorrect');
            }

            return $this->redirectToRoute('front_app_betting_group_by_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('betting_group/join.html.twig', [
            'betting_group' => $bettingGroup,
            'form' => $form,
        ]);
    }


    #[Route('/group-requests/{id}', name: 'app_betting_group_group_requests', methods: ['GET'])]
    public function getGroupRequests(BettingGroup $bettingGroup, Request $request, PaginatorInterface $paginator): Response
    {

        $this->denyAccessUnlessGranted(GroupAdminVoter::EDIT, $bettingGroup);

        $groupRequests = $bettingGroup->getGroupRequests();

        if ($groupRequests) {
            $groupRequests = $groupRequests->filter(function ($groupRequest) {
                return $groupRequest->getIsApproved() === false;
            });

            $groupRequests = $paginator->paginate(
                $groupRequests,
                $request->query->getInt('page', 1),
                10
            );

            return $this->render('group_request/index.html.twig', [
                'group_requests' => $groupRequests,
            ]);

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
    public function getMembers(BettingGroup $bettingGroup, Request $request, PaginatorInterface $paginator): Response
    {

        $members = $bettingGroup->getMembers();

        $this->denyAccessUnlessGranted(GroupAdminVoter::SHOW, $bettingGroup);

        $members = $paginator->paginate(
            $members,
            $request->query->getInt('page', 1),
            10
        );


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
                return $this->redirectToRoute('front_app_betting_group_members', ['id' => $bettingGroup->getId()]);
            }

            if ($bettingGroup->getAdministrators()->contains($user)) {
                $this->addFlash('error', 'You cannot delete an administrator of the group');
                return $this->redirectToRoute('front_app_betting_group_members', ['id' => $bettingGroup->getId()]);

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


    /**
     * @throws \JsonException
     */
    #[Route('/{id}/join-link', name: 'app_betting_group_join_link', methods: ['GET', 'POST'])]
    public function getJoinLink(BettingGroup $bettingGroup): Response
    {

        if(!$bettingGroup)
        {
            return new Response('Betting group not found', Response::HTTP_NOT_FOUND);
        }

       $url = 'https://localhost/betting-group/' . $bettingGroup->getSlug() . '/join';

        $code = $bettingGroup->getCode();

         $json = json_encode(array('url' => $url, 'code' => $code), JSON_THROW_ON_ERROR);

         if($json === false) {
             return new Response('Error encoding json', Response::HTTP_INTERNAL_SERVER_ERROR);
         }

       return new Response($json, Response::HTTP_OK);

    }
}