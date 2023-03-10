<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $finishAt = null;

    #[ORM\ManyToOne(targetEntity: BettingGroup::class, inversedBy: 'events')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?BettingGroup $bettingGroup = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $result = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Participate::class)]
    private Collection $participates;

    #[ORM\ManyToOne]
    private ?User $theUser = null;


    //multiple bets for one event
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Bet::class, cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[ORM\JoinTable(name: 'event_bets')]
    private $bets;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $coverImage = null;

    public function __construct()
    {
        $this->participates = new ArrayCollection();
        $this->bets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFinishAt(): ?\DateTimeImmutable
    {
        return $this->finishAt;
    }

    public function setFinishAt(\DateTimeImmutable $finishAt): self
    {
        $this->finishAt = $finishAt;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return Collection<int, Participate>
     */
    public function getParticipates(): Collection
    {
        return $this->participates;
    }

    public function addParticipate(Participate $participate): self
    {
        if (!$this->participates->contains($participate)) {
            $this->participates->add($participate);
            $participate->setEvent($this);
        }

        return $this;
    }

    public function removeParticipate(Participate $participate): self
    {
        if ($this->participates->removeElement($participate)) {
            // set the owning side to null (unless already changed)
            if ($participate->getEvent() === $this) {
                $participate->setEvent(null);
            }
        }

        return $this;
    }

    public function getTheUser(): ?User
    {
        return $this->theUser;
    }

    public function setTheUser(?User $theUser): self
    {
        $this->theUser = $theUser;

        return $this;
    }


    public function getBettingGroup(): ?BettingGroup
    {
        return $this->bettingGroup;
    }

    public function setBettingGroup($bettingGroup): self
    {
        $this->bettingGroup = $bettingGroup;
        return $this;
    }

    /**
     * @return Collection<int, Bet>
     */
    public function getBets(): Collection
    {
        return $this->bets;
    }

    public function addBet($bet): void
    {

        if($this->bets->contains($bet)){
            return;
        }

        $this->bets[] = $bet;
    }

    public function setBets($bets): void
    {
        $this->bets = $bets;
    }

    public function removeBet(Bet $bet): self
    {
        if ($this->bets->removeElement($bet)) {
            // set the owning side to null (unless already changed)
            if ($bet->getEvent() === $this) {
                $bet->setEvent(null);
            }
        }

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
