<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Component\Assessment\Exceptions\AttemptLockedException;
use App\Component\Assessment\Scoring\ScoredResult;
use App\Component\Assessment\Scoring\ScorerResolver;
use App\Entity\AssessmentResult;
use App\Entity\Attempt;
use App\Enum\AttemptStatus;
use DateTime;

final class AttemptSubmitter
{
    public function __construct(
        private readonly ScorerResolver $scorerResolver,
        private readonly ResultDescriber $resultDescriber,
        private readonly AttemptManager $attemptManager,
    ) {
    }

    public function submit(Attempt $attempt): AssessmentResult
    {
        $this->assertNotSubmitted($attempt);

        $result = new AssessmentResult();
        $result->setAttempt($attempt);
        $result->setCreatedAt(new DateTime());
        $this->applyScore($attempt, $result, $this->score($attempt));
        $this->markSubmitted($attempt, $result);
        $this->attemptManager->save($attempt, true);

        return $result;
    }

    public function score(Attempt $attempt): ScoredResult
    {
        return $this->scorerResolver
            ->resolve($attempt->getQuiz()->getCategory()->getInstrumentType())
            ->score($attempt);
    }

    /** Topshirishda ham, mavjud natijani qayta hisoblashda ham bir xil. */
    public function applyScore(Attempt $attempt, AssessmentResult $result, ScoredResult $scored): void
    {
        $described = $this->resultDescriber->describe($attempt, $scored->resultKey);

        $result->setResultKey($scored->resultKey);
        $result->setLabel($described['label']);
        $result->setDescription($described['description']);
        $result->setScore($scored->score);
        $result->setBreakdown($scored->breakdownToArray());
    }

    private function assertNotSubmitted(Attempt $attempt): void
    {
        if ($attempt->getStatus()->isFinished() === true) {
            throw new AttemptLockedException();
        }
    }

    private function markSubmitted(Attempt $attempt, AssessmentResult $result): void
    {
        $attempt->setResult($result);
        $attempt->setStatus(AttemptStatus::Submitted);
        $attempt->setSubmittedAt(new DateTime());
    }
}
