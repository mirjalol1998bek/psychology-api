<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    normalizationContext: ['groups' => ['question:read', 'quiz:read:full']],
    denormalizationContext: ['groups' => ['question:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['quiz' => 'exact'])]
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['question:read', 'quiz:read:full', 'attempt-answer:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['question:write'])]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: Types::STRING, length: 16, enumType: QuestionType::class)]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private QuestionType $type = QuestionType::SingleChoice;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['question:read', 'quiz:read:full', 'attempt-answer:read', 'question:write'])]
    private string $text = '';

    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private int $position = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private bool $isReversed = false;

    /** `SCORE_SCALE` metodikada subshkala nomi (masalan "Akademik moslashuv") — bo'sh = faqat umumiy ballga kiradi. */
    #[ORM\Column(type: Types::STRING, length: 128, options: ['default' => ''])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private string $subscaleKey = '';

    /** Umumiy ballga qo'shiladigan ishora (+1/-1) — masalan OKM-20'da
     * "Ichki motivatsiya indeksi" = (A+B) − (C+D): A/B savollari +1,
     * C/D savollari −1. Subshkalaning o'z ballini (5-25) o'zgartirmaydi,
     * faqat UMUMIY ko'rsatkichga qanday qo'shilishini belgilaydi. */
    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 1])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private int $overallSign = 1;

    /** Subshkala qaysi ball oralig'i/talqin jadvalidan foydalanadi.
     * Standart `'*'` — barcha subshkalalar bitta umumiy (nomiga bog'liq
     * bo'lmagan) jadvalni ishlatadi (IPM-20, OKM-20: hammasi 5-25).
     * Subshkalalar TENG BO'LMAGAN o'lchamda bo'lsa (masalan EHS-20: A/B
     * 7-35, C 6-30) — har guruh o'z maxsus kalitini oladi (masalan
     * `'anxiety_fatigue'`, `'resilience'`), shu kalit bilan alohida
     * `ScoreRange`/`AssessmentInterpretation` yaratiladi. */
    #[ORM\Column(type: Types::STRING, length: 32, options: ['default' => '*'])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private string $subscaleRangeKey = '*';

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: AnswerOption::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    #[ApiProperty(writableLink: true)]
    private Collection $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getType(): QuestionType
    {
        return $this->type;
    }

    public function setType(QuestionType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

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

    public function getIsReversed(): bool
    {
        return $this->isReversed;
    }

    public function setIsReversed(bool $isReversed): self
    {
        $this->isReversed = $isReversed;

        return $this;
    }

    public function getSubscaleKey(): string
    {
        return $this->subscaleKey;
    }

    public function setSubscaleKey(string $subscaleKey): self
    {
        $this->subscaleKey = $subscaleKey;

        return $this;
    }

    public function getOverallSign(): int
    {
        return $this->overallSign;
    }

    public function setOverallSign(int $overallSign): self
    {
        $this->overallSign = $overallSign;

        return $this;
    }

    public function getSubscaleRangeKey(): string
    {
        return $this->subscaleRangeKey;
    }

    public function setSubscaleRangeKey(string $subscaleRangeKey): self
    {
        $this->subscaleRangeKey = $subscaleRangeKey;

        return $this;
    }

    /**
     * @return Collection<int, AnswerOption>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(AnswerOption $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setQuestion($this);
        }

        return $this;
    }

    public function removeOption(AnswerOption $option): self
    {
        $this->options->removeElement($option);

        return $this;
    }

    public function hasOption(AnswerOption $option): bool
    {
        return $this->options->contains($option);
    }
}
