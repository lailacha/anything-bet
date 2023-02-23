<?php

namespace App\Entity;

use App\Repository\BetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetRepository::class)]
class Bet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;


    #[ORM\ManyToOne(targetEntity: Event::class, cascade: ['persist'], inversedBy: 'bets')]
    private Event $event;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }


    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setIdEvent(?Event $idEvent): self
    {
        $this->event = $idEvent;

        return $this;
    }

    public function setEvent(Event $param): void
    {
        $this->event = $param;
    }

}
