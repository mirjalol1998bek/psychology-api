<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AnswerOptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AnswerOptionRepository::class)]
#[ORM\Table(name: 'answer_option')]
class AnswerOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['question:read', 'quiz:read:full', 'attempt-answer:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['question:read', 'quiz:read:full', 'attempt-answer:read', 'question:write'])]
    private string $text = '';

    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private int $position = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private int $score = 0;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    #[Groups(['question:read', 'quiz:read:full', 'question:write'])]
    private ?string $categoryKey = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): self
    {
        $this->question = $question;

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

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getCategoryKey(): ?string
    {
        return $this->categoryKey;
    }

    public function setCategoryKey(?string $categoryKey): self
    {
        $this->categoryKey = $categoryKey;

        return $this;
    }
}
