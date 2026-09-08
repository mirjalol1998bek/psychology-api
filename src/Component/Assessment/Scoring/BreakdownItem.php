<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

final readonly class BreakdownItem
{
    public function __construct(
        public string $label,
        public int $value,
    ) {
    }

    /**
     * @return array{label: string, value: int}
     */
    public function toArray(): array
    {
        return ['label' => $this->label, 'value' => $this->value];
    }
}
