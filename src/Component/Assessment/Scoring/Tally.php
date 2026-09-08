<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

final class Tally
{
    /**
     * @var array<string, int>
     */
    private array $counts = [];

    public function register(string $key): void
    {
        $this->counts[$key] ??= 0;
    }

    public function add(string $key, int $amount): void
    {
        $this->counts[$key] = ($this->counts[$key] ?? 0) + $amount;
    }

    public function topKey(): string
    {
        $topKey = (string) array_key_first($this->counts);
        $topValue = -1;

        foreach ($this->counts as $key => $value) {
            if ($value > $topValue) {
                $topKey = $key;
                $topValue = $value;
            }
        }

        return $topKey;
    }

    /**
     * @return list<BreakdownItem>
     */
    public function toBreakdown(): array
    {
        $items = [];

        foreach ($this->counts as $key => $value) {
            $items[] = new BreakdownItem($key, $value);
        }

        return $items;
    }

    public function isEmpty(): bool
    {
        return $this->counts === [];
    }
}
