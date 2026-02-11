<?php

namespace App\Entity;

use App\Repository\ExerciseDisciplineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseDisciplineRepository::class)]
#[ORM\Table(name: 'exercise_disciplines')]
class ExerciseDiscipline
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Exercise::class, inversedBy: 'exerciseDisciplines')]
    #[ORM\JoinColumn(name: 'exercise_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Exercise $exercise = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Discipline::class, inversedBy: 'exerciseDisciplines')]
    #[ORM\JoinColumn(name: 'discipline_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Discipline $discipline = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $metadata = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): static
    {
        $this->exercise = $exercise;
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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): static
    {
        $this->metadata = $metadata;
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