<?php

declare(strict_types=1);

namespace App\Component\Assessment\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class AnswerInput
{
    #[Assert\NotNull]
    #[Groups(['attempt:answers'])]
    public int $questionId = 0;

    /**
     * @var list<int>
     */
    #[Groups(['attempt:answers'])]
    public array $optionIds = [];

    #[Groups(['attempt:answers'])]
    public ?string $text = null;
}
