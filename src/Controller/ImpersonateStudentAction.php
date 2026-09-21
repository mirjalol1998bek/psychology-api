<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\TokensCreator;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ImpersonateStudentAction extends AbstractController
{
    #[Route('/api/students/{id}/impersonate', name: 'student_impersonate', methods: ['POST'])]
    public function __invoke(int $id, UserRepository $userRepository, TokensCreator $tokensCreator): Response
    {
        $student = $userRepository->find($id);

        if ($student === null) {
            $this->throwNotFoundException('Talaba topilmadi');
        }

        if ($student->hasRole(RoleEnum::Student) === false) {
            throw new BadRequestHttpException('Bu foydalanuvchi talaba emas.');
        }

        return $this->response($tokensCreator->create($student), Response::HTTP_OK, ResponseFormat::JSON);
    }
}
