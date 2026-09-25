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

    /**
     * Eng ko'p ballga ega BARCHA kalitlar — teng bo'lsa hech biri ustun
     * qo'yilmaydi (aralash tur). Alifbo tartibida: savol/variant tartibiga
     * (til bo'yicha farq qiladi) bog'liq bo'lmasin.
     *
     * @return list<string>
     */
    public function topKeys(): array
    {
        if ($this->counts === []) {
            return [];
        }

        $max = max($this->counts);
        $keys = array_map('strval', array_keys(array_filter($this->counts, static fn (int $value): bool => $value === $max)));
        sort($keys, SORT_STRING);

        return $keys;
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
