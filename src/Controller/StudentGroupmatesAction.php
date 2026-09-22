<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\CurrentUser;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Talaba — sotsiometriya so'rovnomasida guruhdoshlarini tanlashi uchun
 * o'z guruhi ro'yxati (o'zisiz). Faqat ism/id — boshqa hech narsa.
 */
#[IsGranted('ROLE_STUDENT')]
class StudentGroupmatesAction extends AbstractController
{
    #[Route('/api/students/groupmates', name: 'student_groupmates', methods: ['GET'])]
    public function __invoke(CurrentUser $currentUser, UserRepository $userRepository): Response
    {
        $me = $currentUser->getUser();
        $group = $me->getStudyGroup();
        $groupmates = $group === null ? [] : $userRepository->findStudentsByGroup((int) $group->getId());

        return $this->response(
            array_values(array_map(
                static fn (User $u): array => ['id' => $u->getId(), 'fullName' => $u->getFullName()],
                array_filter($groupmates, static fn (User $u): bool => $u->getId() !== $me->getId()),
            )),
            Response::HTTP_OK,
            ResponseFormat::JSON,
        );
    }
}
