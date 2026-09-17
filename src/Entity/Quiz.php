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
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Enum\StudyLanguage;
use App\Repository\QuizRepository;
use App\State\QuizDeleteProcessor;
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
        new Get(normalizationContext: ['groups' => ['quiz:read', 'quiz:read:full']]),
        new Post(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Patch(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Delete(security: "is_granted('ROLE_ADMIN')", processor: QuizDeleteProcessor::class),
    ],
    normalizationContext: ['groups' => ['quiz:read']],
    denormalizationContext: ['groups' => ['quiz:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact', 'studyLanguage' => 'exact', 'isActive' => 'exact'])]
#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;
    use UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['quiz:read', 'attempt:read', 'assignment:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['quiz:read', 'quiz:write', 'attempt:read'])]
    private ?Category $category = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Groups(['quiz:read', 'quiz:write', 'attempt:read', 'assignment:read'])]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['quiz:read', 'quiz:write', 'attempt:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 8, enumType: StudyLanguage::class)]
    #[Groups(['quiz:read', 'quiz:write', 'attempt:read'])]
    private StudyLanguage $studyLanguage = StudyLanguage::Uzbek;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    #[Groups(['quiz:read', 'quiz:write', 'attempt:read'])]
    private int $timeLimitMinutes = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['quiz:read', 'quiz:write'])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Question::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Groups(['quiz:read:full'])]
    private Collection $questions;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getStudyLanguage(): StudyLanguage
    {
        return $this->studyLanguage;
    }

    public function setStudyLanguage(StudyLanguage $studyLanguage): self
    {
        $this->studyLanguage = $studyLanguage;

        return $this;
    }

    public function getTimeLimitMinutes(): int
    {
        return $this->timeLimitMinutes;
    }

    public function setTimeLimitMinutes(int $timeLimitMinutes): self
    {
        $this->timeLimitMinutes = $timeLimitMinutes;

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
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    #[Groups(['quiz:read', 'attempt:read'])]
    public function getQuestionCount(): int
    {
        return $this->questions->count();
    }
}
