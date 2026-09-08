<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Repository\AssessmentResultRepository;
use App\State\StudentResultProvider;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(provider: StudentResultProvider::class),
        new Get(security: "object.getAttempt().getStudent() == user || is_granted('ROLE_PSYCHOLOGIST')"),
    ],
    normalizationContext: ['groups' => ['result:read', 'attempt:read']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['attempt.student' => 'exact', 'attempt.quiz.category' => 'exact', 'resultKey' => 'exact'])]
#[ORM\Entity(repositoryClass: AssessmentResultRepository::class)]
#[ORM\Table(name: 'assessment_result')]
class AssessmentResult implements CreatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['result:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'result', targetEntity: Attempt::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Attempt $attempt = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Groups(['result:read', 'attempt:read'])]
    private string $resultKey = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['result:read', 'attempt:read'])]
    private string $label = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['result:read', 'attempt:read'])]
    private string $description = '';

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['result:read', 'attempt:read'])]
    private ?int $score = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['result:read', 'attempt:read'])]
    private array $breakdown = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['result:read'])]
    private ?DateTimeInterface $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttempt(): ?Attempt
    {
        return $this->attempt;
    }

    public function setAttempt(Attempt $attempt): self
    {
        $this->attempt = $attempt;

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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return list<array{label: string, value: int}>
     */
    public function getBreakdown(): array
    {
        return $this->breakdown;
    }

    /**
     * @param list<array{label: string, value: int}> $breakdown
     */
    public function setBreakdown(array $breakdown): self
    {
        $this->breakdown = $breakdown;

        return $this;
    }
}
