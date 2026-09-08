<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Organization\StudentFactory;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StudentCreateAction extends AbstractController
{
    public function __invoke(
        User $data,
        StudentFactory $studentFactory,
        UserManager $userManager,
        UserRepository $userRepository,
    ): User {
        $fullName = trim((string) $data->getFullName());

        if ($fullName === '') {
            throw new BadRequestHttpException('fullName is required.');
        }

        if ($this->isHemisIdTaken($userRepository, $data->getHemisId())) {
            throw new BadRequestHttpException('A student with this hemisId already exists.');
        }

        $student = $studentFactory->create(
            $fullName,
            $data->getHemisId(),
            $data->getStudyGroup(),
            $data->getStudyLanguage(),
        );
        $userManager->hashPassword($student, bin2hex(random_bytes(8)));
        $userManager->save($student, true);

        return $student;
    }

    private function isHemisIdTaken(UserRepository $userRepository, ?string $hemisId): bool
    {
        if ($hemisId === null) {
            return false;
        }

        return $userRepository->findOneBy(['hemisId' => $hemisId]) !== null;
    }
}
