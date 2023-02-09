<?php

namespace App\Entity;

use App\Repository\PointsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointsRepository::class)]
class Points
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\ManyToOne]
    private ?User $idUser = null;

    #[ORM\ManyToOne]
    private ?BettingGroup $idBettingGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
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

    public function getIdBettingGroup(): ?BettingGroup
    {
        return $this->idBettingGroup;
    }

    public function setIdBettingGroup(?BettingGroup $idBettingGroup): self
    {
        $this->idBettingGroup = $idBettingGroup;

        return $this;
    }
}
