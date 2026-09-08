<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Enum\StudyLanguage;
use App\Repository\StudyGroupRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['study-group:read']],
    denormalizationContext: ['groups' => ['study-group:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial', 'faculty' => 'exact', 'studyLanguage' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['name', 'createdAt'])]
#[ORM\Entity(repositoryClass: StudyGroupRepository::class)]
class StudyGroup implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;
    use UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['study-group:read', 'user:read', 'attempt:read', 'passport:read:staff'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Faculty::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['study-group:read', 'study-group:write', 'user:read'])]
    private ?Faculty $faculty = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Assert\NotBlank]
    #[Groups(['study-group:read', 'study-group:write', 'user:read', 'attempt:read', 'passport:read:staff'])]
    private string $name = '';

    #[ORM\Column(type: Types::STRING, enumType: StudyLanguage::class)]
    #[Groups(['study-group:read', 'study-group:write'])]
    private StudyLanguage $studyLanguage = StudyLanguage::Uzbek;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    #[Groups(['study-group:read', 'study-group:write'])]
    private ?string $externalId = null;

    #[ORM\OneToMany(mappedBy: 'studyGroup', targetEntity: User::class)]
    private Collection $students;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['study-group:read'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    public function setFaculty(Faculty $faculty): self
    {
        $this->faculty = $faculty;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStudyLanguage(): StudyLanguage
    {
        return $this->studyLanguage;
    }

    public function setStudyLanguage(StudyLanguage $studyLanguage): self
    {
        $this->studyLanguage = $studyLanguage;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    #[Groups(['study-group:read'])]
    public function getStudentCount(): int
    {
        return $this->students->count();
    }
}
