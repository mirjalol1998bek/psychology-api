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
use App\Enum\InstrumentType;
use App\Repository\CategoryRepository;
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
        new Post(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Patch(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['category:read']],
    denormalizationContext: ['groups' => ['category:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['instrumentType' => 'exact', 'isActive' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['position', 'name'])]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;
    use UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['category:read', 'quiz:read', 'assignment:read', 'attempt:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 128)]
    #[Assert\NotBlank]
    #[Groups(['category:read', 'category:write', 'quiz:read', 'assignment:read', 'attempt:read'])]
    private string $name = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['category:read', 'category:write'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 32, enumType: InstrumentType::class)]
    #[Groups(['category:read', 'category:write', 'quiz:read', 'assignment:read', 'attempt:read'])]
    private InstrumentType $instrumentType = InstrumentType::ScoreScale;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    #[Groups(['category:read', 'category:write'])]
    private int $position = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['category:read', 'category:write'])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Quiz::class)]
    private Collection $quizzes;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: ScoreRange::class, cascade: ['persist', 'remove'])]
    private Collection $scoreRanges;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: AssessmentInterpretation::class, cascade: ['persist', 'remove'])]
    private Collection $interpretations;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->scoreRanges = new ArrayCollection();
        $this->interpretations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getInstrumentType(): InstrumentType
    {
        return $this->instrumentType;
    }

    public function setInstrumentType(InstrumentType $instrumentType): self
    {
        $this->instrumentType = $instrumentType;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

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

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    /**
     * @return Collection<int, ScoreRange>
     */
    public function getScoreRanges(): Collection
    {
        return $this->scoreRanges;
    }

    public function addScoreRange(ScoreRange $scoreRange): self
    {
        if (!$this->scoreRanges->contains($scoreRange)) {
            $this->scoreRanges->add($scoreRange);
            $scoreRange->setCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, AssessmentInterpretation>
     */
    public function getInterpretations(): Collection
    {
        return $this->interpretations;
    }

    public function addInterpretation(AssessmentInterpretation $interpretation): self
    {
        if (!$this->interpretations->contains($interpretation)) {
            $this->interpretations->add($interpretation);
            $interpretation->setCategory($this);
        }

        return $this;
    }
}
