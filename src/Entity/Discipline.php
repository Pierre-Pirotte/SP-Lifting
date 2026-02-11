<?php

namespace App\Entity;

use App\Repository\DisciplineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisciplineRepository::class)]
#[ORM\Table(name: 'disciplines')]
class Discipline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::STRING, length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'discipline', targetEntity: ExerciseDiscipline::class, cascade: ['persist', 'remove'])]
    private Collection $exerciseDisciplines;

    #[ORM\OneToMany(mappedBy: 'discipline', targetEntity: Performance::class)]
    private Collection $performances;

    #[ORM\OneToMany(mappedBy: 'discipline', targetEntity: Program::class)]
    private Collection $programs;

    #[ORM\OneToMany(mappedBy: 'discipline', targetEntity: UserDiscipline::class, cascade: ['persist', 'remove'])]
    private Collection $userDisciplines;

    public function __construct()
    {
        $this->exerciseDisciplines = new ArrayCollection();
        $this->performances = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->userDisciplines = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, ExerciseDiscipline>
     */
    public function getExerciseDisciplines(): Collection
    {
        return $this->exerciseDisciplines;
    }

    public function addExerciseDiscipline(ExerciseDiscipline $exerciseDiscipline): static
    {
        if (!$this->exerciseDisciplines->contains($exerciseDiscipline)) {
            $this->exerciseDisciplines->add($exerciseDiscipline);
            $exerciseDiscipline->setDiscipline($this);
        }
        return $this;
    }

    public function removeExerciseDiscipline(ExerciseDiscipline $exerciseDiscipline): static
    {
        if ($this->exerciseDisciplines->removeElement($exerciseDiscipline)) {
            if ($exerciseDiscipline->getDiscipline() === $this) {
                $exerciseDiscipline->setDiscipline(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Performance>
     */
    public function getPerformances(): Collection
    {
        return $this->performances;
    }

    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    /**
     * @return Collection<int, UserDiscipline>
     */
    public function getUserDisciplines(): Collection
    {
        return $this->userDisciplines;
    }
}