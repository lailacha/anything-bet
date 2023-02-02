<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\AvatarType;
use App\Form\UserUpdateType;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile')]
    public function index(): Response
    {
        $currentUser = $this->getUser();
        $user = new User();
        $formAvatar = $this->createForm(AvatarType::class, [
            'action' => $this->generateUrl('front_change_avatar'),
        ]);

        $formUpdate = $this->createForm(UserUpdateType::class, $user, [
            'action' => $this->generateUrl('front_update_profile'),
        ]);


        $formUpdate->get('email')->setData($currentUser->getEmail());
        $formUpdate->get('firstname')->setData($currentUser->getFirstName());
        $formUpdate->get('lastname')->setData($currentUser->getLastName());
        $formUpdate->get('pseudo')->setData($currentUser->getPseudo());

        return $this->render('front/profile/index.html.twig', [
            'user' => $currentUser,
            'formAvatar' => $formAvatar->createView(),
            'formUpdate' => $formUpdate->createView()
        ]);
    }

    #[Route('/avatar', name: "change_avatar", methods: ['POST'])]
    public function avatar(Request $request, UserRepository $userRepository){
        $user = new User();
        $form = $this->createForm(AvatarType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $tUser = $this->getUser();

            $image = $form['avatar']->getData();

            $imageName = uniqid().'.'.$image->guessExtension();

            $image->move(
                $this->getParameter('avatar'),
                $imageName
            );

            $tUser->setAvatar($imageName);

            $userRepository->save($tUser, true);
        }

        return $this->redirectToRoute("front_profile");
    }

    #[Route('/update', name: "update_profile", methods: ['POST'])]
    public function updateProfile(Request $request, UserRepository $userRepository){
        $user = new User();
        $form = $this->createForm(UserUpdateType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $tUser = $this->getUser();
            foreach($data as $key => $val){
                $get = "get" . ucfirst($key);
                if($tUser->$get() != $val){
                    $set = "set" . ucfirst($key);
                    $tUser->$set($val);
                }
            }
            $userRepository->save($tUser, true);
        }

        return $this->redirectToRoute("front_profile");
    }
}
