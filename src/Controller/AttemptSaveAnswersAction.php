<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Assessment\AttemptAnswerRecorder;
use App\Component\Assessment\Dto\SaveAnswersInput;
use App\Controller\Base\AbstractController;
use App\Entity\Attempt;
use App\Repository\AttemptRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AttemptSaveAnswersAction extends AbstractController
{
    public function __invoke(
        SaveAnswersInput $data,
        int $id,
        AttemptRepository $attemptRepository,
        AttemptAnswerRecorder $attemptAnswerRecorder,
    ): Attempt {
        $this->validate($data);
        $attempt = $this->requireOwnAttempt($attemptRepository, $id);
        $attemptAnswerRecorder->record($attempt, $data);

        return $attempt;
    }

    private function requireOwnAttempt(AttemptRepository $attemptRepository, int $id): Attempt
    {
        $attempt = $attemptRepository->find($id);

        if ($attempt === null) {
            $this->throwNotFoundException('Attempt is not found');
        }

        if ($attempt->getStudent() !== $this->getUser()) {
            throw new AccessDeniedHttpException('This attempt belongs to another student.');
        }

        return $attempt;
    }
}
