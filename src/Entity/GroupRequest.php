<?php

namespace App\Entity;

use App\Repository\GroupRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRequestRepository::class)]
class GroupRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isApproved = null;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'groupRequests')]
    private $user = null;

    #[ORM\ManyToOne(targetEntity: BettingGroup::class, inversedBy: 'groupRequests')]
    private $bettingGroup = null;

    /**
     * @return null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param null $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    public function getBettingGroup(): ?BettingGroup
    {
        return $this->bettingGroup;
    }

    public function setBettingGroup(?BettingGroup $bettingGroup): self
    {
        $this->bettingGroup = $bettingGroup;

        return $this;
    }
}
