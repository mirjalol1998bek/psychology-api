<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Entity\AnswerOption;
use App\Entity\AssessmentInterpretation;
use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\ScoreRange;
use App\Enum\InstrumentType;
use App\Enum\QuestionType;
use App\Enum\StudyLanguage;
use DateTime;

final class AssessmentFactory
{
    public function createCategory(string $name, InstrumentType $instrumentType, int $position): Category
    {
        $category = new Category();
        $category->setName($name);
        $category->setInstrumentType($instrumentType);
        $category->setPosition($position);
        $category->setIsActive(true);
        $category->setCreatedAt(new DateTime());

        return $category;
    }

    public function createQuiz(Category $category, string $title, StudyLanguage $language): Quiz
    {
        $quiz = new Quiz();
        $quiz->setCategory($category);
        $quiz->setTitle($title);
        $quiz->setStudyLanguage($language);
        $quiz->setIsActive(true);
        $quiz->setCreatedAt(new DateTime());

        return $quiz;
    }

    public function createQuestion(Quiz $quiz, QuestionType $type, string $text, int $position): Question
    {
        $question = new Question();
        $question->setQuiz($quiz);
        $question->setType($type);
        $question->setText($text);
        $question->setPosition($position);

        return $question;
    }

    public function createOption(Question $question, string $text, int $score, ?string $categoryKey): AnswerOption
    {
        $option = new AnswerOption();
        $option->setQuestion($question);
        $option->setText($text);
        $option->setScore($score);
        $option->setPosition($question->getOptions()->count());
        $option->setCategoryKey($categoryKey);
        $question->addOption($option);

        return $option;
    }

    public function createScoreRange(Category $category, int $min, int $max, string $resultKey): ScoreRange
    {
        $range = new ScoreRange();
        $range->setMinScore($min);
        $range->setMaxScore($max);
        $range->setResultKey($resultKey);
        $category->addScoreRange($range);

        return $range;
    }

    public function createInterpretation(
        Category $category,
        string $resultKey,
        StudyLanguage $language,
        ?string $title,
        string $text,
    ): AssessmentInterpretation {
        $interpretation = new AssessmentInterpretation();
        $interpretation->setResultKey($resultKey);
        $interpretation->setStudyLanguage($language);
        $interpretation->setTitle($title);
        $interpretation->setText($text);
        $category->addInterpretation($interpretation);

        return $interpretation;
    }
}
