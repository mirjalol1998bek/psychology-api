<?php

declare(strict_types=1);

namespace App\Component\Assessment\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class AssignmentNotFoundException extends AccessDeniedHttpException
{
    public function __construct(string $categoryName)
    {
        parent::__construct(sprintf('You have no open assignment for "%s".', $categoryName));
    }
}
