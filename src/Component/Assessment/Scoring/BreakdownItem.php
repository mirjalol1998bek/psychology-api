<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

final readonly class BreakdownItem
{
    /**
     * `resultKey`/`title`/`description` — subshkala darajasi va talqini
     * (SCORE_SCALE'da subshkalalar bo'lsa); oddiy (subshkalasiz) natijada
     * bo'sh qoladi, JSON'ga chiqmaydi (`toArray()`da filtrlanadi).
     */
    public function __construct(
        public string $label,
        public int $value,
        public ?string $resultKey = null,
        public ?string $title = null,
        public ?string $description = null,
    ) {
    }

    /**
     * @return array{label: string, value: int, resultKey?: string, title?: string, description?: string}
     */
    public function toArray(): array
    {
        return array_filter([
            'label' => $this->label,
            'value' => $this->value,
            'resultKey' => $this->resultKey,
            'title' => $this->title,
            'description' => $this->description,
        ], static fn (mixed $v): bool => $v !== null);
    }
}
