<?php

declare(strict_types=1);

namespace App\Component\Appeal;

use App\Component\Notification\NotificationDispatcher;
use App\Entity\Appeal;
use App\Entity\User;
use App\Enum\AppealStatus;
use DateTime;

final class AppealReplyWriter
{
    public function __construct(
        private readonly AppealManager $appealManager,
        private readonly NotificationDispatcher $notificationDispatcher,
    ) {
    }

    public function write(Appeal $appeal, string $reply, User $author): Appeal
    {
        $appeal->setReply($reply);
        $appeal->setRepliedBy($author);
        $appeal->setRepliedAt(new DateTime());
        $appeal->setStatus(AppealStatus::Answered);
        $this->appealManager->save($appeal, true);
        $this->notificationDispatcher->notifyStudentOnAppealReply($appeal);

        return $appeal;
    }
}
