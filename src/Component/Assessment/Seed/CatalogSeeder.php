<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

use App\Component\Assessment\AssessmentFactory;
use App\Component\Assessment\CategoryManager;
use App\Component\Assessment\QuizManager;
use App\Entity\Category;
use App\Entity\Quiz;
use App\Enum\InstrumentType;
use App\Enum\QuestionType;
use App\Enum\StudyLanguage;
use App\Repository\CategoryRepository;

final class CatalogSeeder
{
    public function __construct(
        private readonly AssessmentFactory $factory,
        private readonly CategoryManager $categoryManager,
        private readonly QuizManager $quizManager,
        private readonly CategoryRepository $categoryRepository,
        private readonly TemperamentUzData $temperamentUz,
        private readonly TemperamentRuData $temperamentRu,
        private readonly PsychogeometricData $psychogeometric,
        private readonly ZungData $zung,
    ) {
    }

    /**
     * @return list<string>
     */
    public function seed(): array
    {
        $created = [];
        $created[] = $this->seedTemperamentUz();
        $created[] = $this->seedTemperamentRu();
        $created[] = $this->seedPsychogeometric();
        $created[] = $this->seedZung();

        return array_values(array_filter($created, static fn (?string $name): bool => $name !== null));
    }

    private function seedTemperamentUz(): ?string
    {
        $name = 'Temperament testi (uz)';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::TemperamentStatements, 1);
        $quiz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillStatementQuestions($quiz);
        $this->addInterpretations($category, StudyLanguage::Uzbek, $this->temperamentUz->interpretations());
        $this->persist($category, [$quiz]);

        return $name;
    }

    private function fillStatementQuestions(Quiz $quiz): void
    {
        $position = 0;

        foreach ($this->temperamentUz->blocks() as $blockKey => $statements) {
            foreach ($statements as $statement) {
                $position++;
                $question = $this->factory->createQuestion($quiz, QuestionType::YesNo, $statement, $position);
                $this->factory->createOption($question, 'Ha', 1, $blockKey);
                $this->factory->createOption($question, 'Yo\'q', 0, null);
                $quiz->addQuestion($question);
            }
        }
    }

    private function seedTemperamentRu(): ?string
    {
        $name = 'Тест на темперамент (ru)';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::TemperamentChoice, 2);
        $quiz = $this->factory->createQuiz($category, $name, StudyLanguage::Russian);
        $this->fillChoiceQuestions($quiz);
        $this->addInterpretations($category, StudyLanguage::Russian, $this->temperamentRu->interpretations());
        $this->persist($category, [$quiz]);

        return $name;
    }

    private function fillChoiceQuestions(Quiz $quiz): void
    {
        $position = 0;

        foreach ($this->temperamentRu->questions() as $questionData) {
            $position++;
            $question = $this->factory->createQuestion($quiz, QuestionType::SingleChoice, $questionData['text'], $position);

            foreach ($questionData['options'] as $optionData) {
                $this->factory->createOption($question, $optionData['text'], 0, $optionData['category']);
            }

            $quiz->addQuestion($question);
        }
    }

    private function seedPsychogeometric(): ?string
    {
        $name = 'Psixogeometrik test';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::FigureChoice, 3);
        $quizUz = $this->buildFigureQuiz($category, 'Psixogeometrik test', StudyLanguage::Uzbek, 'labelUz');
        $quizRu = $this->buildFigureQuiz($category, 'Психогеометрический тест', StudyLanguage::Russian, 'labelRu');
        $this->addFigureInterpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    private function buildFigureQuiz(Category $category, string $title, StudyLanguage $language, string $labelField): Quiz
    {
        $quiz = $this->factory->createQuiz($category, $title, $language);
        $prompt = $language === StudyLanguage::Russian
            ? 'Выберите одну фигуру — самую близкую вам'
            : 'Sizga eng yaqin bo\'lgan bitta figurani tanlang';
        $question = $this->factory->createQuestion($quiz, QuestionType::Figure, $prompt, 1);

        foreach ($this->psychogeometric->figures() as $figure) {
            $option = $this->factory->createOption($question, $figure[$labelField], 0, $figure['key']);
            $option->setImageUrl($figure['icon']);
        }

        $quiz->addQuestion($question);

        return $quiz;
    }

    private function addFigureInterpretations(Category $category): void
    {
        foreach ($this->psychogeometric->interpretations() as $figureKey => $text) {
            $this->factory->createInterpretation($category, $figureKey, StudyLanguage::Uzbek, $figureKey, $text['uz']);
            $this->factory->createInterpretation($category, $figureKey, StudyLanguage::Russian, $figureKey, $text['ru']);
        }
    }

    private function seedZung(): ?string
    {
        $name = 'Zung depressiya shkalasi';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::ScoreScale, 4);
        $quiz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillScaleQuestions($quiz);
        $this->addZungRanges($category);
        $this->addZungInterpretations($category);
        $this->persist($category, [$quiz]);

        return $name;
    }

    private function fillScaleQuestions(Quiz $quiz): void
    {
        $position = 0;

        foreach ($this->zung->questions() as $questionData) {
            $position++;
            $question = $this->factory->createQuestion($quiz, QuestionType::Scale, $questionData['text'], $position);
            $question->setIsReversed($questionData['reversed']);
            $score = 0;

            foreach (ZungData::OPTIONS as $label) {
                $score++;
                $this->factory->createOption($question, $label, $score, null);
            }

            $quiz->addQuestion($question);
        }
    }

    private function addZungRanges(Category $category): void
    {
        foreach ($this->zung->ranges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key']);
        }
    }

    private function addZungInterpretations(Category $category): void
    {
        foreach ($this->zung->interpretations() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Uzbek, $data['title'], $data['text']);
        }
    }

    /**
     * @param array<string, string> $texts
     */
    private function addInterpretations(Category $category, StudyLanguage $language, array $texts): void
    {
        foreach ($texts as $resultKey => $text) {
            $this->factory->createInterpretation($category, $resultKey, $language, $resultKey, $text);
        }
    }

    /**
     * @param list<Quiz> $quizzes
     */
    private function persist(Category $category, array $quizzes): void
    {
        $this->categoryManager->save($category);

        foreach ($quizzes as $quiz) {
            $this->quizManager->save($quiz);
        }

        $this->quizManager->save($quizzes[array_key_last($quizzes)], true);
    }

    private function exists(string $name): bool
    {
        return $this->categoryRepository->findOneBy(['name' => $name]) !== null;
    }
}
