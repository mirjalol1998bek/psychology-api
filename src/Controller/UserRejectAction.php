<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\AccessDecider;
use App\Controller\Base\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UserRejectAction extends AbstractController
{
    #[Route('/api/users/{id}/reject', name: 'user_reject', methods: ['POST'])]
    public function __invoke(int $id, UserRepository $userRepository, AccessDecider $accessDecider): Response
    {
        $user = $userRepository->find($id);

        if ($user === null) {
            $this->throwNotFoundException('Foydalanuvchi topilmadi');
        }

        $accessDecider->reject($user);

        return $this->responseEmpty();
    }
}
