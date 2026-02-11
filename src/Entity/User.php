<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $passwordHash = null;

    #[ORM\Column(type: Types::STRING, length: 120, nullable: true)]
    private ?string $displayName = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $roles = [];

    #[ORM\Column(type: Types::STRING, length: 1, options: ['default' => 'X'])]
    private string $sex = 'X';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    private ?string $weightKg = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $heightCm = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $sexOld = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Performance::class, cascade: ['remove'])]
    private Collection $performances;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Program::class)]
    private Collection $programs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserDiscipline::class, cascade: ['persist', 'remove'])]
    private Collection $userDisciplines;

    public function __construct()
    {
        $this->performances = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->userDisciplines = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): static
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): static
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(?array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSex(): string
    {
        return $this->sex;
    }

    public function setSex(string $sex): static
    {
        $this->sex = $sex;
        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;
        return $this;
    }

    public function getWeightKg(): ?string
    {
        return $this->weightKg;
    }

    public function setWeightKg(?string $weightKg): static
    {
        $this->weightKg = $weightKg;
        return $this;
    }

    public function getHeightCm(): ?int
    {
        return $this->heightCm;
    }

    public function setHeightCm(?int $heightCm): static
    {
        $this->heightCm = $heightCm;
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

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getSexOld(): ?string
    {
        return $this->sexOld;
    }

    public function setSexOld(?string $sexOld): static
    {
        $this->sexOld = $sexOld;
        return $this;
    }

    /**
     * @return Collection<int, Performance>
     */
    public function getPerformances(): Collection
    {
        return $this->performances;
    }

    public function addPerformance(Performance $performance): static
    {
        if (!$this->performances->contains($performance)) {
            $this->performances->add($performance);
            $performance->setUser($this);
        }
        return $this;
    }

    public function removePerformance(Performance $performance): static
    {
        if ($this->performances->removeElement($performance)) {
            if ($performance->getUser() === $this) {
                $performance->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): static
    {
        if (!$this->programs->contains($program)) {
            $this->programs->add($program);
            $program->setUser($this);
        }
        return $this;
    }

    public function removeProgram(Program $program): static
    {
        if ($this->programs->removeElement($program)) {
            if ($program->getUser() === $this) {
                $program->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, UserDiscipline>
     */
    public function getUserDisciplines(): Collection
    {
        return $this->userDisciplines;
    }

    public function addUserDiscipline(UserDiscipline $userDiscipline): static
    {
        if (!$this->userDisciplines->contains($userDiscipline)) {
            $this->userDisciplines->add($userDiscipline);
            $userDiscipline->setUser($this);
        }
        return $this;
    }

    public function removeUserDiscipline(UserDiscipline $userDiscipline): static
    {
        if ($this->userDisciplines->removeElement($userDiscipline)) {
            if ($userDiscipline->getUser() === $this) {
                $userDiscipline->setUser(null);
            }
        }
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez des données temporaires sensibles, nettoyez-les ici
    }
}