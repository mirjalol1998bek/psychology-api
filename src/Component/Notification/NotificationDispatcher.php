<?php

declare(strict_types=1);

namespace App\Component\Notification;

use App\Entity\Appeal;
use App\Entity\User;
use App\Enum\NotificationType;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;

final class NotificationDispatcher
{
    public function __construct(
        private readonly NotificationFactory $notificationFactory,
        private readonly NotificationManager $notificationManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function notifyPsychologistsOnNewAppeal(Appeal $appeal): void
    {
        $recipients = $this->staffRecipients();
        $lastIndex = count($recipients) - 1;

        foreach ($recipients as $index => $recipient) {
            $notification = $this->notificationFactory->create(
                $recipient,
                NotificationType::AppealNew,
                'Yangi murojaat',
                $this->previewOf($appeal),
                '/appeals/' . $appeal->getId(),
            );
            $this->notificationManager->save($notification, $index === $lastIndex);
        }
    }

    public function notifyAdminsOnAccessRequest(User $requester): void
    {
        $recipients = $this->adminRecipients();
        $lastIndex = count($recipients) - 1;

        foreach ($recipients as $index => $recipient) {
            $notification = $this->notificationFactory->create(
                $recipient,
                NotificationType::AccessRequest,
                'Yangi kirish so\'rovi',
                ($requester->getFullName() ?? $requester->getEmail() ?? 'Xodim') . ' tizimga kirmoqchi. Rol bering yoki rad eting.',
                '/admin/access-requests',
            );
            $this->notificationManager->save($notification, $index === $lastIndex);
        }
    }

    public function notifyUserOnAccessApproved(User $user): void
    {
        $notification = $this->notificationFactory->create(
            $user,
            NotificationType::AccessApproved,
            'Kirish ruxsati berildi',
            'Administrator arizangizni tasdiqladi. Endi HEMIS orqali tizimga kira olasiz.',
            '/',
        );
        $this->notificationManager->save($notification, true);
    }

    public function notifyStudentOnAppealReply(Appeal $appeal): void
    {
        $student = $appeal->getStudent();

        if ($student === null) {
            return;
        }

        $notification = $this->notificationFactory->create(
            $student,
            NotificationType::AppealReply,
            'Murojaatingizga javob keldi',
            $this->previewOf($appeal),
            '/appeals/' . $appeal->getId(),
        );
        $this->notificationManager->save($notification, true);
    }

    /**
     * @return list<User>
     */
    private function staffRecipients(): array
    {
        $byId = [];

        foreach ([RoleEnum::Psychologist, RoleEnum::Admin] as $role) {
            foreach ($this->userRepository->findByRole($role->value) as $user) {
                $byId[$user->getId()] = $user;
            }
        }

        return array_values($byId);
    }

    /**
     * @return list<User>
     */
    private function adminRecipients(): array
    {
        return array_values($this->userRepository->findByRole(RoleEnum::Admin->value));
    }

    private function previewOf(Appeal $appeal): string
    {
        $message = $appeal->getMessage();

        if (mb_strlen($message) <= 120) {
            return $message;
        }

        return mb_substr($message, 0, 120) . '…';
    }
}
