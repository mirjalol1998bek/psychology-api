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
 * Tyutor — faqat o'ziga biriktirilgan guruhlar talabalari ro'yxatini ko'radi
 * (test natijalari emas — shu maqsadda alohida, yengil javob shakli).
 */
#[IsGranted('ROLE_TUTOR')]
class TutorStudentsAction extends AbstractController
{
    #[Route('/api/tutor/students', name: 'tutor_students', methods: ['GET'])]
    public function __invoke(CurrentUser $currentUser, UserRepository $userRepository): Response
    {
        $students = $userRepository->findByTutor((int) $currentUser->getUser()->getId());

        return $this->response(array_map($this->toRow(...), $students), Response::HTTP_OK, ResponseFormat::JSON);
    }

    /**
     * @return array<string, mixed>
     */
    private function toRow(User $student): array
    {
        $group = $student->getStudyGroup();

        return [
            'id' => $student->getId(),
            'fullName' => $student->getFullName(),
            'hemisId' => $student->getHemisId(),
            'image' => $student->getImage(),
            'studyGroup' => $group === null ? null : ['id' => $group->getId(), 'name' => $group->getName()],
        ];
    }
}
