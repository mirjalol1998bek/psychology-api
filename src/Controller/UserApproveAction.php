<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\AccessDecider;
use App\Component\User\Dtos\AccessApproveDto;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')]
class UserApproveAction extends AbstractController
{
    #[Route('/api/users/{id}/approve', name: 'user_approve', methods: ['POST'])]
    public function __invoke(
        int $id,
        Request $request,
        UserRepository $userRepository,
        AccessDecider $accessDecider,
    ): Response {
        $user = $userRepository->find($id);

        if ($user === null) {
            $this->throwNotFoundException('Foydalanuvchi topilmadi');
        }

        $accessDecider->approve($user, $this->resolveRole($request));

        return $this->responseEmpty();
    }

    private function resolveRole(Request $request): RoleEnum
    {
        $role = RoleEnum::tryFrom($this->readRequestedRole($request));

        if ($role === null || $role === RoleEnum::Student) {
            throw new BadRequestHttpException(
                'Rol faqat ROLE_PSYCHOLOGIST, ROLE_ADMIN yoki ROLE_TUTOR bo\'lishi mumkin.',
            );
        }

        return $role;
    }

    private function readRequestedRole(Request $request): string
    {
        if ($request->getContent() === '') {
            return RoleEnum::Psychologist->value;
        }

        /** @var AccessApproveDto $dto */
        $dto = $this->getDtoFromRequest($request, AccessApproveDto::class, ResponseFormat::JSON);

        return $dto->getRole();
    }
}
