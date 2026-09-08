<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Entity\Assignment;
use App\Entity\Attempt;
use App\Entity\Quiz;
use App\Entity\User;
use App\Enum\AttemptStatus;

final class AttemptFactory
{
    public function create(User $student, Quiz $quiz, ?Assignment $assignment): Attempt
    {
        $attempt = new Attempt();
        $attempt->setStudent($student);
        $attempt->setQuiz($quiz);
        $attempt->setAssignment($assignment);
        $attempt->setStatus(AttemptStatus::InProgress);

        return $attempt;
    }
}
