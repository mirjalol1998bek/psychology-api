<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Component\Assessment\Dto\AnswerInput;
use App\Component\Assessment\Dto\SaveAnswersInput;
use App\Component\Assessment\Exceptions\AttemptLockedException;
use App\Component\Assessment\Exceptions\UnknownQuestionException;
use App\Entity\Attempt;
use App\Entity\AttemptAnswer;
use App\Entity\Question;
use App\Repository\AnswerOptionRepository;

final class AttemptAnswerRecorder
{
    public function __construct(
        private readonly AnswerOptionRepository $answerOptionRepository,
        private readonly AttemptManager $attemptManager,
    ) {
    }

    public function record(Attempt $attempt, SaveAnswersInput $input): void
    {
        $this->assertEditable($attempt);
        $attempt->clearAnswers();

        foreach ($input->answers as $answerInput) {
            $attempt->addAnswer($this->buildAnswer($attempt, $answerInput));
        }

        $this->attemptManager->save($attempt, true);
    }

    private function assertEditable(Attempt $attempt): void
    {
        if ($attempt->getStatus()->isFinished() === true) {
            throw new AttemptLockedException();
        }
    }

    private function buildAnswer(Attempt $attempt, AnswerInput $input): AttemptAnswer
    {
        $answer = new AttemptAnswer();
        $answer->setQuestion($this->resolveQuestion($attempt, $input->questionId));
        $answer->setTextValue($input->text);

        foreach ($input->optionIds as $optionId) {
            $option = $this->answerOptionRepository->find($optionId);

            if ($option !== null) {
                $answer->addSelectedOption($option);
            }
        }

        return $answer;
    }

    private function resolveQuestion(Attempt $attempt, int $questionId): Question
    {
        foreach ($attempt->getQuiz()->getQuestions() as $question) {
            if ($question->getId() === $questionId) {
                return $question;
            }
        }

        throw new UnknownQuestionException($questionId);
    }
}
