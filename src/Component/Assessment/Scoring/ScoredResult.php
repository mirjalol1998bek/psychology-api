<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

final readonly class ScoredResult
{
    /**
     * @param list<BreakdownItem> $breakdown
     */
    public function __construct(
        public string $resultKey,
        public ?int $score,
        public array $breakdown,
    ) {
    }

    /**
     * @return list<array{label: string, value: int}>
     */
    public function breakdownToArray(): array
    {
        return array_map(static fn (BreakdownItem $item): array => $item->toArray(), $this->breakdown);
    }
}
