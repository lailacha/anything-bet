<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\AvatarType;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
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

        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }
}
