<?php

declare(strict_types=1);

namespace App\Component\Assessment\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class QuizNotFoundException extends NotFoundHttpException
{
    public function __construct(string $categoryName)
    {
        parent::__construct(sprintf('No active quiz for category "%s".', $categoryName));
    }
}
