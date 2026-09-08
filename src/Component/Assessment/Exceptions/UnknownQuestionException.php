<?php

declare(strict_types=1);

namespace App\Component\Assessment\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UnknownQuestionException extends BadRequestHttpException
{
    public function __construct(int $questionId)
    {
        parent::__construct(sprintf('Question %d does not belong to this quiz.', $questionId));
    }
}
