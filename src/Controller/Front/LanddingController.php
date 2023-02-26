<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanddingController extends AbstractController
{
    #[Route('/accueil', name: 'app_landding')]
    public function index(): Response
    {
        return $this->render('front/landding/index.html.twig', [
            'controller_name' => 'LanddingController',
        ]);
    }
    #[Route('/a_propos', name: 'app_a_propos')]
    public function aboutUs(): Response
    {
        return $this->render('front/landding/about_us.html.twig', [
            'controller_name' => 'LanddingController',
        ]);
    }
    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('front/landding/contact.html.twig', [
            'controller_name' => 'LanddingController',
        ]);
    }
    #[Route('/mention', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('front/landding/legal.html.twig', [
            'controller_name' => 'LanddingController',
        ]);
    }
    #[Route('/reglement', name: 'app_rule')]
    public function rules(): Response
    {
        return $this->render('front/landding/rule.html.twig', [
            'controller_name' => 'LanddingController',
        ]);
    }
}
