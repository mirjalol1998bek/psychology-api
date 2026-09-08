<?php

declare(strict_types=1);

namespace App\Component\Assessment\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class SaveAnswersInput
{
    /**
     * @var list<AnswerInput>
     */
    #[Assert\Valid]
    #[Groups(['attempt:answers'])]
    public array $answers = [];
}
