<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Component\Assessment\Dto\SaveAnswersInput;
use App\Component\Assessment\Dto\StartAttemptInput;
use App\Controller\AttemptStartAction;
use App\Controller\AttemptSubmitAction;
use App\Controller\AttemptSaveAnswersAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Enum\AttemptStatus;
use App\Enum\StudyLanguage;
use App\Repository\AttemptRepository;
use App\State\StudentAttemptProvider;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(provider: StudentAttemptProvider::class),
        new Get(security: "object.getStudent() == user || is_granted('ROLE_PSYCHOLOGIST')"),
        new Post(
            uriTemplate: 'attempts/start',
            controller: AttemptStartAction::class,
            input: StartAttemptInput::class,
            name: 'startAttempt',
        ),
        new Post(
            uriTemplate: 'attempts/{id}/answers',
            controller: AttemptSaveAnswersAction::class,
            input: SaveAnswersInput::class,
            read: false,
            name: 'saveAnswers',
        ),
        new Post(
            uriTemplate: 'attempts/{id}/submit',
            controller: AttemptSubmitAction::class,
            input: false,
            name: 'submitAttempt',
        ),
    ],
    normalizationContext: ['groups' => ['attempt:read']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['student' => 'exact', 'quiz' => 'exact', 'status' => 'exact', 'quiz.category' => 'exact'])]
#[ORM\Entity(repositoryClass: AttemptRepository::class)]
#[ORM\UniqueConstraint(fields: ['student', 'quiz'])]
class Attempt implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;
    use UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['attempt:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attempt:read'])]
    private ?User $student = null;

    #[ORM\ManyToOne(targetEntity: Assignment::class)]
    #[Groups(['attempt:read'])]
    private ?Assignment $assignment = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attempt:read'])]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: Types::STRING, length: 16, enumType: AttemptStatus::class)]
    #[Groups(['attempt:read'])]
    private AttemptStatus $status = AttemptStatus::InProgress;

    #[ORM\OneToMany(mappedBy: 'attempt', targetEntity: AttemptAnswer::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['attempt:read:full'])]
    private Collection $answers;

    #[ORM\OneToOne(mappedBy: 'attempt', targetEntity: AssessmentResult::class, cascade: ['persist', 'remove'])]
    #[Groups(['attempt:read'])]
    private ?AssessmentResult $result = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['attempt:read'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['attempt:read'])]
    private ?DateTimeInterface $submittedAt = null;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(User $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getAssignment(): ?Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(?Assignment $assignment): self
    {
        $this->assignment = $assignment;

        return $this;
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

    public function getStatus(): AttemptStatus
    {
        return $this->status;
    }

    public function setStatus(AttemptStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, AttemptAnswer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(AttemptAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setAttempt($this);
        }

        return $this;
    }

    public function clearAnswers(): self
    {
        $this->answers->clear();

        return $this;
    }

    public function getResult(): ?AssessmentResult
    {
        return $this->result;
    }

    public function setResult(?AssessmentResult $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getSubmittedAt(): ?DateTimeInterface
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(?DateTimeInterface $submittedAt): self
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

    #[Groups(['attempt:read'])]
    public function getStudyLanguage(): StudyLanguage
    {
        return $this->quiz?->getStudyLanguage() ?? StudyLanguage::Uzbek;
    }

    public function getIsSubmitted(): bool
    {
        return $this->status->isFinished();
    }
}
