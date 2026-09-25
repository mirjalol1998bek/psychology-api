<?php

declare(strict_types=1);

namespace App\Component\Assessment;

use App\Component\Assessment\Scoring\CategoryTallyScorer;
use App\Entity\AssessmentInterpretation;
use App\Entity\Attempt;
use App\Repository\AssessmentInterpretationRepository;

/**
 * Natija kaliti → ko'rinadigan nom va izoh (`AssessmentInterpretation`dan).
 * Aralash temperamentda (`Flegmatik+Xolerik`) har bir tur o'z izohi bilan
 * ketma-ket beriladi — hech biri ustun qo'yilmaydi.
 */
final class ResultDescriber
{
    public function __construct(private readonly AssessmentInterpretationRepository $interpretationRepository)
    {
    }

    /**
     * @return array{label: string, description: string}
     */
    public function describe(Attempt $attempt, string $resultKey): array
    {
        $keys = explode(CategoryTallyScorer::MIXED_SEPARATOR, $resultKey);

        if (count($keys) === 1) {
            $interpretation = $this->find($attempt, $resultKey);

            return ['label' => $interpretation?->getTitle() ?? $resultKey, 'description' => $interpretation?->getText() ?? ''];
        }

        return $this->describeMixed($attempt, $keys);
    }

    /**
     * @param list<string> $keys
     * @return array{label: string, description: string}
     */
    private function describeMixed(Attempt $attempt, array $keys): array
    {
        $titles = [];
        $texts = [];

        foreach ($keys as $key) {
            $interpretation = $this->find($attempt, $key);
            $titles[] = $interpretation?->getTitle() ?? $key;
            $texts[] = end($titles) . "\n" . ($interpretation?->getText() ?? '');
        }

        return ['label' => implode(' + ', $titles), 'description' => implode("\n\n", $texts)];
    }

    private function find(Attempt $attempt, string $resultKey): ?AssessmentInterpretation
    {
        return $this->interpretationRepository->findOneBy([
            'category' => $attempt->getQuiz()->getCategory(),
            'subscaleKey' => '',
            'resultKey' => $resultKey,
            'studyLanguage' => $attempt->getStudyLanguage(),
        ]);
    }
}
