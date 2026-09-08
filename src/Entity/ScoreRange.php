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
use App\Enum\StudyLanguage;
use App\Repository\ScoreRangeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Patch(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Delete(security: "is_granted('ROLE_PSYCHOLOGIST')"),
    ],
    normalizationContext: ['groups' => ['score-range:read']],
    denormalizationContext: ['groups' => ['score-range:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact'])]
#[ORM\Entity(repositoryClass: ScoreRangeRepository::class)]
#[ORM\Table(name: 'score_range')]
class ScoreRange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['category:read', 'score-range:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'scoreRanges')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['score-range:read', 'score-range:write'])]
    private ?Category $category = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['category:read', 'category:write', 'score-range:read', 'score-range:write'])]
    private int $minScore = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['category:read', 'category:write', 'score-range:read', 'score-range:write'])]
    private int $maxScore = 0;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Groups(['category:read', 'category:write', 'score-range:read', 'score-range:write'])]
    private string $resultKey = '';

    #[ORM\Column(type: Types::STRING, length: 8, nullable: true, enumType: StudyLanguage::class)]
    #[Groups(['category:read', 'category:write', 'score-range:read', 'score-range:write'])]
    private ?StudyLanguage $studyLanguage = null;

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

    public function getMinScore(): int
    {
        return $this->minScore;
    }

    public function setMinScore(int $minScore): self
    {
        $this->minScore = $minScore;

        return $this;
    }

    public function getMaxScore(): int
    {
        return $this->maxScore;
    }

    public function setMaxScore(int $maxScore): self
    {
        $this->maxScore = $maxScore;

        return $this;
    }

    public function getResultKey(): string
    {
        return $this->resultKey;
    }

    public function setResultKey(string $resultKey): self
    {
        $this->resultKey = $resultKey;

        return $this;
    }

    public function getStudyLanguage(): ?StudyLanguage
    {
        return $this->studyLanguage;
    }

    public function setStudyLanguage(?StudyLanguage $studyLanguage): self
    {
        $this->studyLanguage = $studyLanguage;

        return $this;
    }

    public function containsScore(int $score): bool
    {
        return $score >= $this->minScore && $score <= $this->maxScore;
    }
}
