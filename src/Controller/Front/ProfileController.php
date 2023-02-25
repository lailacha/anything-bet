<?php

namespace App\Controller\Front;

use App\Service\FileUploader;
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
        $formAvatar = $this->createForm(AvatarType::class, $user, [
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
        $user = $this->getUser();
        $form = $this->createForm(AvatarType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('avatar')->getData();

            $fileUploader = new FileUploader($this->getParameter('avatar'));
            $newFilename = $fileUploader->upload($image);
            if($newFilename) {
                $user->setAvatar($newFilename);

            } else {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
            }

            $userRepository->save($user, true);
            $this->addFlash("success", "Vous avez bien mis à jour votre profile !");
        }else
        {
            foreach ($form->getErrors(true) as $error) {
                $erreur = $error->getMessage();
            }
            $this->addFlash("error", $erreur);
        }

        return $this->redirectToRoute("front_profile");
    }

    #[Route('/update', name: "update_profile", methods: ['POST'])]
    public function updateProfile(Request $request, UserRepository $userRepository){
        $user = new User();
        $form = $this->createForm(UserUpdateType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
          $user = $this->getUser();
            $user->setFirstName($form->get('firstname')->getData());
            $user->setLastName($form->get('lastname')->getData());
            $user->setPseudo($form->get('pseudo')->getData());

            $userRepository->save($user, true);
            $this->addFlash("success", "Votre profile a bien été mis à jour !");
        }else{

            //retrieve error message
            foreach ($form->getErrors(true) as $error) {
                $error = $error->getMessage();
            }
            if(isset($error)){
                $this->addFlash("error", $error);
            }
        }

        return $this->redirectToRoute("front_profile");
    }
}
