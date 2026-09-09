<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\Message;

final readonly class SyncFacultyGroupsMessage
{
    public function __construct(public int $facultyId)
    {
    }
}
