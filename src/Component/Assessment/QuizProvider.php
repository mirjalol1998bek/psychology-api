<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Component\Assessment\Exceptions\QuizNotFoundException;
use App\Entity\Category;
use App\Entity\Quiz;
use App\Enum\StudyLanguage;
use App\Repository\QuizRepository;

final class QuizProvider
{
    public function __construct(private readonly QuizRepository $quizRepository)
    {
    }

    public function forCategoryAndLanguage(Category $category, StudyLanguage $language): Quiz
    {
        $quiz = $this->findActive($category, $language) ?? $this->findActive($category, StudyLanguage::Uzbek);

        if ($quiz === null) {
            throw new QuizNotFoundException($category->getName());
        }

        return $quiz;
    }

    private function findActive(Category $category, StudyLanguage $language): ?Quiz
    {
        return $this->quizRepository->findOneBy([
            'category' => $category,
            'studyLanguage' => $language,
            'isActive' => true,
        ]);
    }
}
