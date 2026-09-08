<?php

declare(strict_types=1);

namespace App\Component\Assignment;

use App\Entity\Assignment;
use App\Entity\Category;
use App\Entity\StudyGroup;
use DateTime;

final class AssignmentFactory
{
    public function create(Category $category, StudyGroup $studyGroup, ?DateTime $startAt = null): Assignment
    {
        $assignment = new Assignment();
        $assignment->setCategory($category);
        $assignment->setStudyGroup($studyGroup);
        $assignment->setStartAt($startAt ?? new DateTime('-1 day'));
        $assignment->setIsActive(true);
        $assignment->setCreatedAt(new DateTime());

        return $assignment;
    }
}
