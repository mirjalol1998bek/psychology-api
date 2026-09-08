<?php

declare(strict_types=1);

namespace App\Component\Assessment\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class IncompleteAttemptException extends BadRequestHttpException
{
}
