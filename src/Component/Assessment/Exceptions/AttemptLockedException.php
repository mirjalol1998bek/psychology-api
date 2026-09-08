<?php

declare(strict_types=1);

namespace App\Component\Assessment\Exceptions;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

final class AttemptLockedException extends ConflictHttpException
{
    public function __construct()
    {
        parent::__construct('This attempt is already submitted.');
    }
}
