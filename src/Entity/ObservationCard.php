<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Enum\ObservationRiskLevel;
use App\Repository\ObservationCardRepository;
use App\State\ObservationCardCollectionProvider;
use App\State\ObservationCardCreateProcessor;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 10-metodika: kurator (tyutor) o'z guruhi talabasi haqida to'ldiradigan
 * kuzatuv kartasi (ekspert bahosi). Attempt/Quiz emas — filler (tyutor) va
 * subject (talaba) turli shaxs, hozircha har talaba uchun bir marta.
 * Ko'rish: to'ldirgan tyutor yoki psixolog/admin — talaba o'zi ko'rmaydi.
 */
#[ApiResource(
    operations: [
        new GetCollection(provider: ObservationCardCollectionProvider::class),
        new Get(security: "object.getTutor() == user || is_granted('ROLE_PSYCHOLOGIST')"),
        new Post(
            security: "is_granted('ROLE_TUTOR')",
            processor: ObservationCardCreateProcessor::class,
            denormalizationContext: ['groups' => ['observation-card:create']],
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['observation-card:read']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['student' => 'exact', 'student.studyGroup' => 'exact', 'riskLevel' => 'exact'])]
#[ORM\Entity(repositoryClass: ObservationCardRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_OBSERVATION_CARD_STUDENT', fields: ['student'])]
class ObservationCard implements CreatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['observation-card:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['observation-card:read', 'observation-card:create'])]
    private ?User $student = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['observation-card:read'])]
    private ?User $tutor = null;

    /**
     * @var array<string, int> ko'rsatkich kaliti → ball (0-3), `ObservationCardData::INDICATORS`
     */
    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotNull]
    #[Groups(['observation-card:read', 'observation-card:create'])]
    private array $scores = [];

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['observation-card:read'])]
    private int $totalScore = 0;

    #[ORM\Column(type: Types::STRING, enumType: ObservationRiskLevel::class)]
    #[Groups(['observation-card:read'])]
    private ObservationRiskLevel $riskLevel = ObservationRiskLevel::None;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['observation-card:read'])]
    private bool $alertTriggered = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['observation-card:read'])]
    private ?DateTimeInterface $createdAt = null;

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

    public function getTutor(): ?User
    {
        return $this->tutor;
    }

    public function setTutor(User $tutor): self
    {
        $this->tutor = $tutor;

        return $this;
    }

    /**
     * @return array<string, int>
     */
    public function getScores(): array
    {
        return $this->scores;
    }

    /**
     * @param array<string, int> $scores
     */
    public function setScores(array $scores): self
    {
        $this->scores = $scores;

        return $this;
    }

    public function getTotalScore(): int
    {
        return $this->totalScore;
    }

    public function setTotalScore(int $totalScore): self
    {
        $this->totalScore = $totalScore;

        return $this;
    }

    public function getRiskLevel(): ObservationRiskLevel
    {
        return $this->riskLevel;
    }

    public function setRiskLevel(ObservationRiskLevel $riskLevel): self
    {
        $this->riskLevel = $riskLevel;

        return $this;
    }

    public function getAlertTriggered(): bool
    {
        return $this->alertTriggered;
    }

    public function setAlertTriggered(bool $alertTriggered): self
    {
        $this->alertTriggered = $alertTriggered;

        return $this;
    }
}
