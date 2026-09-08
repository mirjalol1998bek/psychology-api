<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Assessment\AttemptResetter;
use App\Controller\Base\AbstractController;
use App\Entity\Attempt;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AttemptResetAction extends AbstractController
{
    public function __invoke(Attempt $data, AttemptResetter $attemptResetter): Attempt
    {
        if ($data->getStudent() !== $this->getUser() && $this->isGranted('ROLE_PSYCHOLOGIST') === false) {
            throw new AccessDeniedHttpException('Bu urinish boshqa talabaga tegishli.');
        }

        $attemptResetter->reset($data);

        return $data;
    }
}
