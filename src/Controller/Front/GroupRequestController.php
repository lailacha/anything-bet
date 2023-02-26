<?php

namespace App\Controller\Front;

use \Mailjet\Resources;

use App\Entity\BettingGroup;
use App\Entity\GroupRequest;
use App\Entity\Points;
use App\Form\GroupRequestType;
use App\Repository\BettingGroupRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\PointsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/group/request')]
class GroupRequestController extends AbstractController
{
    #[Route('/', name: 'app_group_request_index', methods: ['GET'])]
    public function index(MailerInterface $mailer, GroupRequestRepository $groupRequestRepository, Request $request, PaginatorInterface $paginator): Response
    {


        $groupRequests = $groupRequestRepository->findBy(['isApproved' => false]);
        $groupRequests = $paginator->paginate(
            $groupRequests,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('group_request/index.html.twig', [
            'group_requests' => $groupRequests,
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

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/{id}/accept', name: 'app_group_request_approve', methods: ['POST'])]
    public function approve(MailerInterface $mailer,Request $request, GroupRequest $groupRequest, GroupRequestRepository $groupRequestRepository, BettingGroupRepository $bettingGroupRepository, PointsRepository $pointsRepository): Response
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
            // create points for user
            $bettingGroupRepository->save($group, true);
            $usersPoints = new Points();
            $usersPoints->setUser($groupRequest->getUser());
            $usersPoints->setBettingGroup($group);
            $usersPoints->setScore(0);
            $pointsRepository->save($usersPoints, true);



            $mj = new \Mailjet\Client($_ENV['MAILJET_APIKEY'],$_ENV['MAILJET_SECRET_KEY'], true, ['version' => 'v3.1']);
            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => "laila.charaoui@outlook.fr",
                            'Name' => "Registration is valid"
                        ],
                        'To' => [
                            [
                                'Email' => "blablagirl76@gmail.com",
                                'Name' => "passenger 1"
                            ]
                        ],
                        'TemplateLanguage' => true,
                        'TemplateID' => 4612337,
                        'Subject' => "Bienvenue sur Antything bet",
                        'Variables' => json_decode('{
                    "confirmation_link" : "http://localhost:8000/confirm/1"
                    }', true, 512, JSON_THROW_ON_ERROR)

                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);
            $response->success() && var_dump($response->getData());

            return $this->redirectToRoute('front_app_group_request_index', [], Response::HTTP_SEE_OTHER);
        }

}
