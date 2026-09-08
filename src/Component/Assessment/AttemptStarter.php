<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Component\Assessment\Exceptions\AssignmentNotFoundException;
use App\Entity\Assignment;
use App\Entity\Attempt;
use App\Entity\Category;
use App\Entity\User;
use App\Enum\StudyLanguage;
use App\Repository\AssignmentRepository;
use App\Repository\AttemptRepository;
use DateTimeImmutable;

final class AttemptStarter
{
    public function __construct(
        private readonly AssignmentRepository $assignmentRepository,
        private readonly AttemptRepository $attemptRepository,
        private readonly QuizProvider $quizProvider,
        private readonly AttemptFactory $attemptFactory,
        private readonly AttemptManager $attemptManager,
    ) {
    }

    public function start(User $student, Category $category): Attempt
    {
        $assignment = $this->requireAssignment($student, $category);
        $quiz = $this->quizProvider->forCategoryAndLanguage($category, $this->languageOf($student));
        $existing = $this->attemptRepository->findOneBy(['student' => $student, 'quiz' => $quiz]);

        if ($existing !== null) {
            return $existing;
        }

        $attempt = $this->attemptFactory->create($student, $quiz, $assignment);
        $this->attemptManager->save($attempt, true);

        return $attempt;
    }

    private function requireAssignment(User $student, Category $category): Assignment
    {
        $group = $student->getStudyGroup();
        $assignments = $group === null
            ? []
            : $this->assignmentRepository->findBy(['category' => $category, 'studyGroup' => $group]);

        foreach ($assignments as $assignment) {
            if ($assignment->isOpenAt(new DateTimeImmutable()) === true) {
                return $assignment;
            }
        }

        throw new AssignmentNotFoundException($category->getName());
    }

    private function languageOf(User $student): StudyLanguage
    {
        return $student->getStudyLanguage() ?? StudyLanguage::Uzbek;
    }
}
