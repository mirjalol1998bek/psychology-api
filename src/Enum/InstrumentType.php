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
    /** Xuddi shu — QY-16 (qadriyat yo'nalishlari, ranjirlash) uchun. Savol
     * javobi "variant tanlash" emas — har savol bitta qadriyat, variantlari
     * 1-16 o'rin, `AnswerOption.score` = tanlangan o'rin raqami. UMUMIY
     * BALLSIZ (KSM-20 kabi): natija faqat 4 blokning o'rinlar yig'indisi
     * (kichik yig'indi = ustuvor blok). Frontendda maxsus "tartiblash"
     * interfeysi bilan render qilinadi (scale_choice emas). */
    case ScoreScaleValues = 'SCORE_SCALE_VALUES';
    /** Dembo-Rubinshteyn shkalalari — `ScoreScaleScorer`dan MUSTAQIL, o'z
     * `DemboRubinsteinScorer`i bilan. Savollar variant emas, har biri bitta
     * chiziq — javob ikkita 0-100 qiymat (`SLIDER_DUAL`). Natija: o'zini
     * baholash (OB) va da'vogarlik (DD) o'rtachasi + ular farqi — 3 mustaqil
     * ko'rsatkich, umumiy ball yo'q (KSM-20/QY-16 kabi). */
    case DemboRubinstein = 'DEMBO_RUBINSTEIN';
}
