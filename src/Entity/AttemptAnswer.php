<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AttemptAnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AttemptAnswerRepository::class)]
#[ORM\Table(name: 'attempt_answer')]
class AttemptAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['attempt:read:full'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Attempt::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Attempt $attempt = null;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attempt-answer:read'])]
    private ?Question $question = null;

    #[ORM\ManyToMany(targetEntity: AnswerOption::class)]
    #[ORM\JoinTable(name: 'attempt_answer_option')]
    #[Groups(['attempt-answer:read'])]
    private Collection $selectedOptions;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['attempt:read:full', 'attempt-answer:read'])]
    private ?string $textValue = null;

    #[Groups(['attempt:read:full'])]
    public function getQuestionId(): ?int
    {
        return $this->question?->getId();
    }

    /**
     * @return list<int>
     */
    #[Groups(['attempt:read:full'])]
    public function getSelectedOptionIds(): array
    {
        $ids = [];

        foreach ($this->selectedOptions as $option) {
            $ids[] = (int) $option->getId();
        }

        return $ids;
    }

    public function __construct()
    {
        $this->selectedOptions = new ArrayCollection();
    }

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

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return Collection<int, AnswerOption>
     */
    public function getSelectedOptions(): Collection
    {
        return $this->selectedOptions;
    }

    public function addSelectedOption(AnswerOption $option): self
    {
        if (!$this->selectedOptions->contains($option)) {
            $this->selectedOptions->add($option);
        }

        return $this;
    }

    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    public function setTextValue(?string $textValue): self
    {
        $this->textValue = $textValue;

        return $this;
    }

    public function getFirstOption(): ?AnswerOption
    {
        $first = $this->selectedOptions->first();

        return $first === false ? null : $first;
    }
}
