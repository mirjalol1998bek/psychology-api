<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\User\CurrentUser;
use App\Entity\AppointmentSlot;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * ORM'ning standart CollectionProvider'ini o'raydi (filtr/paginatsiya
 * saqlanadi) va talabaga boshqa birovning band slotini ko'rsatganda
 * `student`/`title`/`room`'ni yashiradi — faqat "band" holati qoladi.
 * Nusxa (`clone`) ustida ishlaydi, asl (boshqaruvchi) obyektga tegmaydi —
 * hech narsa flush qilinmaydi.
 *
 * @implements ProviderInterface<AppointmentSlot>
 */
final readonly class AppointmentSlotCollectionProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)]
        private ProviderInterface $collectionProvider,
        private CurrentUser $currentUser,
    ) {
    }

    /**
     * @return list<AppointmentSlot>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $slots = $this->collectionProvider->provide($operation, $uriVariables, $context);
        $viewer = $this->currentUser->getUser();

        if ($viewer->getPrimaryRole()->isStaff() === true) {
            return is_array($slots) ? $slots : iterator_to_array($slots);
        }

        $masked = [];

        foreach ($slots as $slot) {
            $masked[] = $this->maskForViewer($slot, $viewer);
        }

        return $masked;
    }

    private function maskForViewer(AppointmentSlot $slot, User $viewer): AppointmentSlot
    {
        if ($slot->getStudent() === null || $slot->getStudent() === $viewer) {
            return $slot;
        }

        $masked = clone $slot;
        $masked->setStudent(null);
        $masked->setTitle(null);
        $masked->setRoom(null);

        return $masked;
    }
}
