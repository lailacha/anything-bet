<?php

namespace App\Controller\Back;

use App\Entity\BettingGroup;
use App\Entity\GroupRequest;
use App\Form\BettingGroupType;
use App\Form\JoinBettingGroupType;
use App\Form\SearchBettingGroupType;
use App\Repository\BettingGroupRepository;
use App\Repository\DailyRecompenseRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\UserRepository;
use App\Security\Voter\GroupAdminVoter;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/betting-group')]
class BettingGroupController extends AbstractController
{

    #[Route('/', name: 'app_betting_group_index', methods: ['GET', 'POST'])]
    public function index(BettingGroupRepository $bettingGroupRepository, Request $request, PaginatorInterface $paginator): Response
    {


        $searchForm = $this->createForm(SearchBettingGroupType::class);

        $searchForm->handleRequest($request);


        if($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $query = $bettingGroupRepository->search($data);
        } else {
            $query = $bettingGroupRepository->createQueryBuilder('u')
                ->orderBy('u.id', 'DESC')
                ->getQuery();
        }

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('back/betting_group/index.html.twig', [
            'betting_groups' =>$pagination ,
            'searchForm' => $searchForm->createView(),
        ]);
    }



    #[Route('/{id}', name: 'app_betting_group_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(BettingGroup $bettingGroup): Response
    {

        $members = $bettingGroup->getMembers();
        $events = $bettingGroup->getEvents();
        return $this->render('back/betting_group/show.html.twig', [
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
                    return $this->redirectToRoute('back_app_betting_group_edit', ['id' => $bettingGroup->getId()], Response::HTTP_SEE_OTHER);
                }
            }

            $bettingGroupRepository->save($bettingGroup, true);

            return $this->redirectToRoute('back_app_betting_group_show', ['id' => $bettingGroup->getId()], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('back_app_betting_group_index', [], Response::HTTP_SEE_OTHER);
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
        return $this->redirectToRoute('back_app_betting_group_members', ['id' => $bettingGroup->getId()], Response::HTTP_SEE_OTHER);
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