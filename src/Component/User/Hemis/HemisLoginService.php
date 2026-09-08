<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use App\Component\User\UserFactory;
use App\Component\User\UserManager;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Repository\StudyGroupRepository;
use App\Repository\UserRepository;

final class HemisLoginService
{
    public function __construct(
        private readonly HemisClient $hemisClient,
        private readonly UserRepository $userRepository,
        private readonly UserFactory $userFactory,
        private readonly UserManager $userManager,
        private readonly StudyGroupRepository $studyGroupRepository,
    ) {
    }

    public function loginByCode(string $code): User
    {
        $profile = $this->hemisClient->fetchProfile($this->hemisClient->fetchAccessToken($code));
        $user = $this->userRepository->findOneBy(['hemisId' => $profile->hemisId]);

        if ($user === null) {
            $user = $this->userFactory->createFromHemis($profile, $this->resolveGroup($profile));
        } else {
            $this->refreshFromProfile($user, $profile);
        }

        $this->userManager->save($user, true);

        return $user;
    }

    private function refreshFromProfile(User $user, HemisProfile $profile): void
    {
        $user->setFullName($profile->fullName);
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
