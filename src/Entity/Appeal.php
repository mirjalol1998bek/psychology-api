<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\AppealReplyAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Enum\AppealMode;
use App\Enum\AppealStatus;
use App\Enum\AppealTopic;
use App\Repository\AppealRepository;
use App\State\AppealCollectionProvider;
use App\State\AppealCreateProcessor;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(provider: AppealCollectionProvider::class),
        new Get(security: "object.getStudent() == user || is_granted('ROLE_PSYCHOLOGIST')"),
        new Post(
            denormalizationContext: ['groups' => ['appeal:create']],
            processor: AppealCreateProcessor::class,
        ),
        new Post(
            uriTemplate: 'appeals/{id}/reply',
            controller: AppealReplyAction::class,
            denormalizationContext: ['groups' => ['appeal:reply']],
            security: "is_granted('ROLE_PSYCHOLOGIST')",
            name: 'replyAppeal',
        ),
    ],
    normalizationContext: ['groups' => ['appeal:read']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact', 'topic' => 'exact', 'student' => 'exact'])]
#[ORM\Entity(repositoryClass: AppealRepository::class)]
class Appeal implements CreatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['appeal:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appeal:read:staff'])]
    private ?User $student = null;

    #[ORM\Column(type: Types::STRING, length: 16, enumType: AppealMode::class)]
    #[Groups(['appeal:read', 'appeal:create'])]
    private AppealMode $mode = AppealMode::Named;

    #[ORM\Column(type: Types::STRING, length: 16, enumType: AppealTopic::class)]
    #[Groups(['appeal:read', 'appeal:create'])]
    private AppealTopic $topic = AppealTopic::Question;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 2000)]
    #[Groups(['appeal:read', 'appeal:create'])]
    private string $message = '';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['appeal:read', 'appeal:create'])]
    private bool $wantsAppointment = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['appeal:read', 'appeal:create'])]
    private ?DateTimeInterface $preferredDate = null;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    #[Groups(['appeal:read', 'appeal:create'])]
    private ?string $preferredTime = null;

    #[ORM\OneToOne(targetEntity: AppointmentSlot::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?AppointmentSlot $appointmentSlot = null;

    #[ORM\Column(type: Types::STRING, length: 16, enumType: AppealStatus::class)]
    #[Groups(['appeal:read'])]
    private AppealStatus $status = AppealStatus::Open;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['appeal:read', 'appeal:reply'])]
    private ?string $reply = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['appeal:read'])]
    private ?User $repliedBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['appeal:read'])]
    private ?DateTimeInterface $repliedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['appeal:read'])]
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

    public function getMode(): AppealMode
    {
        return $this->mode;
    }

    public function setMode(AppealMode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getTopic(): AppealTopic
    {
        return $this->topic;
    }

    public function setTopic(AppealTopic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getWantsAppointment(): bool
    {
        return $this->wantsAppointment;
    }

    public function setWantsAppointment(bool $wantsAppointment): self
    {
        $this->wantsAppointment = $wantsAppointment;

        return $this;
    }

    public function getPreferredDate(): ?DateTimeInterface
    {
        return $this->preferredDate;
    }

    public function setPreferredDate(?DateTimeInterface $preferredDate): self
    {
        $this->preferredDate = $preferredDate;

        return $this;
    }

    public function getPreferredTime(): ?string
    {
        return $this->preferredTime;
    }

    public function setPreferredTime(?string $preferredTime): self
    {
        $this->preferredTime = $preferredTime;

        return $this;
    }

    public function getAppointmentSlot(): ?AppointmentSlot
    {
        return $this->appointmentSlot;
    }

    public function setAppointmentSlot(?AppointmentSlot $appointmentSlot): self
    {
        $this->appointmentSlot = $appointmentSlot;

        return $this;
    }

    public function getStatus(): AppealStatus
    {
        return $this->status;
    }

    public function setStatus(AppealStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getReply(): ?string
    {
        return $this->reply;
    }

    public function setReply(?string $reply): self
    {
        $this->reply = $reply;

        return $this;
    }

    public function getRepliedBy(): ?User
    {
        return $this->repliedBy;
    }

    public function setRepliedBy(?User $repliedBy): self
    {
        $this->repliedBy = $repliedBy;

        return $this;
    }

    public function getRepliedAt(): ?DateTimeInterface
    {
        return $this->repliedAt;
    }

    public function setRepliedAt(?DateTimeInterface $repliedAt): self
    {
        $this->repliedAt = $repliedAt;

        return $this;
    }

    public function getIsAnonymous(): bool
    {
        return $this->mode === AppealMode::Anonymous;
    }

    #[Groups(['appeal:read'])]
    public function getSenderName(): ?string
    {
        if ($this->getIsAnonymous() === true) {
            return null;
        }

        return $this->student?->getFullName();
    }

    #[Groups(['appeal:read'])]
    public function getSenderGroup(): ?string
    {
        if ($this->getIsAnonymous() === true) {
            return null;
        }

        return $this->student?->getStudyGroup()?->getName();
    }

    #[Groups(['appeal:read'])]
    public function getRepliedByName(): ?string
    {
        return $this->repliedBy?->getFullName();
    }

    #[Groups(['appeal:read'])]
    public function getAppointmentDate(): ?string
    {
        return $this->appointmentSlot?->getDate()->format('Y-m-d');
    }

    #[Groups(['appeal:read'])]
    public function getAppointmentStartTime(): ?string
    {
        return $this->appointmentSlot?->getStartTime();
    }

    #[Groups(['appeal:read'])]
    public function getAppointmentEndTime(): ?string
    {
        return $this->appointmentSlot?->getEndTime();
    }

    #[Groups(['appeal:read'])]
    public function getAppointmentStatus(): ?string
    {
        return $this->appointmentSlot?->getStatus()->value;
    }
}
