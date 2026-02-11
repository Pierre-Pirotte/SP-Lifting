<?php

namespace App\Entity;

use App\Repository\ExerciseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
#[ORM\Table(name: 'exercises')]
class Exercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 150, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::STRING, length: 200)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['default' => 'accessory'])]
    private string $category = 'accessory';

    #[ORM\Column(type: Types::STRING, length: 20, options: ['default' => 'kg'])]
    private string $defaultUnit = 'kg';

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $defaultRepsRange = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $defaultUnitOld = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    private ?string $categoryOld = null;

    #[ORM\OneToMany(mappedBy: 'exercise', targetEntity: ExerciseDiscipline::class, cascade: ['persist', 'remove'])]
    private Collection $exerciseDisciplines;

    #[ORM\OneToMany(mappedBy: 'exercise', targetEntity: Performance::class)]
    private Collection $performances;

    public function __construct()
    {
        $this->exerciseDisciplines = new ArrayCollection();
        $this->performances = new ArrayCollection();
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

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getDefaultUnit(): string
    {
        return $this->defaultUnit;
    }

    public function setDefaultUnit(string $defaultUnit): static
    {
        $this->defaultUnit = $defaultUnit;
        return $this;
    }

    public function getDefaultRepsRange(): ?string
    {
        return $this->defaultRepsRange;
    }

    public function setDefaultRepsRange(?string $defaultRepsRange): static
    {
        $this->defaultRepsRange = $defaultRepsRange;
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

    public function getDefaultUnitOld(): ?string
    {
        return $this->defaultUnitOld;
    }

    public function setDefaultUnitOld(?string $defaultUnitOld): static
    {
        $this->defaultUnitOld = $defaultUnitOld;
        return $this;
    }

    public function getCategoryOld(): ?string
    {
        return $this->categoryOld;
    }

    public function setCategoryOld(?string $categoryOld): static
    {
        $this->categoryOld = $categoryOld;
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
            $exerciseDiscipline->setExercise($this);
        }
        return $this;
    }

    public function removeExerciseDiscipline(ExerciseDiscipline $exerciseDiscipline): static
    {
        if ($this->exerciseDisciplines->removeElement($exerciseDiscipline)) {
            if ($exerciseDiscipline->getExercise() === $this) {
                $exerciseDiscipline->setExercise(null);
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
}