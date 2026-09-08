<?php

declare(strict_types=1);

namespace App\Component\Appeal;

use App\Component\Notification\NotificationDispatcher;
use App\Entity\Appeal;
use App\Entity\User;
use App\Enum\AppealStatus;

final class AppealCreator
{
    public function __construct(
        private readonly AppealManager $appealManager,
        private readonly NotificationDispatcher $notificationDispatcher,
    ) {
    }

    public function create(Appeal $appeal, User $student): Appeal
    {
        $appeal->setStudent($student);
        $appeal->setStatus(AppealStatus::Open);
        $appeal->setReply(null);
        $appeal->setRepliedBy(null);
        $appeal->setRepliedAt(null);
        $this->appealManager->save($appeal, true);
        $this->notificationDispatcher->notifyPsychologistsOnNewAppeal($appeal);

        return $appeal;
    }
}
