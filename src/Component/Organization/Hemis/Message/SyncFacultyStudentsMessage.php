<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\Message;

/**
 * Bitta fakultetning barcha hozirgi guruh + talabalarini HEMIS'dan sinxronlash
 * (`student-list?_department=`). Fon rejimida, rate limiter bilan.
 */
final readonly class SyncFacultyStudentsMessage
{
    public function __construct(public int $facultyId)
    {
    }
}
