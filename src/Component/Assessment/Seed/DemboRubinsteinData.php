<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

/**
 * Dembo-Rubinshteyn shkalalari — o'zini baholash (OB) va da'vogarlik (DD)
 * darajasi. 7 ta chiziq (shkala); 1-chizig'i (sog'liq) namoyish/demo
 * shkalasi — o'rtachaga qo'shilmaydi (`DemboRubinsteinScorer` buni
 * `Question.overallSign=0` orqali aniqlaydi). Har chiziqda talaba ikkita
 * belgi qo'yadi: hozirgi holati (OB, 0-100) va xohlagan darajasi (DD,
 * 0-100). Natija: OB o'rtachasi, DD o'rtachasi, ular farqi — 3 mustaqil
 * ko'rsatkich, umumiy ball yo'q.
 */
final class DemboRubinsteinData
{
    public const SCALE_HEALTH_UZ = 'Sog\'liq';
    public const SCALE_MIND_UZ = 'Aql, qobiliyat';
    public const SCALE_CHARACTER_UZ = 'Xarakter';
    public const SCALE_REPUTATION_UZ = 'Tengdoshlar orasidagi obro\'';
    public const SCALE_COMPETENCE_UZ = 'Ish qila olish, uddaburonlik';
    public const SCALE_APPEARANCE_UZ = 'Tashqi ko\'rinish';
    public const SCALE_CONFIDENCE_UZ = 'O\'ziga ishonch';

    public const SCALE_HEALTH_RU = 'Здоровье';
    public const SCALE_MIND_RU = 'Ум, способности';
    public const SCALE_CHARACTER_RU = 'Характер';
    public const SCALE_REPUTATION_RU = 'Авторитет среди сверстников';
    public const SCALE_COMPETENCE_RU = 'Умение многое делать своими руками, умелые руки';
    public const SCALE_APPEARANCE_RU = 'Внешность';
    public const SCALE_CONFIDENCE_RU = 'Уверенность в себе';

    /**
     * @return list<array{text: string, include: bool}>
     */
    public function scalesUz(): array
    {
        return [
            ['text' => self::SCALE_HEALTH_UZ, 'include' => false],
            ['text' => self::SCALE_MIND_UZ, 'include' => true],
            ['text' => self::SCALE_CHARACTER_UZ, 'include' => true],
            ['text' => self::SCALE_REPUTATION_UZ, 'include' => true],
            ['text' => self::SCALE_COMPETENCE_UZ, 'include' => true],
            ['text' => self::SCALE_APPEARANCE_UZ, 'include' => true],
            ['text' => self::SCALE_CONFIDENCE_UZ, 'include' => true],
        ];
    }

    /**
     * @return list<array{text: string, include: bool}>
     */
    public function scalesRu(): array
    {
        return [
            ['text' => self::SCALE_HEALTH_RU, 'include' => false],
            ['text' => self::SCALE_MIND_RU, 'include' => true],
            ['text' => self::SCALE_CHARACTER_RU, 'include' => true],
            ['text' => self::SCALE_REPUTATION_RU, 'include' => true],
            ['text' => self::SCALE_COMPETENCE_RU, 'include' => true],
            ['text' => self::SCALE_APPEARANCE_RU, 'include' => true],
            ['text' => self::SCALE_CONFIDENCE_RU, 'include' => true],
        ];
    }

    /**
     * @return array{title: string, text: string}
     */
    public function noOverallInterpretationUz(): array
    {
        return [
            'title' => 'O\'zini baholash va da\'vogarlik profili',
            'text' => 'Bitta umumiy ball hisoblanmaydi — natija 3 ko\'rsatkich bilan pastda ko\'rsatilgan: o\'zini baholash (OB), da\'vogarlik darajasi (DD) va ular orasidagi farq. Har biri alohida talqin qilinadi.',
        ];
    }

