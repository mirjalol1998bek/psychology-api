<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

use App\Entity\Attempt;
use App\Enum\InstrumentType;

/**
 * Sotsiometriya — individual attempt uchun shaxsiy natija yo'q (talaba o'z
 * natijasini ko'rmaydi, faqat "javoblaringiz qabul qilindi" degan umumiy
 * matn — `ScoreScaleScorer::NO_OVERALL_RESULT_KEY` boshqa klassdan qayta
 * ishlatiladi, KSM-20/QY-16/Dembo-Rubinshteyn kabi). Haqiqiy tahlil — guruh
 * darajasida, barcha a'zolarning javoblarini birlashtirib
 * (`SociometryReporter`, faqat psixolog/admin uchun).
 */
final class SociometryScorer implements ScorerInterface
{
    public function supports(InstrumentType $instrumentType): bool
    {
        return $instrumentType === InstrumentType::Sociometry;
    }

    public function score(Attempt $attempt): ScoredResult
    {
        return new ScoredResult(ScoreScaleScorer::NO_OVERALL_RESULT_KEY, null, []);
    }
}
