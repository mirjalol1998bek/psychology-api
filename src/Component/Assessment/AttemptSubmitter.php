<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Component\Assessment\Exceptions\AttemptLockedException;
use App\Component\Assessment\Scoring\ScoredResult;
use App\Component\Assessment\Scoring\ScorerResolver;
use App\Entity\AssessmentInterpretation;
use App\Entity\AssessmentResult;
use App\Entity\Attempt;
use App\Enum\AttemptStatus;
use App\Repository\AssessmentInterpretationRepository;
use DateTime;

final class AttemptSubmitter
{
    public function __construct(
        private readonly ScorerResolver $scorerResolver,
        private readonly AssessmentInterpretationRepository $interpretationRepository,
        private readonly AttemptManager $attemptManager,
    ) {
    }

    public function submit(Attempt $attempt): AssessmentResult
    {
        $this->assertNotSubmitted($attempt);

        $scored = $this->scorerResolver
            ->resolve($attempt->getQuiz()->getCategory()->getInstrumentType())
            ->score($attempt);

        $result = $this->buildResult($attempt, $scored);
        $this->markSubmitted($attempt, $result);
        $this->attemptManager->save($attempt, true);

        return $result;
    }

    private function assertNotSubmitted(Attempt $attempt): void
    {
        if ($attempt->getStatus()->isFinished() === true) {
            throw new AttemptLockedException();
        }
    }

    private function buildResult(Attempt $attempt, ScoredResult $scored): AssessmentResult
    {
        $interpretation = $this->findInterpretation($attempt, $scored->resultKey);

        $result = new AssessmentResult();
        $result->setAttempt($attempt);
        $result->setResultKey($scored->resultKey);
        $result->setLabel($interpretation?->getTitle() ?? $scored->resultKey);
        $result->setDescription($interpretation?->getText() ?? '');
        $result->setScore($scored->score);
        $result->setBreakdown($scored->breakdownToArray());
        $result->setCreatedAt(new DateTime());

        return $result;
    }

    private function findInterpretation(Attempt $attempt, string $resultKey): ?AssessmentInterpretation
    {
        return $this->interpretationRepository->findOneBy([
            'category' => $attempt->getQuiz()->getCategory(),
            'resultKey' => $resultKey,
            'studyLanguage' => $attempt->getStudyLanguage(),
        ]);
    }

    private function markSubmitted(Attempt $attempt, AssessmentResult $result): void
    {
        $attempt->setResult($result);
        $attempt->setStatus(AttemptStatus::Submitted);
        $attempt->setSubmittedAt(new DateTime());
    }
}
