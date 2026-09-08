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

    private function previewOf(Appeal $appeal): string
    {
        $message = $appeal->getMessage();

        if (mb_strlen($message) <= 120) {
            return $message;
        }

        return mb_substr($message, 0, 120) . '…';
    }
}
