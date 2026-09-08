<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Assessment\AttemptSubmitter;
use App\Controller\Base\AbstractController;
use App\Entity\Attempt;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AttemptSubmitAction extends AbstractController
{
    public function __invoke(Attempt $data, AttemptSubmitter $attemptSubmitter): Attempt
    {
        if ($data->getStudent() !== $this->getUser()) {
            throw new AccessDeniedHttpException('This attempt belongs to another student.');
        }

        $attemptSubmitter->submit($data);

        return $data;
    }
}
