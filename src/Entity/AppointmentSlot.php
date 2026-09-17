<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
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
use App\Enum\AppointmentStatus;
use App\Repository\AppointmentSlotRepository;
use App\State\AppointmentSlotCollectionProvider;
use App\State\AppointmentSlotCreateProcessor;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * `student`/`title`/`room` boshqa talabaning band slotida yashirin —
 * AppointmentSlotCollectionProvider'da maxfiylik izohiga qarang.
 */
#[ApiResource(
    operations: [
        new GetCollection(provider: AppointmentSlotCollectionProvider::class),
        new Get(security: "object.getStudent() == user || object.getStudent() == null || is_granted('ROLE_PSYCHOLOGIST')"),
        new Post(
            security: "is_granted('ROLE_PSYCHOLOGIST')",
            processor: AppointmentSlotCreateProcessor::class,
        ),
        new Patch(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Delete(security: "is_granted('ROLE_PSYCHOLOGIST')"),
    ],
    normalizationContext: ['groups' => ['appointment:read']],
    denormalizationContext: ['groups' => ['appointment:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['psychologist' => 'exact', 'status' => 'exact', 'student' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['date'])]
#[ORM\Entity(repositoryClass: AppointmentSlotRepository::class)]
#[ORM\Table(name: 'appointment_slot')]
class AppointmentSlot implements CreatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['appointment:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appointment:read'])]
    private ?User $psychologist = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['appointment:read', 'appointment:write'])]
    private ?User $student = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['appointment:read', 'appointment:write'])]
    private DateTime $date;

    #[ORM\Column(type: Types::STRING, length: 5)]
    #[Groups(['appointment:read', 'appointment:write'])]
    private string $startTime = '09:00';

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    #[Groups(['appointment:read', 'appointment:write'])]
    private ?string $endTime = null;

    #[ORM\Column(type: Types::STRING, length: 16, enumType: AppointmentStatus::class)]
    #[Groups(['appointment:read', 'appointment:write'])]
    private AppointmentStatus $status = AppointmentStatus::Free;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['appointment:read', 'appointment:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    #[Groups(['appointment:read', 'appointment:write'])]
    private ?string $room = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->date = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPsychologist(): ?User
    {
        return $this->psychologist;
    }

    public function setPsychologist(User $psychologist): self
    {
        $this->psychologist = $psychologist;

        return $this;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function setStartTime(string $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    public function setEndTime(?string $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getStatus(): AppointmentStatus
    {
        return $this->status;
    }

    public function setStatus(AppointmentStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(?string $room): self
    {
        $this->room = $room;

        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->status === AppointmentStatus::Free && $this->endTime === null) {
            $context->buildViolation('Bo\'sh vaqt oralig\'i uchun tugash vaqti kiritilishi shart.')
                ->atPath('endTime')
                ->addViolation();

            return;
        }

        if ($this->endTime !== null && $this->endTime <= $this->startTime) {
            $context->buildViolation('Tugash vaqti boshlanish vaqtidan keyin bo\'lishi kerak.')
                ->atPath('endTime')
                ->addViolation();
        }
    }
}
