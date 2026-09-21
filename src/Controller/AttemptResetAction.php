<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Assessment\AttemptResetter;
use App\Controller\Base\AbstractController;
use App\Entity\Attempt;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Talabaning urinishini tozalab, qayta topshirishga ruxsat beradi.
 * Faqat xodim — talaba o'zi qayta topshira olmaydi (bir marta qoida).
 */
#[IsGranted('ROLE_PSYCHOLOGIST')]
class AttemptResetAction extends AbstractController
{
    public function __invoke(Attempt $data, AttemptResetter $attemptResetter): Attempt
    {
        $attemptResetter->reset($data);

        return $data;
    }
}
