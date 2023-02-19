<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
class AppsGlobals extends \Twig\Extension\AbstractExtension
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    public function groups(): \Doctrine\Common\Collections\Collection|array
    {

        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();


        return $user instanceof User ? $user->getBets() : [];
    }
}