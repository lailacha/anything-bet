<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            if($this->getUser()->getResetToken()){
                return $this->redirectToRoute('front_default_index');
            }else{
                $this->addFlash("error", 'Votre email n\'a pas été vérifié.');
            }
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgotten-password', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGeneratorInterface, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByEmail($form->get("email")->getData());
            if($user == null){
                $this->addFlash("error", 'Une erreure est survenu');
                return $this->render('security/forgotten_password.html.twig',['form' => $form->createView()]);
            }
            $token = $tokenGeneratorInterface->generateToken();
            $user->setResetToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            $url = $this->generateUrl('app_change_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('blablagirl76@gmail.com', 'AnythingBet'))
                    ->to($user->getEmail())
                    ->subject('Modification de mot de passe')
                    ->htmlTemplate('security/change_password.html.twig')
                    ->context([
                        'url' => $url
                    ])
            );
            $this->addFlash("success", 'Vous allez recevoir un mail !');
            return $this->render('security/forgotten_password.html.twig',['form' => $form->createView()]);
        }

        return $this->render('security/forgotten_password.html.twig',['form' => $form->createView()]);
    }

    #[Route(path: '/change-password/{token}', name: 'app_change_password')]
    public function changePasswd(Request $request, string $token, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->findOneByResetToken($token);
        if($user == null){
            $this->addFlash("error", 'Jeton invalide');
            return $this->redirectToRoute("app_login");
        }
        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setResetToken('');
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get("password")->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", 'Votre mot de passe a bien été modifié !');
            return $this->redirectToRoute("app_login");
        }else{
            //retrieve error message
            foreach ($form->getErrors(true) as $error) {
                $error = $error->getMessage();
            }
            if(isset($error)){
                $this->addFlash("error", $error);
            }
        }
        return $this->render('security/password_reset.html.twig', ["form" => $form->createView()]);
    }
}
