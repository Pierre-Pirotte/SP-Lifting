<?php

namespace App\Entity;

use App\Repository\PerformanceSetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerformanceSetRepository::class)]
#[ORM\Table(name: 'performance_sets')]
#[ORM\UniqueConstraint(name: 'ux_performance_set_index', columns: ['performance_id', 'set_index'])]
class PerformanceSet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Performance::class, inversedBy: 'performanceSets')]
    #[ORM\JoinColumn(name: 'performance_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Performance $performance = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $setIndex = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $reps = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    private ?string $weight = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $rpe = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $restSeconds = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isWarmup = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerformance(): ?Performance
    {
        return $this->performance;
    }

    public function setPerformance(?Performance $performance): static
    {
        $this->performance = $performance;
        return $this;
    }

    public function getSetIndex(): ?int
    {
        return $this->setIndex;
    }

    public function setSetIndex(int $setIndex): static
    {
        $this->setIndex = $setIndex;
        return $this;
    }

    public function getReps(): ?int
    {
        return $this->reps;
    }

    public function setReps(?int $reps): static
    {
        $this->reps = $reps;
        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): static
    {
        $this->weight = $weight;
        return $this;
    }

    public function getRpe(): ?int
    {
        return $this->rpe;
    }

    public function setRpe(?int $rpe): static
    {
        $this->rpe = $rpe;
        return $this;
    }

    public function getRestSeconds(): ?int
    {
        return $this->restSeconds;
    }

    public function setRestSeconds(?int $restSeconds): static
    {
        $this->restSeconds = $restSeconds;
        return $this;
    }

    public function isWarmup(): bool
    {
        return $this->isWarmup;
    }

    public function setIsWarmup(bool $isWarmup): static
    {
        $this->isWarmup = $isWarmup;
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