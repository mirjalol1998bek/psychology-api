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
use App\Enum\FamilyStatus;
use App\Enum\LivingEnvironment;
use App\Repository\StudentPassportRepository;
use App\State\CurrentUserPassportProvider;
use App\State\PassportPutProcessor;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

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
#[ApiFilter(SearchFilter::class, properties: ['student' => 'exact', 'student.studyGroup' => 'exact'])]
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

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?DateTimeInterface $birthDate = null;

    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $currentAddress = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true, enumType: FamilyStatus::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?FamilyStatus $familyStatus = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true, enumType: LivingEnvironment::class)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?LivingEnvironment $livingEnvironment = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $talents = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $parentsInfo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['passport:read', 'passport:write'])]
    private ?string $tutorInfo = null;

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

    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getCurrentAddress(): ?string
    {
        return $this->currentAddress;
    }

    public function setCurrentAddress(?string $currentAddress): self
    {
        $this->currentAddress = $currentAddress;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getLivingEnvironment(): ?LivingEnvironment
    {
        return $this->livingEnvironment;
    }

    public function setLivingEnvironment(?LivingEnvironment $livingEnvironment): self
    {
        $this->livingEnvironment = $livingEnvironment;

        return $this;
    }

    public function getTalents(): ?string
    {
        return $this->talents;
    }

    public function setTalents(?string $talents): self
    {
        $this->talents = $talents;

        return $this;
    }

    public function getParentsInfo(): ?string
    {
        return $this->parentsInfo;
    }

    public function setParentsInfo(?string $parentsInfo): self
    {
        $this->parentsInfo = $parentsInfo;

        return $this;
    }

    public function getTutorInfo(): ?string
    {
        return $this->tutorInfo;
    }

    public function setTutorInfo(?string $tutorInfo): self
    {
        $this->tutorInfo = $tutorInfo;

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

    #[Groups(['passport:read'])]
    public function getCompleteness(): int
    {
        $filled = 0;

        foreach ($this->requiredValues() as $value) {
            if ($value !== null && $value !== '') {
                $filled++;
            }
        }

        return (int) round($filled / 7 * 100);
    }

    /**
     * @return list<mixed>
     */
    private function requiredValues(): array
    {
        return [
            $this->birthDate,
            $this->currentAddress,
            $this->phone,
            $this->familyStatus,
            $this->livingEnvironment,
            $this->parentsInfo,
            $this->tutorInfo,
        ];
    }
}
