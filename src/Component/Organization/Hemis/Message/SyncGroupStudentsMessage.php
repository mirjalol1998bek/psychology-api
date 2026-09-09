<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\Message;

final readonly class SyncGroupStudentsMessage
{
    public function __construct(public int $studyGroupId)
    {
    }
}
