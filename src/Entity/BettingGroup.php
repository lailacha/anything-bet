<?php

namespace App\Entity;

use App\Repository\BettingGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BettingGroupRepository::class)]
class BettingGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $userMax = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "bettingAdminGroups")]
    #[ORM\JoinTable(name: 'betting_group_administrators')]
    private $administrators = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "bettingGroups")]
    #[ORM\JoinTable(name: 'betting_group_members')]
    private $members = null;

    // create a unique code for each group
    #[ORM\Column(type: 'string', length: 23, unique: true)]
    private ?string $code = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->administrators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
        $this->code = uniqid('', true);
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

    public function getUserMax(): ?int
    {
        return $this->userMax;
    }

    public function setUserMax(int $userMax): self
    {
        $this->userMax = $userMax;

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

    public function getAdministrators()
    {
        return $this->administrators;
    }

    public function addAdministrator($administrator): self
    {
        $this->administrators[] = $administrator;

        return $this;
    }

    public function getMembers()
    {
        return $this->members;
    }

    public function addMember($member): self
    {
        $this->members[] = $member;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}