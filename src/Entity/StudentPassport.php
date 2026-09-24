<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Enum\EducationForm;
use App\Enum\FamilyStatus;
use App\Enum\FamilyType;
use App\Enum\FinancialStatus;
use App\Enum\Gender;
use App\Enum\LivingArrangement;
use App\Enum\WorkStatus;
use App\Repository\StudentPassportRepository;
use App\State\CurrentUserPassportProvider;
use App\State\PassportPutProcessor;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Ijtimoiy-psixologik anketa — har talabada bitta, o'zi to'ldiradi (FISH,
 * fakultet, guruh HEMIS'dan avtomatik — bu yerda saqlanmaydi). `personalCode`
 * bundan mustasno — psixolog beradi, talaba formasida ko'rinmaydi/tahrirlanmaydi.
 */
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: 'student_passport',
            provider: CurrentUserPassportProvider::class,
            name: 'myPassport',
        ),
        new Put(
            uriTemplate: 'student_passport',
            provider: CurrentUserPassportProvider::class,
            processor: PassportPutProcessor::class,
            denormalizationContext: ['groups' => ['passport:write']],
            extraProperties: ['standard_put' => true],
            name: 'putMyPassport',
        ),
        new GetCollection(
            security: "is_granted('ROLE_PSYCHOLOGIST')",
            normalizationContext: ['groups' => ['passport:read', 'passport:read:staff']],
        ),
    ],
    normalizationContext: ['groups' => ['passport:read']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ApiFilter(SearchFilter::class, properties: [
    'student' => 'exact',
    'student.studyGroup' => 'exact',
    'student.studyGroup.faculty' => 'exact',
])]
#[ORM\Entity(repositoryClass: StudentPassportRepository::class)]
#[ORM\Table(name: 'student_passport')]
class StudentPassport implements UpdatedAtSettableInterface
{
    use UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['passport:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['passport:read:staff'])]
    private ?User $student = null;

    /** Psixolog beradi — talaba formasida yo'q. */
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['passport:read:staff'])]
    private ?string $personalCode = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?DateTimeInterface $birthDate = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true, enumType: Gender::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?Gender $gender = null;

    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $permanentAddress = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true, enumType: LivingArrangement::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?LivingArrangement $livingArrangement = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?int $commuteMinutes = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true, enumType: FamilyStatus::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?FamilyStatus $familyStatus = null;

    #[ORM\Column(type: Types::STRING, length: 24, nullable: true, enumType: FamilyType::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?FamilyType $familyType = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?int $siblingsCount = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?int $birthOrder = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $fatherInfo = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $motherInfo = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true, enumType: FinancialStatus::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?FinancialStatus $financialStatus = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true, enumType: EducationForm::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?EducationForm $educationForm = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true, enumType: WorkStatus::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?WorkStatus $workStatus = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $priorEducation = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $gpaScore = null;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $languageLevel = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $extracurricular = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $leisureActivity = null;

    /** Ixtiyoriy — anketaning o'zida shunday belgilangan. */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $healthLimitations = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?bool $priorPsychologistVisit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $currentConcern = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['passport:read'])]
    private ?DateTimeInterface $updatedAt = null;

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

    public function getPersonalCode(): ?string
    {
        return $this->personalCode;
    }

    public function setPersonalCode(?string $personalCode): self
    {
        $this->personalCode = $personalCode;

        return $this;
    }

    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPermanentAddress(): ?string
    {
        return $this->permanentAddress;
    }

    public function setPermanentAddress(?string $permanentAddress): self
    {
        $this->permanentAddress = $permanentAddress;

        return $this;
    }

    public function getLivingArrangement(): ?LivingArrangement
    {
        return $this->livingArrangement;
    }

    public function setLivingArrangement(?LivingArrangement $livingArrangement): self
    {
        $this->livingArrangement = $livingArrangement;

        return $this;
    }

    public function getCommuteMinutes(): ?int
    {
        return $this->commuteMinutes;
    }

    public function setCommuteMinutes(?int $commuteMinutes): self
    {
        $this->commuteMinutes = $commuteMinutes;

        return $this;
    }

    public function getFamilyStatus(): ?FamilyStatus
    {
        return $this->familyStatus;
    }

    public function setFamilyStatus(?FamilyStatus $familyStatus): self
    {
        $this->familyStatus = $familyStatus;

        return $this;
    }

    public function getFamilyType(): ?FamilyType
    {
        return $this->familyType;
    }

    public function setFamilyType(?FamilyType $familyType): self
    {
        $this->familyType = $familyType;

        return $this;
    }

    public function getSiblingsCount(): ?int
    {
        return $this->siblingsCount;
    }

    public function setSiblingsCount(?int $siblingsCount): self
    {
        $this->siblingsCount = $siblingsCount;

        return $this;
    }

    public function getBirthOrder(): ?int
    {
        return $this->birthOrder;
    }

    public function setBirthOrder(?int $birthOrder): self
    {
        $this->birthOrder = $birthOrder;

        return $this;
    }

    public function getFatherInfo(): ?string
    {
        return $this->fatherInfo;
    }

    public function setFatherInfo(?string $fatherInfo): self
    {
        $this->fatherInfo = $fatherInfo;

        return $this;
    }

    public function getMotherInfo(): ?string
    {
        return $this->motherInfo;
    }

    public function setMotherInfo(?string $motherInfo): self
    {
        $this->motherInfo = $motherInfo;

        return $this;
    }

    public function getFinancialStatus(): ?FinancialStatus
    {
        return $this->financialStatus;
    }

    public function setFinancialStatus(?FinancialStatus $financialStatus): self
    {
        $this->financialStatus = $financialStatus;

        return $this;
    }

    public function getEducationForm(): ?EducationForm
    {
        return $this->educationForm;
    }

    public function setEducationForm(?EducationForm $educationForm): self
    {
        $this->educationForm = $educationForm;

        return $this;
    }

    public function getWorkStatus(): ?WorkStatus
    {
        return $this->workStatus;
    }

    public function setWorkStatus(?WorkStatus $workStatus): self
    {
        $this->workStatus = $workStatus;

        return $this;
    }

    public function getPriorEducation(): ?string
    {
        return $this->priorEducation;
    }

    public function setPriorEducation(?string $priorEducation): self
    {
        $this->priorEducation = $priorEducation;

        return $this;
    }

    public function getGpaScore(): ?string
    {
        return $this->gpaScore;
    }

    public function setGpaScore(?string $gpaScore): self
    {
        $this->gpaScore = $gpaScore;

        return $this;
    }

    public function getLanguageLevel(): ?string
    {
        return $this->languageLevel;
    }

    public function setLanguageLevel(?string $languageLevel): self
    {
        $this->languageLevel = $languageLevel;

        return $this;
    }

    public function getExtracurricular(): ?string
    {
        return $this->extracurricular;
    }

    public function setExtracurricular(?string $extracurricular): self
    {
        $this->extracurricular = $extracurricular;

        return $this;
    }

    public function getLeisureActivity(): ?string
    {
        return $this->leisureActivity;
    }

    public function setLeisureActivity(?string $leisureActivity): self
    {
        $this->leisureActivity = $leisureActivity;

        return $this;
    }

    public function getHealthLimitations(): ?string
    {
        return $this->healthLimitations;
    }

    public function setHealthLimitations(?string $healthLimitations): self
    {
        $this->healthLimitations = $healthLimitations;

        return $this;
    }

    public function getPriorPsychologistVisit(): ?bool
    {
        return $this->priorPsychologistVisit;
    }

    public function setPriorPsychologistVisit(?bool $priorPsychologistVisit): self
    {
        $this->priorPsychologistVisit = $priorPsychologistVisit;

        return $this;
    }

    public function getCurrentConcern(): ?string
    {
        return $this->currentConcern;
    }

    public function setCurrentConcern(?string $currentConcern): self
    {
        $this->currentConcern = $currentConcern;

        return $this;
    }

    #[Groups(['passport:read:staff'])]
    public function getStudentId(): ?int
    {
        return $this->student?->getId();
    }

    #[Groups(['passport:read:staff'])]
    public function getStudentHemisId(): ?string
    {
        return $this->student?->getHemisId();
    }

    #[Groups(['passport:read:staff'])]
    public function getStudentName(): ?string
    {
        return $this->student?->getFullName();
    }

    /** Sog'liq cheklovi (healthLimitations) — anketada aniq ixtiyoriy, hisobga kirmaydi. */
    #[Groups(['passport:read'])]
    public function getCompleteness(): int
    {
        $filled = 0;

        foreach ($this->requiredValues() as $value) {
            if ($value !== null && $value !== '') {
                $filled++;
            }
        }

        return (int) round($filled / count($this->requiredValues()) * 100);
    }

    /**
     * @return list<mixed>
     */
    private function requiredValues(): array
    {
        return [
            $this->birthDate,
            $this->gender,
            $this->permanentAddress,
            $this->livingArrangement,
            $this->commuteMinutes,
            $this->familyStatus,
            $this->familyType,
            $this->siblingsCount,
            $this->birthOrder,
            $this->fatherInfo,
            $this->motherInfo,
            $this->financialStatus,
            $this->educationForm,
            $this->workStatus,
            $this->priorEducation,
            $this->gpaScore,
            $this->languageLevel,
            $this->extracurricular,
            $this->leisureActivity,
            $this->priorPsychologistVisit,
            $this->currentConcern,
        ];
    }
}
