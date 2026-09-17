<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Component\Passport\StudentPassportManager;
use App\Component\User\CurrentUser;
use App\Entity\StudentPassport;
use App\Repository\StudentPassportRepository;

/**
 * @implements ProcessorInterface<StudentPassport, StudentPassport>
 */
final class PassportPutProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly StudentPassportManager $passportManager,
        private readonly StudentPassportRepository $passportRepository,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): StudentPassport
    {
        $student = $this->currentUser->getUser();
        $passport = $this->passportRepository->findOneBy(['student' => $student]) ?? $data;

        if ($passport !== $data) {
            $this->copyFields($data, $passport);
        }

        $passport->setStudent($student);
        $this->passportManager->save($passport, true);

        return $passport;
    }

    /** `personalCode` bu yerda yo'q — u passport:write guruhida emas, talaba PUT bilan yoza olmaydi. */
    private function copyFields(StudentPassport $from, StudentPassport $to): void
    {
        $to->setBirthDate($from->getBirthDate());
        $to->setGender($from->getGender());
        $to->setPermanentAddress($from->getPermanentAddress());
        $to->setLivingArrangement($from->getLivingArrangement());
        $to->setCommuteMinutes($from->getCommuteMinutes());
        $to->setFamilyStatus($from->getFamilyStatus());
        $to->setFamilyType($from->getFamilyType());
        $to->setSiblingsCount($from->getSiblingsCount());
        $to->setBirthOrder($from->getBirthOrder());
        $to->setFatherInfo($from->getFatherInfo());
        $to->setMotherInfo($from->getMotherInfo());
        $to->setFinancialStatus($from->getFinancialStatus());
        $to->setEducationForm($from->getEducationForm());
        $to->setWorkStatus($from->getWorkStatus());
        $to->setPriorEducation($from->getPriorEducation());
        $to->setGpaScore($from->getGpaScore());
        $to->setLanguageLevel($from->getLanguageLevel());
        $to->setExtracurricular($from->getExtracurricular());
        $to->setLeisureActivity($from->getLeisureActivity());
        $to->setHealthLimitations($from->getHealthLimitations());
        $to->setPriorPsychologistVisit($from->getPriorPsychologistVisit());
        $to->setCurrentConcern($from->getCurrentConcern());
    }
}