    /**
     * @return array{title: string, text: string}
     */
    public function noOverallInterpretationRu(): array
    {
        return [
            'title' => 'Профиль самооценки и уровня притязаний',
            'text' => 'Единый общий балл не подсчитывается — результат показан ниже по 3 показателям: самооценка (СО), уровень притязаний (УП) и разница между ними. Каждый интерпретируется отдельно.',
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function obRanges(): array
    {
        return [
            ['min' => 0, 'max' => 44, 'key' => 'low'],
            ['min' => 45, 'max' => 59, 'key' => 'medium'],
            ['min' => 60, 'max' => 74, 'key' => 'high'],
            ['min' => 75, 'max' => 100, 'key' => 'inadequate'],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function ddRanges(): array
    {
        return [
            ['min' => 0, 'max' => 59, 'key' => 'low'],
            ['min' => 60, 'max' => 74, 'key' => 'medium'],
            ['min' => 75, 'max' => 89, 'key' => 'high'],
            ['min' => 90, 'max' => 100, 'key' => 'unreal'],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function diffRanges(): array
    {
        return [
            ['min' => -100, 'max' => -1, 'key' => 'negative'],
            ['min' => 0, 'max' => 7, 'key' => 'small'],
            ['min' => 8, 'max' => 22, 'key' => 'optimal'],
            ['min' => 23, 'max' => 100, 'key' => 'sharp'],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function obInterpretationsUz(): array
    {
        return [
            'low' => ['title' => 'Past', 'text' => 'Shaxsiy noblagopoluchiye ko\'rsatkichi: o\'ziga ishonchsizlik, tashvish, ba\'zan himoya reaksiyasi. Interventsiya nishoni.'],
            'medium' => ['title' => 'O\'rta (norma)', 'text' => 'Barqaror, realistik o\'zini baholash.'],
            'high' => ['title' => 'Yuqori (adekvat)', 'text' => 'Real, ijobiy o\'zini baholash. Qulay variant.'],
            'inadequate' => ['title' => 'Yuqori / noadekvat', 'text' => 'O\'zini tanqidiy baholay olmaslik, shaxsiy yetuklikdagi buzilish belgisi bo\'lishi mumkin.'],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function obInterpretationsRu(): array
    {
        return [
            'low' => ['title' => 'Низкая', 'text' => 'Показатель личностного неблагополучия: неуверенность в себе, тревога, иногда защитная реакция. Мишень для вмешательства.'],
            'medium' => ['title' => 'Средняя (норма)', 'text' => 'Устойчивая, реалистичная самооценка.'],
            'high' => ['title' => 'Высокая (адекватная)', 'text' => 'Реальная, позитивная самооценка. Благоприятный вариант.'],
            'inadequate' => ['title' => 'Высокая / неадекватная', 'text' => 'Неспособность критично оценивать себя, может быть признаком нарушения личностной зрелости.'],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function ddInterpretationsUz(): array
    {
        return [
            'low' => ['title' => 'Past', 'text' => 'Rivojlanish uchun noqulay: talaba imkoniyatidan past maqsad qo\'yadi.'],
            'medium' => ['title' => 'O\'rta', 'text' => 'Maqbul, ammo intilish darajasini oshirish ustida ishlash mumkin.'],
            'high' => ['title' => 'Yuqori (realistik)', 'text' => 'Optimal variant: shaxsiy rivojlanishning ishonchli asosi.'],
            'unreal' => ['title' => 'Noreal yuqori', 'text' => 'Maqsadlarni qo\'yishdagi tanqidiylikning yo\'qligi; muvaffaqiyatsizlikka duch kelganda keskin tushkunlik xavfi.'],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function ddInterpretationsRu(): array
    {
        return [
            'low' => ['title' => 'Низкий', 'text' => 'Неблагоприятно для развития: студент ставит цель ниже своих возможностей.'],
            'medium' => ['title' => 'Средний', 'text' => 'Приемлемо, но можно работать над повышением уровня притязаний.'],
            'high' => ['title' => 'Высокий (реалистичный)', 'text' => 'Оптимальный вариант: надёжная основа личностного развития.'],
            'unreal' => ['title' => 'Нереально высокий', 'text' => 'Отсутствие критичности при постановке целей; риск резкого спада при неудаче.'],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function diffInterpretationsUz(): array
    {
        return [
            'negative' => ['title' => 'Manfiy farq', 'text' => 'O\'zini baholashning himoya xarakteridagi ko\'tarilishini bildiradi va albatta individual suhbatda tekshiriladi.'],
            'small' => ['title' => 'Past farq', 'text' => 'Rivojlanish istagi so\'ngan, o\'z-o\'zidan qoniqish holati.'],
            'optimal' => ['title' => 'Optimal', 'text' => 'O\'sish uchun sog\'lom zaxira mavjud.'],
            'sharp' => ['title' => 'Keskin nomuvofiqlik', 'text' => 'O\'z imkoniyatlarini baholay olmaslik, doimiy ichki ziddiyat va frustratsiya manbai.'],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function diffInterpretationsRu(): array
    {
        return [
            'negative' => ['title' => 'Отрицательная разница', 'text' => 'Означает защитное повышение самооценки, обязательно проверяется в индивидуальной беседе.'],
            'small' => ['title' => 'Малая разница', 'text' => 'Угасшее стремление к развитию, состояние самоудовлетворённости.'],
            'optimal' => ['title' => 'Оптимально', 'text' => 'Есть здоровый резерв для роста.'],
            'sharp' => ['title' => 'Резкое несоответствие', 'text' => 'Неспособность оценить свои возможности, источник постоянного внутреннего конфликта и фрустрации.'],
        ];
    }
}
