<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Repository\AssignmentRepository;
use App\State\StudentAssignmentProvider;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(provider: StudentAssignmentProvider::class),
        new Get(),
        new Post(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Patch(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Delete(security: "is_granted('ROLE_PSYCHOLOGIST')"),
    ],
    normalizationContext: ['groups' => ['assignment:read']],
    denormalizationContext: ['groups' => ['assignment:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact', 'studyGroup' => 'exact', 'isActive' => 'exact'])]
#[ORM\Entity(repositoryClass: AssignmentRepository::class)]
class Assignment implements CreatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['assignment:read', 'attempt:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['assignment:read', 'assignment:write', 'attempt:read'])]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: StudyGroup::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['assignment:read', 'assignment:write'])]
    private ?StudyGroup $studyGroup = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['assignment:read', 'assignment:write'])]
    private DateTime $startAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['assignment:read', 'assignment:write'])]
    private ?DateTimeInterface $endAt = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['assignment:read', 'assignment:write'])]
    private bool $isActive = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->startAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getStudyGroup(): ?StudyGroup
    {
        return $this->studyGroup;
    }

    public function setStudyGroup(StudyGroup $studyGroup): self
    {
        $this->studyGroup = $studyGroup;

        return $this;
    }

    public function getStartAt(): DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(DateTime $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isOpenAt(DateTimeInterface $moment): bool
    {
        if ($this->isActive === false) {
            return false;
        }

        if ($moment < $this->startAt) {
            return false;
        }

        return $this->endAt === null || $moment <= $this->endAt;
    }
}
