<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\BettingRepository;
use App\Repository\NotificationRepository;
use App\Repository\PointsRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
class AppsGlobals extends \Twig\Extension\AbstractExtension
{

    private $tokenStorage;
    private $bettingRepository;
    private $notificationRepository;
    private $pointsRepository;

    public function __construct(TokenStorageInterface $tokenStorage, BettingRepository $bettingRepository, NotificationRepository $notificationRepository, PointsRepository $pointsRepository)
    {
        $this->bettingRepository = $bettingRepository;
        $this->tokenStorage = $tokenStorage;
        $this->notificationRepository = $notificationRepository;
        $this->pointsRepository = $pointsRepository;
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

    public function notifications():  Collection|array
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        $notifications = $this->notificationRepository->findBy(['user_id' => $user], ['created_at' => 'DESC']);
        return $notifications;
    }

    public function points(): int
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        $points = $this->pointsRepository->findOneBy(['idUser' => $user]);

        if($points === null){
            return 0;
        }

        return $points->getScore();
    }

}