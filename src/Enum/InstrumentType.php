<?php

declare(strict_types=1);

namespace App\Enum;

enum InstrumentType: string
{
    case TemperamentStatements = 'TEMPERAMENT_STATEMENTS';
    case TemperamentChoice = 'TEMPERAMENT_CHOICE';
    case FigureChoice = 'FIGURE_CHOICE';
    case ScoreScale = 'SCORE_SCALE';
    /** `ScoreScale` bilan bir xil ballash (`ScoreScaleScorer`) — faqat frontendga
     * subshkalali metodikani (IPM-20) alohida "test" sifatida ajratish uchun. */
    case ScoreScaleSubscale = 'SCORE_SCALE_SUBSCALE';
    /** Xuddi shu — OKM-20 uchun (subshkalalar + ishorali umumiy indeks: IMI = (A+B)-(C+D)). */
    case ScoreScaleMotivation = 'SCORE_SCALE_MOTIVATION';
    /** Xuddi shu — EHS-20 uchun (teng bo'lmagan subshkalalar + ERI = (A+B)-C). */
    case ScoreScaleEmotional = 'SCORE_SCALE_EMOTIONAL';
    /** Xuddi shu — XO-20 uchun (5 subshkala + oddiy umumiy yig'indi, IPM-20 kabi). */
    case ScoreScaleRisk = 'SCORE_SCALE_RISK';
    /** Xuddi shu — KSM-20 uchun, lekin UMUMIY BALLSIZ: faqat 4 subshkala
     * (birining o'zi — konfliktlilik — teskari yo'nalishda). Ballash
     * mexanizmi bir xil, faqat kategoriya uchun umumiy ScoreRange
     * yaratilmaydi — ScoreScaleScorer buni avtomatik aniqlab, umumiy ball
     * o'rniga faqat subshkala breakdown qaytaradi. */
    case ScoreScaleCommunication = 'SCORE_SCALE_COMMUNICATION';
}
