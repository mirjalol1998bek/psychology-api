<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Entity\Attempt;
use App\Enum\AttemptStatus;

final class AttemptResetter
{
    public function __construct(private readonly AttemptManager $attemptManager)
    {
    }

    public function reset(Attempt $attempt): void
    {
        $attempt->clearAnswers();
        $attempt->setResult(null);
        $attempt->setStatus(AttemptStatus::InProgress);
        $attempt->setSubmittedAt(null);
        $this->attemptManager->save($attempt, true);
    }
}
