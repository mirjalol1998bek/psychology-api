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
        new Delete(security: "is_granted('ROLE_ADMIN')"),
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

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: AnswerOption::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
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

    public function hasOption(AnswerOption $option): bool
    {
        return $this->options->contains($option);
    }
}
