<?php

namespace App\Entity;

use App\Repository\PerformanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerformanceRepository::class)]
#[ORM\Table(name: 'performances')]
#[ORM\Index(name: 'idx_performances_user_date', columns: ['user_id', 'date_performed'])]
#[ORM\Index(name: 'idx_performances_exercise', columns: ['exercise_id'])]
#[ORM\Index(name: 'idx_performances_discipline', columns: ['discipline_id'])]
class Performance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'performances')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Exercise::class, inversedBy: 'performances')]
    #[ORM\JoinColumn(name: 'exercise_id', referencedColumnName: 'id', nullable: false)]
    private ?Exercise $exercise = null;

    #[ORM\ManyToOne(targetEntity: Discipline::class, inversedBy: 'performances')]
    #[ORM\JoinColumn(name: 'discipline_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Discipline $discipline = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datePerformed = null;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['default' => 'classic'])]
    private string $entryType = 'classic';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['default' => 'manual'])]
    private string $source = 'manual';

    #[ORM\Column(type: Types::STRING, length: 20, options: ['default' => 'private'])]
    private string $privacyLevel = 'private';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $entryTypeOld = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $privacyLevelOld = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $sourceOld = null;

    #[ORM\OneToMany(mappedBy: 'performance', targetEntity: PerformanceSet::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['setIndex' => 'ASC'])]
    private Collection $performanceSets;

    public function __construct()
    {
        $this->performanceSets = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDatePerformed(): ?\DateTimeInterface
    {
        return $this->datePerformed;
    }

    public function setDatePerformed(\DateTimeInterface $datePerformed): static
    {
        $this->datePerformed = $datePerformed;
        return $this;
    }

    public function getEntryType(): string
    {
        return $this->entryType;
    }

    public function setEntryType(string $entryType): static
    {
        $this->entryType = $entryType;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;
        return $this;
    }

    public function getPrivacyLevel(): string
    {
        return $this->privacyLevel;
    }

    public function setPrivacyLevel(string $privacyLevel): static
    {
        $this->privacyLevel = $privacyLevel;
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

    public function getEntryTypeOld(): ?string
    {
        return $this->entryTypeOld;
    }

    public function setEntryTypeOld(?string $entryTypeOld): static
    {
        $this->entryTypeOld = $entryTypeOld;
        return $this;
    }

    public function getPrivacyLevelOld(): ?string
    {
        return $this->privacyLevelOld;
    }

    public function setPrivacyLevelOld(?string $privacyLevelOld): static
    {
        $this->privacyLevelOld = $privacyLevelOld;
        return $this;
    }

    public function getSourceOld(): ?string
    {
        return $this->sourceOld;
    }

    public function setSourceOld(?string $sourceOld): static
    {
        $this->sourceOld = $sourceOld;
        return $this;
    }

    /**
     * @return Collection<int, PerformanceSet>
     */
    public function getPerformanceSets(): Collection
    {
        return $this->performanceSets;
    }

    public function addPerformanceSet(PerformanceSet $performanceSet): static
    {
        if (!$this->performanceSets->contains($performanceSet)) {
            $this->performanceSets->add($performanceSet);
            $performanceSet->setPerformance($this);
        }
        return $this;
    }

    public function removePerformanceSet(PerformanceSet $performanceSet): static
    {
        if ($this->performanceSets->removeElement($performanceSet)) {
            if ($performanceSet->getPerformance() === $this) {
                $performanceSet->setPerformance(null);
            }
        }
        return $this;
    }
}