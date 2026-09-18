<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\User\CurrentUser;
use App\Entity\Attempt;
use App\Repository\AttemptRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Talaba — faqat o'zining urinishlari (xavfsizlik uchun qo'lda `findBy`,
 * so'rov filtriga bog'liq emas). Xodim — ORM'ning standart
 * `CollectionProvider`'i orqali, shu sababli `#[ApiFilter]`da e'lon
 * qilingan `quiz`/`student`/`status`/`quiz.category` filtri to'g'ri
 * ishlaydi (masalan "shu quiz'ni kim topshirgan" — Testlar sahifasida
 * test o'chirilmoqchi bo'lganda kerak).
 *
 * @implements ProviderInterface<Attempt>
 */
final class StudentAttemptProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)]
        private readonly ProviderInterface $collectionProvider,
        private readonly AttemptRepository $attemptRepository,
        private readonly CurrentUser $currentUser,
    ) {
    }

    /**
     * @return list<Attempt>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->currentUser->getUser();

        if ($user->getPrimaryRole()->isStaff() === true) {
            $result = $this->collectionProvider->provide($operation, $uriVariables, $context);

            return is_array($result) ? $result : iterator_to_array($result);
        }

        return $this->attemptRepository->findBy(['student' => $user], ['id' => 'DESC']);
    }
}
