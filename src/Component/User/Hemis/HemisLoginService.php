<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use App\Component\Notification\NotificationDispatcher;
use App\Component\User\UserFactory;
use App\Component\User\UserManager;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Enum\HemisPortal;
use App\Enum\UserStatusEnum;
use App\Repository\StudyGroupRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;

final class HemisLoginService
{
    public function __construct(
        private readonly HemisClient $hemisClient,
        private readonly UserRepository $userRepository,
        private readonly UserFactory $userFactory,
        private readonly UserManager $userManager,
        private readonly StudyGroupRepository $studyGroupRepository,
        private readonly NotificationDispatcher $notificationDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function loginByCode(string $code, HemisPortal $portal): User
    {
        $token = $this->hemisClient->fetchAccessToken($code, $portal);
        $profile = $this->hemisClient->fetchProfile($token, $portal);
        $user = $this->findExisting($profile);
        $isNew = $user === null;

        if ($isNew) {
            $user = $this->userFactory->createFromHemis($profile, $this->resolveGroup($profile));
        } else {
            $this->refreshFromProfile($user, $profile);
        }

        $this->userManager->save($user, true);

        if ($isNew && $user->getStatus() === UserStatusEnum::Pending) {
            $this->notificationDispatcher->notifyAdminsOnAccessRequest($user);
        }

        if ($profile->isEmployee() && ($isNew || $profile->employeeIds === [])) {
            // Xodim sinxron yozuviga Xodim ID orqali bog'lanmadi — tahlil uchun (qiymatlarsiz).
            $this->logger->warning('HEMIS xodim profili sinxron yozuviga bog\'lanmadi', [
                'userId' => $user->getId(),
                'isNew' => $isNew,
                'employeeIds' => count($profile->employeeIds),
                'fields' => implode(', ', $profile->fieldNames),
            ]);
        }

        return $user;
    }

    /**
     * Sinxron yozuvlarni `employee_id_number` (xodim) / `student_id_number`
     * (talaba) bilan saqlaydi. Talabaning OAuth `login`i — Talaba ID, xodimniki
     * esa foydalanuvchi nomi (`bekzod_utekov`) — shu sabab xodim avval
     * profilidagi Xodim ID'lari bo'yicha qidiriladi. `id` — dastlabki OAuth
     * yozuvlari (masalan `2506`), `login` — talaba va yangi hisoblar.
     */
    private function findExisting(HemisProfile $profile): ?User
    {
        foreach ($profile->employeeIds as $employeeId) {
            $user = $this->userRepository->findOneBy(['hemisId' => $employeeId]);

            if ($user !== null) {
                return $user;
            }
        }

        return $this->userRepository->findOneBy(['hemisId' => $profile->hemisId])
            ?? $this->userRepository->findOneBy(['hemisId' => $profile->login]);
    }

    private function refreshFromProfile(User $user, HemisProfile $profile): void
    {
        // Sinxrondagi to'liq F.I.Sh ("UTEKOV BEKZOD MARAT O'G'LI") OAuth'dagi
        // qisqa ism ("BEKZOD UTEKOV") bilan almashtirilmasin.
        if (($user->getFullName() ?? '') === '') {
            $user->setFullName($profile->fullName);
        }

        $user->setImage($profile->picture);

        if ($user->getStudyGroup() === null) {
            $group = $this->resolveGroup($profile);

            if ($group !== null) {
                $user->setStudyGroup($group);
            }
        }
    }

    private function resolveGroup(HemisProfile $profile): ?StudyGroup
    {
        if ($profile->groupName === null) {
            return null;
        }

        return $this->studyGroupRepository->findOneBy(['externalId' => $profile->groupName])
            ?? $this->studyGroupRepository->findOneBy(['name' => $profile->groupName]);
    }
}
