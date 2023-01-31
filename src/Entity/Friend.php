<?php

namespace App\Entity;

use App\Repository\FriendRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendRepository::class)]
class Friend
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $idUser = null;

    #[ORM\ManyToOne]
    private ?User $idUserTwo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdUserTwo(): ?User
    {
        return $this->idUserTwo;
    }

    public function setIdUserTwo(?User $idUserTwo): self
    {
        $this->idUserTwo = $idUserTwo;

        return $this;
    }
}
