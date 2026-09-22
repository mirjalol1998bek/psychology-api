<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

use App\Component\Assessment\AssessmentFactory;
use App\Component\Assessment\CategoryManager;
use App\Component\Assessment\QuizManager;
use App\Component\Assessment\Scoring\ScoreScaleScorer;
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
        private readonly Ipm20Data $ipm20,
        private readonly Okm20Data $okm20,
        private readonly Ehs20Data $ehs20,
        private readonly Ksm20Data $ksm20,
        private readonly Xo20Data $xo20,
        private readonly Qy16Data $qy16,
        private readonly DemboRubinsteinData $demboRubinstein,
        private readonly SociometryData $sociometry,
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
        $created[] = $this->seedIpm20();
        $created[] = $this->seedOkm20();
        $created[] = $this->seedEhs20();
        $created[] = $this->seedKsm20();
        $created[] = $this->seedXo20();
        $created[] = $this->seedQy16();
        $created[] = $this->seedDemboRubinstein();
        $created[] = $this->seedSociometry();

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

    private function seedIpm20(): ?string
    {
        $name = 'IPM-20: Universitetga ijtimoiy-psixologik moslashuv so\'rovnomasi';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::ScoreScaleSubscale, 5);
        $quizUz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillIpm20Questions($quizUz, $this->ipm20->questionsUz(), Ipm20Data::OPTIONS_UZ);
        $quizRu = $this->factory->createQuiz(
            $category,
            'ОСПА-20: Опросник социально-психологической адаптации к университету',
            StudyLanguage::Russian,
        );
        $this->fillIpm20Questions($quizRu, $this->ipm20->questionsRu(), Ipm20Data::OPTIONS_RU);

        $this->addIpm20Ranges($category);
        $this->addIpm20Interpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    /**
     * @param list<array{text: string, reversed: bool, subscale: string}> $questions
     * @param list<string>                                                $options
     */
    private function fillIpm20Questions(Quiz $quiz, array $questions, array $options): void
    {
        $position = 0;

        foreach ($questions as $questionData) {
            $position++;
            $question = $this->factory->createQuestion($quiz, QuestionType::Scale, $questionData['text'], $position, $questionData['subscale']);
            $question->setIsReversed($questionData['reversed']);
            $score = 0;

            foreach ($options as $label) {
                $score++;
                $this->factory->createOption($question, $label, $score, null);
            }

            $quiz->addQuestion($question);
        }
    }

    private function addIpm20Ranges(Category $category): void
    {
        foreach ($this->ipm20->overallRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key']);
        }

        foreach ($this->ipm20->subscaleRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], '*');
        }
    }

    private function addIpm20Interpretations(Category $category): void
    {
        foreach ($this->ipm20->overallInterpretationsUz() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Uzbek, $data['title'], $data['text']);
        }

        foreach ($this->ipm20->overallInterpretationsRu() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Russian, $data['title'], $data['text']);
        }

        foreach ($this->ipm20->subscaleInterpretationsUz() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Uzbek, $data['title'], $data['text'], '*');
        }

        foreach ($this->ipm20->subscaleInterpretationsRu() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Russian, $data['title'], $data['text'], '*');
        }
    }

    private function seedOkm20(): ?string
    {
        $name = 'OKM-20: O\'quv-kasbiy motivatsiya so\'rovnomasi';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::ScoreScaleMotivation, 6);
        $quizUz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillOkm20Questions($quizUz, $this->okm20->questionsUz(), Okm20Data::OPTIONS_UZ);
        $quizRu = $this->factory->createQuiz(
            $category,
            'ОУПМ-20: Опросник учебно-профессиональной мотивации',
            StudyLanguage::Russian,
        );
        $this->fillOkm20Questions($quizRu, $this->okm20->questionsRu(), Okm20Data::OPTIONS_RU);

        $this->addOkm20Ranges($category);
        $this->addOkm20Interpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    /**
     * @param list<array{text: string, subscale: string, sign: int}> $questions
     * @param list<string>                                           $options
     */
    private function fillOkm20Questions(Quiz $quiz, array $questions, array $options): void
    {
        $position = 0;

        foreach ($questions as $questionData) {
            $position++;
            $question = $this->factory->createQuestion(
                $quiz,
                QuestionType::Scale,
                $questionData['text'],
                $position,
                $questionData['subscale'],
                $questionData['sign'],
            );
            $score = 0;

            foreach ($options as $label) {
                $score++;
                $this->factory->createOption($question, $label, $score, null);
            }

            $quiz->addQuestion($question);
        }
    }

    private function addOkm20Ranges(Category $category): void
    {
        foreach ($this->okm20->overallRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key']);
        }

        foreach ($this->okm20->subscaleRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], '*');
        }
    }

    private function addOkm20Interpretations(Category $category): void
    {
        foreach ($this->okm20->overallInterpretationsUz() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Uzbek, $data['title'], $data['text']);
        }

        foreach ($this->okm20->overallInterpretationsRu() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Russian, $data['title'], $data['text']);
        }

        foreach ($this->okm20->subscaleInterpretationsUz() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Uzbek, $data['title'], $data['text'], '*');
        }

        foreach ($this->okm20->subscaleInterpretationsRu() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Russian, $data['title'], $data['text'], '*');
        }
    }

    private function seedEhs20(): ?string
    {
        $name = 'EHS-20: Emotsional holat va stressga chidamlilik so\'rovnomasi';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::ScoreScaleEmotional, 7);
        $quizUz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillEhs20Questions($quizUz, $this->ehs20->questionsUz(), Ehs20Data::OPTIONS_UZ);
        $quizRu = $this->factory->createQuiz(
            $category,
            'ЭСС-20: Опросник эмоционального состояния и стрессоустойчивости',
            StudyLanguage::Russian,
        );
        $this->fillEhs20Questions($quizRu, $this->ehs20->questionsRu(), Ehs20Data::OPTIONS_RU);

        $this->addEhs20Ranges($category);
        $this->addEhs20Interpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    /**
     * @param list<array{text: string, subscale: string, sign: int, rangeKey: string}> $questions
     * @param list<string>                                                             $options
     */
    private function fillEhs20Questions(Quiz $quiz, array $questions, array $options): void
    {
        $position = 0;

        foreach ($questions as $questionData) {
            $position++;
            $question = $this->factory->createQuestion(
                $quiz,
                QuestionType::Scale,
                $questionData['text'],
                $position,
                $questionData['subscale'],
                $questionData['sign'],
                $questionData['rangeKey'],
            );
            $score = 0;

            foreach ($options as $label) {
                $score++;
                $this->factory->createOption($question, $label, $score, null);
            }

            $quiz->addQuestion($question);
        }
    }

    private function addEhs20Ranges(Category $category): void
    {
        foreach ($this->ehs20->overallRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key']);
        }

        foreach ($this->ehs20->subscaleRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], $range['rangeKey']);
        }
    }

    private function addEhs20Interpretations(Category $category): void
    {
        foreach ($this->ehs20->overallInterpretationsUz() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Uzbek, $data['title'], $data['text']);
        }

        foreach ($this->ehs20->overallInterpretationsRu() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Russian, $data['title'], $data['text']);
        }

        foreach ($this->ehs20->subscaleInterpretationsUz() as $data) {
            $this->factory->createInterpretation($category, $data['key'], StudyLanguage::Uzbek, $data['title'], $data['text'], $data['rangeKey']);
        }

        foreach ($this->ehs20->subscaleInterpretationsRu() as $data) {
            $this->factory->createInterpretation($category, $data['key'], StudyLanguage::Russian, $data['title'], $data['text'], $data['rangeKey']);
        }
    }

    private function seedKsm20(): ?string
    {
        $name = 'KSM-20: Kommunikativ xususiyatlar va shaxslararo munosabatlar so\'rovnomasi';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::ScoreScaleCommunication, 8);
        $quizUz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillKsm20Questions($quizUz, $this->ksm20->questionsUz(), Ksm20Data::OPTIONS_UZ);
        $quizRu = $this->factory->createQuiz(
            $category,
            'КСМ-20: Опросник коммуникативных особенностей и межличностных отношений',
            StudyLanguage::Russian,
        );
        $this->fillKsm20Questions($quizRu, $this->ksm20->questionsRu(), Ksm20Data::OPTIONS_RU);

        $this->addKsm20Ranges($category);
        $this->addKsm20Interpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    /**
     * @param list<array{text: string, subscale: string, rangeKey: string}> $questions
     * @param list<string>                                                  $options
     */
    private function fillKsm20Questions(Quiz $quiz, array $questions, array $options): void
    {
        $position = 0;

        foreach ($questions as $questionData) {
            $position++;
            $question = $this->factory->createQuestion(
                $quiz,
                QuestionType::Scale,
                $questionData['text'],
                $position,
                $questionData['subscale'],
                1,
                $questionData['rangeKey'],
            );
            $score = 0;

            foreach ($options as $label) {
                $score++;
                $this->factory->createOption($question, $label, $score, null);
            }

            $quiz->addQuestion($question);
        }
    }

    /** KSM-20'da umumiy ScoreRange (subscaleKey='') ataylab yaratilmaydi —
     * shu holatni ScoreScaleScorer avtomatik aniqlab, umumiy ball o'rniga
     * faqat subshkala breakdown qaytaradi. */
    private function addKsm20Ranges(Category $category): void
    {
        foreach ($this->ksm20->subscaleRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], $range['rangeKey']);
        }
    }

    private function addKsm20Interpretations(Category $category): void
    {
        $noOverallUz = $this->ksm20->noOverallInterpretationUz();
        $this->factory->createInterpretation(
            $category,
            ScoreScaleScorer::NO_OVERALL_RESULT_KEY,
            StudyLanguage::Uzbek,
            $noOverallUz['title'],
            $noOverallUz['text'],
        );

        $noOverallRu = $this->ksm20->noOverallInterpretationRu();
        $this->factory->createInterpretation(
            $category,
            ScoreScaleScorer::NO_OVERALL_RESULT_KEY,
            StudyLanguage::Russian,
            $noOverallRu['title'],
            $noOverallRu['text'],
        );

        foreach ($this->ksm20->subscaleInterpretationsUz() as $data) {
            $this->factory->createInterpretation($category, $data['key'], StudyLanguage::Uzbek, $data['title'], $data['text'], $data['rangeKey']);
        }

        foreach ($this->ksm20->subscaleInterpretationsRu() as $data) {
            $this->factory->createInterpretation($category, $data['key'], StudyLanguage::Russian, $data['title'], $data['text'], $data['rangeKey']);
        }
    }

    private function seedXo20(): ?string
    {
        $name = 'XO-20: Xatar omillari skrining so\'rovnomasi';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::ScoreScaleRisk, 9);
        $quizUz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillXo20Questions($quizUz, $this->xo20->questionsUz(), Xo20Data::OPTIONS_UZ);
        $quizRu = $this->factory->createQuiz(
            $category,
            'ФР-20: Опросник скрининга факторов риска',
            StudyLanguage::Russian,
        );
        $this->fillXo20Questions($quizRu, $this->xo20->questionsRu(), Xo20Data::OPTIONS_RU);

        $this->addXo20Ranges($category);
        $this->addXo20Interpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    /**
     * @param list<array{text: string, subscale: string, rangeKey: string}> $questions
     * @param list<string>                                                  $options
     */
    private function fillXo20Questions(Quiz $quiz, array $questions, array $options): void
    {
        $position = 0;

        foreach ($questions as $questionData) {
            $position++;
            $question = $this->factory->createQuestion(
                $quiz,
                QuestionType::Scale,
                $questionData['text'],
                $position,
                $questionData['subscale'],
                1,
                $questionData['rangeKey'],
            );
            $score = 0;

            foreach ($options as $label) {
                $score++;
                $this->factory->createOption($question, $label, $score, null);
            }

            $quiz->addQuestion($question);
        }
    }

    private function addXo20Ranges(Category $category): void
    {
        foreach ($this->xo20->overallRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key']);
        }

        foreach ($this->xo20->subscaleRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], $range['rangeKey']);
        }
    }

    private function addXo20Interpretations(Category $category): void
    {
        foreach ($this->xo20->overallInterpretationsUz() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Uzbek, $data['title'], $data['text']);
        }

        foreach ($this->xo20->overallInterpretationsRu() as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Russian, $data['title'], $data['text']);
        }

        foreach ($this->xo20->subscaleInterpretationsUz() as $data) {
            $this->factory->createInterpretation($category, $data['key'], StudyLanguage::Uzbek, $data['title'], $data['text'], $data['rangeKey']);
        }

        foreach ($this->xo20->subscaleInterpretationsRu() as $data) {
            $this->factory->createInterpretation($category, $data['key'], StudyLanguage::Russian, $data['title'], $data['text'], $data['rangeKey']);
        }
    }

    private function seedQy16(): ?string
    {
        $name = 'QY-16: Qadriyat yo\'nalishlari so\'rovnomasi';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::ScoreScaleValues, 10);
        $quizUz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillQy16Questions($quizUz, $this->qy16->valuesUz(), $this->qy16->positionOptionsUz());
        $quizRu = $this->factory->createQuiz(
            $category,
            'ЦО-16: Опросник ценностных ориентаций',
            StudyLanguage::Russian,
        );
        $this->fillQy16Questions($quizRu, $this->qy16->valuesRu(), $this->qy16->positionOptionsRu());

        $this->addQy16Ranges($category);
        $this->addQy16Interpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    /**
     * @param list<array{text: string, block: string, rangeKey: string}> $values
     * @param list<string>                                                $positionOptions
     */
    private function fillQy16Questions(Quiz $quiz, array $values, array $positionOptions): void
    {
        $position = 0;

        foreach ($values as $valueData) {
            $position++;
            $question = $this->factory->createQuestion(
                $quiz,
                QuestionType::Scale,
                $valueData['text'],
                $position,
                $valueData['block'],
                1,
                $valueData['rangeKey'],
            );
            $rank = 0;

            foreach ($positionOptions as $label) {
                $rank++;
                $this->factory->createOption($question, $label, $rank, null);
            }

            $quiz->addQuestion($question);
        }
    }

    /** QY-16'da ham KSM-20'dagi kabi umumiy ScoreRange (subscaleKey='') ataylab
     * yaratilmaydi — 4 blokning har biri o'z o'rinlar yig'indisi bilan chiqadi. */
    private function addQy16Ranges(Category $category): void
    {
        foreach ($this->qy16->blockRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], $range['rangeKey']);
        }
    }

    private function addQy16Interpretations(Category $category): void
    {
        $noOverallUz = $this->qy16->noOverallInterpretationUz();
        $this->factory->createInterpretation(
            $category,
            ScoreScaleScorer::NO_OVERALL_RESULT_KEY,
            StudyLanguage::Uzbek,
            $noOverallUz['title'],
            $noOverallUz['text'],
        );

        $noOverallRu = $this->qy16->noOverallInterpretationRu();
        $this->factory->createInterpretation(
            $category,
            ScoreScaleScorer::NO_OVERALL_RESULT_KEY,
            StudyLanguage::Russian,
            $noOverallRu['title'],
            $noOverallRu['text'],
        );

        foreach ($this->qy16->blockInterpretationsUz() as $data) {
            $this->factory->createInterpretation($category, $data['key'], StudyLanguage::Uzbek, $data['title'], $data['text'], $data['rangeKey']);
        }

        foreach ($this->qy16->blockInterpretationsRu() as $data) {
            $this->factory->createInterpretation($category, $data['key'], StudyLanguage::Russian, $data['title'], $data['text'], $data['rangeKey']);
        }
    }

    private function seedDemboRubinstein(): ?string
    {
        $name = 'Dembo–Rubinshteyn shkalalari: o\'zini baholash va da\'vogarlik darajasi';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::DemboRubinstein, 11);
        $quizUz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillDemboRubinsteinQuestions($quizUz, $this->demboRubinstein->scalesUz());
        $quizRu = $this->factory->createQuiz(
            $category,
            'Шкалы Дембо–Рубинштейн: самооценка и уровень притязаний',
            StudyLanguage::Russian,
        );
        $this->fillDemboRubinsteinQuestions($quizRu, $this->demboRubinstein->scalesRu());

        $this->addDemboRubinsteinRanges($category);
        $this->addDemboRubinsteinInterpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    /**
     * @param list<array{text: string, include: bool}> $scales
     */
    private function fillDemboRubinsteinQuestions(Quiz $quiz, array $scales): void
    {
        $position = 0;

        foreach ($scales as $scaleData) {
            $position++;
            $question = $this->factory->createQuestion(
                $quiz,
                QuestionType::SliderDual,
                $scaleData['text'],
                $position,
                $scaleData['text'],
                $scaleData['include'] ? 1 : 0,
            );
            $quiz->addQuestion($question);
        }
    }

    private function addDemboRubinsteinRanges(Category $category): void
    {
        foreach ($this->demboRubinstein->obRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], 'ob');
        }

        foreach ($this->demboRubinstein->ddRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], 'dd');
        }

        foreach ($this->demboRubinstein->diffRanges() as $range) {
            $this->factory->createScoreRange($category, $range['min'], $range['max'], $range['key'], 'diff');
        }
    }

    private function addDemboRubinsteinInterpretations(Category $category): void
    {
        $noOverallUz = $this->demboRubinstein->noOverallInterpretationUz();
        $this->factory->createInterpretation(
            $category,
            ScoreScaleScorer::NO_OVERALL_RESULT_KEY,
            StudyLanguage::Uzbek,
            $noOverallUz['title'],
            $noOverallUz['text'],
        );

        $noOverallRu = $this->demboRubinstein->noOverallInterpretationRu();
        $this->factory->createInterpretation(
            $category,
            ScoreScaleScorer::NO_OVERALL_RESULT_KEY,
            StudyLanguage::Russian,
            $noOverallRu['title'],
            $noOverallRu['text'],
        );

        $this->addDemboRubinsteinGroupInterpretations($category, 'ob', $this->demboRubinstein->obInterpretationsUz(), $this->demboRubinstein->obInterpretationsRu());
        $this->addDemboRubinsteinGroupInterpretations($category, 'dd', $this->demboRubinstein->ddInterpretationsUz(), $this->demboRubinstein->ddInterpretationsRu());
        $this->addDemboRubinsteinGroupInterpretations($category, 'diff', $this->demboRubinstein->diffInterpretationsUz(), $this->demboRubinstein->diffInterpretationsRu());
    }

    /**
     * @param array<string, array{title: string, text: string}> $uz
     * @param array<string, array{title: string, text: string}> $ru
     */
    private function addDemboRubinsteinGroupInterpretations(Category $category, string $subscaleKey, array $uz, array $ru): void
    {
        foreach ($uz as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Uzbek, $data['title'], $data['text'], $subscaleKey);
        }

        foreach ($ru as $key => $data) {
            $this->factory->createInterpretation($category, $key, StudyLanguage::Russian, $data['title'], $data['text'], $subscaleKey);
        }
    }

    private function seedSociometry(): ?string
    {
        $name = 'Sotsiometrik tadqiqot';

        if ($this->exists($name)) {
            return null;
        }

        $category = $this->factory->createCategory($name, InstrumentType::Sociometry, 12);
        $quizUz = $this->factory->createQuiz($category, $name, StudyLanguage::Uzbek);
        $this->fillSociometryQuestions($quizUz, $this->sociometry->criteriaUz());
        $quizRu = $this->factory->createQuiz($category, 'Социометрическое исследование', StudyLanguage::Russian);
        $this->fillSociometryQuestions($quizRu, $this->sociometry->criteriaRu());

        $this->addSociometryInterpretations($category);
        $this->persist($category, [$quizUz, $quizRu]);

        return $name;
    }

    /**
     * @param list<string> $criteria
     */
    private function fillSociometryQuestions(Quiz $quiz, array $criteria): void
    {
        $position = 0;

        foreach ($criteria as $text) {
            $position++;
            $question = $this->factory->createQuestion($quiz, QuestionType::PeerChoice, $text, $position);
            $quiz->addQuestion($question);
        }
    }

    private function addSociometryInterpretations(Category $category): void
    {
        $uz = $this->sociometry->noOverallInterpretationUz();
        $this->factory->createInterpretation($category, ScoreScaleScorer::NO_OVERALL_RESULT_KEY, StudyLanguage::Uzbek, $uz['title'], $uz['text']);

        $ru = $this->sociometry->noOverallInterpretationRu();
        $this->factory->createInterpretation($category, ScoreScaleScorer::NO_OVERALL_RESULT_KEY, StudyLanguage::Russian, $ru['title'], $ru['text']);
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
