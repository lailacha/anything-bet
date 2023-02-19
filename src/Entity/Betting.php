<?php

namespace App\Entity;

use App\Repository\BettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BettingRepository::class)]
class Betting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $idUser = null;

    #[ORM\ManyToOne]
    private ?Bet $idBet = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isWon = null;

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

    public function getIdBet(): ?Bet
    {
        return $this->idBet;
    }

    public function setIdBet(?Bet $idBet): self
    {
        $this->idBet = $idBet;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function isIsWon(): ?bool
    {
        return $this->isWon;
    }

    public function setIsWon(?bool $isWon): self
    {
        $this->isWon = $isWon;

        return $this;
    }

}
