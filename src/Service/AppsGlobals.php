<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\BettingRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
class AppsGlobals extends \Twig\Extension\AbstractExtension
{

    private $tokenStorage;
    private $bettingRepository;

    public function __construct(TokenStorageInterface $tokenStorage, BettingRepository $bettingRepository)
    {
        $this->bettingRepository = $bettingRepository;
        $this->tokenStorage = $tokenStorage;
    }
    public function events(): \Doctrine\Common\Collections\Collection|array
    {

        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $bettings = $this->bettingRepository->findBy(['idUser' => $user]);
        $event = [];
        foreach ($bettings as $betting) {
            $event[] = $betting->getBet()->getEvent();
        }
        return $event;


    }
}