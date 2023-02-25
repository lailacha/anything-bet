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
    private string $name = "";

    #[ORM\Column]
    private ?int $userMax = null;


    #[ORM\Column(length: 128, nullable: true)]
    private ?string $cover = 'default-cover.svg';


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

    #[ORM\OneToMany(mappedBy: 'bettingGroup', targetEntity: GroupRequest::class)]
    private $groupRequests = null;

    #[ORM\OneToMany(mappedBy: 'bettingGroup', targetEntity: Event::class)]
    private $events = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;


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

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getGroupRequests()
    {
        return $this->groupRequests;
    }

    public function removeMember(User $user)
    {
        $this->members->removeElement($user);
    }


    public function getEvents()
    {
        return $this->events;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }


    public function __toString(){
        return $this->name;
    }
}
