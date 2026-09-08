<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\NotificationMarkReadAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Enum\NotificationType;
use App\Repository\NotificationRepository;
use App\State\CurrentUserNotificationProvider;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(provider: CurrentUserNotificationProvider::class),
        new Post(
            uriTemplate: 'notifications/mark_read',
            controller: NotificationMarkReadAction::class,
            input: false,
            name: 'markRead',
        ),
    ],
    normalizationContext: ['groups' => ['notification:read']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification implements CreatedAtSettableInterface
{
    use CreatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['notification:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;

    #[ORM\Column(type: Types::STRING, length: 32, enumType: NotificationType::class)]
    #[Groups(['notification:read'])]
    private NotificationType $type = NotificationType::AppealReply;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['notification:read'])]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:read'])]
    private string $body = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['notification:read'])]
    private ?string $link = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['notification:read'])]
    private bool $isRead = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['notification:read'])]
    private ?DateTimeInterface $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function setType(NotificationType $type): self
    {
        $this->type = $type;

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

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getIsRead(): bool
    {
        return $this->isRead;
    }

    public function markAsRead(): self
    {
        $this->isRead = true;

        return $this;
    }
}
