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

    private function copyFields(StudentPassport $from, StudentPassport $to): void
    {
        $to->setBirthDate($from->getBirthDate());
        $to->setCurrentAddress($from->getCurrentAddress());
        $to->setPhone($from->getPhone());
        $to->setFamilyStatus($from->getFamilyStatus());
        $to->setLivingEnvironment($from->getLivingEnvironment());
        $to->setTalents($from->getTalents());
        $to->setParentsInfo($from->getParentsInfo());
        $to->setTutorInfo($from->getTutorInfo());
    }
}
