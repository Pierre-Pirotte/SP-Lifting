<?php

namespace App\Entity;

use App\Repository\UserDisciplineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDisciplineRepository::class)]
#[ORM\Table(name: 'user_disciplines')]
class UserDiscipline
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userDisciplines')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Discipline::class, inversedBy: 'userDisciplines')]
    #[ORM\JoinColumn(name: 'discipline_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Discipline $discipline = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $preferred = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(?Discipline $discipline): static
    {
        $this->discipline = $discipline;
        return $this;
    }

    public function isPreferred(): bool
    {
        return $this->preferred;
    }

    public function setPreferred(bool $preferred): static
    {
        $this->preferred = $preferred;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}