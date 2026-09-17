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
use App\Enum\StudyLanguage;
use App\Repository\AssessmentInterpretationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Patch(security: "is_granted('ROLE_PSYCHOLOGIST')"),
        new Delete(security: "is_granted('ROLE_PSYCHOLOGIST')"),
    ],
    normalizationContext: ['groups' => ['interpretation:read']],
    denormalizationContext: ['groups' => ['interpretation:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact', 'resultKey' => 'exact', 'studyLanguage' => 'exact'])]
#[ORM\Entity(repositoryClass: AssessmentInterpretationRepository::class)]
#[ORM\Table(name: 'assessment_interpretation')]
#[ORM\UniqueConstraint(fields: ['category', 'subscaleKey', 'resultKey', 'studyLanguage'])]
class AssessmentInterpretation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['category:read', 'interpretation:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'interpretations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['interpretation:read', 'interpretation:write'])]
    private ?Category $category = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Groups(['category:read', 'category:write', 'interpretation:read', 'interpretation:write'])]
    private string $resultKey = '';

    /** `''` = umumiy natija uchun; `'*'` = subshkala ballari uchun (nomiga bog'liq bo'lmagan) umumiy talqin. */
    #[ORM\Column(type: Types::STRING, length: 8, options: ['default' => ''])]
    #[Groups(['category:read', 'category:write', 'interpretation:read', 'interpretation:write'])]
    private string $subscaleKey = '';

    #[ORM\Column(type: Types::STRING, length: 8, enumType: StudyLanguage::class)]
    #[Groups(['category:read', 'category:write', 'interpretation:read', 'interpretation:write'])]
    private StudyLanguage $studyLanguage = StudyLanguage::Uzbek;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['category:read', 'category:write', 'interpretation:read', 'interpretation:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['category:read', 'category:write', 'interpretation:read', 'interpretation:write'])]
    private string $text = '';

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

    public function getResultKey(): string
    {
        return $this->resultKey;
    }

    public function setResultKey(string $resultKey): self
    {
        $this->resultKey = $resultKey;

        return $this;
    }

    public function getSubscaleKey(): string
    {
        return $this->subscaleKey;
    }

    public function setSubscaleKey(string $subscaleKey): self
    {
        $this->subscaleKey = $subscaleKey;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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
}
