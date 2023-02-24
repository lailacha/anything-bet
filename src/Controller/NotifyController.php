<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotifyController extends AbstractController
{
    #[Route('/{notyf}', name: 'app_notify')]
    public function index(?string $notyf): Response
    {
        return $this->render('notify/index.html.twig', [
           'notyf' => $notyf
        ]);
    }
}
