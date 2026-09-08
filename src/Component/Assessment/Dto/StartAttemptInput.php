<?php

declare(strict_types=1);

namespace App\Component\Assessment\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class StartAttemptInput
{
    #[Assert\Positive]
    #[Groups(['attempt:start'])]
    public int $categoryId = 0;
}
