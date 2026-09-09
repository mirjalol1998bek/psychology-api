<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis;

final class SyncCounts
{
    public int $created = 0;
    public int $updated = 0;

    public function total(): int
    {
        return $this->created + $this->updated;
    }
}
