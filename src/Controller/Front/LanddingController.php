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
}
