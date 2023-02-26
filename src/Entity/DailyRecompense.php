<?php

namespace App\Entity;

use App\Repository\DailyRecompenseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DailyRecompenseRepository::class)]
class DailyRecompense
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;


    #[ORM\ManyToOne(targetEntity: BettingGroup::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private bettingGroup $bettingGroup;

    #[ORM\Column]
    private \DateTimeImmutable $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $idUser): self
    {
        $this->user = $idUser;

        return $this;
    }


    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }


    public function getBettingGroup(): ?BettingGroup
    {
        return $this->bettingGroup;
    }

    public function setBettingGroup(BettingGroup $bettingGroup): self
    {
        $this->bettingGroup = $bettingGroup;

        return $this;
    }

}
