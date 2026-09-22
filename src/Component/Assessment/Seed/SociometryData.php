<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

/**
 * 7-metodika: Sotsiometrik tadqiqot. 3 mezon (savol) — talaba har biriga
 * guruhdoshlaridan ≤3 kishini afzallik tartibida tanlaydi (`PEER_CHOICE`,
 * variantsiz). Individual natija yo'q — tahlil guruh darajasida
 * (`SociometryReporter`). Salbiy tanlovlar (rasmiy hujjatda "zarurat
 * bo'lganda" ixtiyoriy qo'shiladigan qism) v1'da kiritilmagan — hujjatning
 * o'zi buni standart emas, istisno holat sifatida ta'riflaydi.
 */
final class SociometryData
{
    public const CRITERION_ACADEMIC_UZ = 'Murakkab o\'quv topshirig\'ini yoki ilmiy loyihani kim bilan birga bajarishni istardingiz?';
    public const CRITERION_LEISURE_UZ = 'Bo\'sh vaqtingizni, sayohat yoki tadbirni kim bilan birga o\'tkazishni istardingiz?';
    public const CRITERION_TRUST_UZ = 'Shaxsiy tashvishingizni guruhdan kimga ishonib aytishingiz mumkin?';

    public const CRITERION_ACADEMIC_RU = 'С кем из группы вы хотели бы вместе выполнить сложное учебное задание или научный проект?';
    public const CRITERION_LEISURE_RU = 'С кем из группы вы хотели бы вместе провести свободное время, путешествие или мероприятие?';
    public const CRITERION_TRUST_RU = 'Кому из группы вы могли бы доверить личное беспокойство?';

    /**
     * @return list<string>
     */
    public function criteriaUz(): array
    {
        return [self::CRITERION_ACADEMIC_UZ, self::CRITERION_LEISURE_UZ, self::CRITERION_TRUST_UZ];
    }

    /**
     * @return list<string>
     */
    public function criteriaRu(): array
    {
        return [self::CRITERION_ACADEMIC_RU, self::CRITERION_LEISURE_RU, self::CRITERION_TRUST_RU];
    }

    /**
     * @return array{title: string, text: string}
     */
    public function noOverallInterpretationUz(): array
    {
        return [
            'title' => 'Rahmat!',
            'text' => 'Javoblaringiz muvaffaqiyatli qabul qilindi.',
        ];
    }

    /**
     * @return array{title: string, text: string}
     */
    public function noOverallInterpretationRu(): array
    {
        return [
            'title' => 'Спасибо!',
            'text' => 'Ваши ответы успешно приняты.',
        ];
    }
}
