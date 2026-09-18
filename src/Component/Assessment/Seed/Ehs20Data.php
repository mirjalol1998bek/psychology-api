<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

/**
 * EHS-20 — emotsional holat va stressga chidamlilik so'rovnomasi. 3
 * subshkala TENG BO'LMAGAN o'lchamda: A/B (7 bayonot, 7–35) va C (6
 * bayonot, 6–30, mustaqil resurs shkalasi — teskari ballash yo'q).
 * Ishorali umumiy indeks: ERI = (A+B) − C (−16..+64).
 */
final class Ehs20Data
{
    public const SUBSCALE_ANXIETY_UZ = 'Tashvishlanish (xavotir)';
    public const SUBSCALE_FATIGUE_UZ = 'Emotsional charchoq va kayfiyat fon';
    public const SUBSCALE_RESILIENCE_UZ = 'Stressga chidamlilik va o\'z-o\'zini boshqarish';

    public const SUBSCALE_ANXIETY_RU = 'Тревожность (беспокойство)';
    public const SUBSCALE_FATIGUE_RU = 'Эмоциональная усталость и фон настроения';
    public const SUBSCALE_RESILIENCE_RU = 'Стрессоустойчивость и саморегуляция';

    /** A/B (7 bayonot, 7-35) uchun jadval kaliti — C dan farqli, chunki o'lchami boshqa. */
    public const RANGE_ANXIETY_FATIGUE = 'anxiety_fatigue';
    public const RANGE_RESILIENCE = 'resilience';

    public const OPTIONS_UZ = [
        '1 — mutlaqo to\'g\'ri kelmaydi',
        '2 — ko\'pincha to\'g\'ri kelmaydi',
        '3 — qisman to\'g\'ri keladi',
        '4 — ko\'pincha to\'g\'ri keladi',
        '5 — to\'liq to\'g\'ri keladi',
    ];

    public const OPTIONS_RU = [
        '1 — совершенно не соответствует',
        '2 — чаще не соответствует',
        '3 — отчасти соответствует',
        '4 — чаще соответствует',
        '5 — полностью соответствует',
    ];

    /**
     * @return list<array{text: string, subscale: string, sign: int, rangeKey: string}>
     */
    public function questionsUz(): array
    {
        return [
            ['text' => 'Ko\'p hollarda sababsiz xavotirlanaman.', 'subscale' => self::SUBSCALE_ANXIETY_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Kunning oxiriga borib o\'zimni butunlay holdan toygan his qilaman.', 'subscale' => self::SUBSCALE_FATIGUE_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Qiyin vaziyatlarda ham xotirjamlikni saqlay olaman.', 'subscale' => self::SUBSCALE_RESILIENCE_UZ, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Kelajakda yomon bir narsa yuz berishidan qo\'rqaman.', 'subscale' => self::SUBSCALE_ANXIETY_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Ilgari qiziqarli bo\'lgan narsalar endi meni qiziqtirmayapti.', 'subscale' => self::SUBSCALE_FATIGUE_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Asabiylashganimda o\'zimni tez qo\'lga ola bilaman.', 'subscale' => self::SUBSCALE_RESILIENCE_UZ, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Muhim voqealardan oldin qattiq hayajonlanaman va uxlay olmayman.', 'subscale' => self::SUBSCALE_ANXIETY_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Ko\'pincha kayfiyatim tushkun bo\'ladi.', 'subscale' => self::SUBSCALE_FATIGUE_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Muammo yuzaga kelganda uni hal qilish yo\'lini izlayman.', 'subscale' => self::SUBSCALE_RESILIENCE_UZ, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Boshqalarning men haqimdagi fikridan doim tashvishlanaman.', 'subscale' => self::SUBSCALE_ANXIETY_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'O\'zimni yolg\'iz his qilaman.', 'subscale' => self::SUBSCALE_FATIGUE_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Rejalarimni tuzib, ularga amal qila olaman.', 'subscale' => self::SUBSCALE_RESILIENCE_UZ, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Ichimda doimiy zo\'riqish borday tuyuladi.', 'subscale' => self::SUBSCALE_ANXIETY_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Ko\'p narsaga kuchim yetmayotganday tuyuladi.', 'subscale' => self::SUBSCALE_FATIGUE_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Tanqidni xotirjam qabul qila olaman.', 'subscale' => self::SUBSCALE_RESILIENCE_UZ, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Yangi vaziyatlarga tushganda o\'zimni noqulay his qilaman.', 'subscale' => self::SUBSCALE_ANXIETY_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Ertalab charchagan holda uyg\'onaman.', 'subscale' => self::SUBSCALE_FATIGUE_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Charchaganimda dam olish va tiklanish usullarimni bilaman.', 'subscale' => self::SUBSCALE_RESILIENCE_UZ, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Kichik xatolarim uchun ham uzoq vaqt o\'zimni ayblayman.', 'subscale' => self::SUBSCALE_ANXIETY_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'So\'nggi vaqtda meni hech narsa quvontirmayotganday.', 'subscale' => self::SUBSCALE_FATIGUE_UZ, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
        ];
    }

    /**
     * @return list<array{text: string, subscale: string, sign: int, rangeKey: string}>
     */
    public function questionsRu(): array
    {
        return [
            ['text' => 'Часто беспокоюсь без видимой причины.', 'subscale' => self::SUBSCALE_ANXIETY_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'К концу дня чувствую себя совершенно обессиленным(ой).', 'subscale' => self::SUBSCALE_FATIGUE_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Способен(на) сохранять спокойствие даже в трудных ситуациях.', 'subscale' => self::SUBSCALE_RESILIENCE_RU, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Боюсь, что в будущем произойдёт что-то плохое.', 'subscale' => self::SUBSCALE_ANXIETY_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'То, что раньше было интересным, теперь меня не привлекает.', 'subscale' => self::SUBSCALE_FATIGUE_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Когда нервничаю, быстро могу взять себя в руки.', 'subscale' => self::SUBSCALE_RESILIENCE_RU, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Перед важными событиями сильно волнуюсь и не могу уснуть.', 'subscale' => self::SUBSCALE_ANXIETY_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Часто у меня подавленное настроение.', 'subscale' => self::SUBSCALE_FATIGUE_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Когда возникает проблема, ищу способ её решения.', 'subscale' => self::SUBSCALE_RESILIENCE_RU, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Постоянно беспокоюсь о том, что обо мне думают другие.', 'subscale' => self::SUBSCALE_ANXIETY_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Чувствую себя одиноким(ой).', 'subscale' => self::SUBSCALE_FATIGUE_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Умею составлять планы и следовать им.', 'subscale' => self::SUBSCALE_RESILIENCE_RU, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Ощущаю постоянное внутреннее напряжение.', 'subscale' => self::SUBSCALE_ANXIETY_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Кажется, что на многое не хватает сил.', 'subscale' => self::SUBSCALE_FATIGUE_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Способен(на) спокойно воспринимать критику.', 'subscale' => self::SUBSCALE_RESILIENCE_RU, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'В новых ситуациях чувствую себя некомфортно.', 'subscale' => self::SUBSCALE_ANXIETY_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Просыпаюсь по утрам уставшим(ей).', 'subscale' => self::SUBSCALE_FATIGUE_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'Знаю способы отдыха и восстановления, когда устаю.', 'subscale' => self::SUBSCALE_RESILIENCE_RU, 'sign' => -1, 'rangeKey' => self::RANGE_RESILIENCE],
            ['text' => 'Долго виню себя даже за мелкие ошибки.', 'subscale' => self::SUBSCALE_ANXIETY_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['text' => 'В последнее время меня ничто не радует.', 'subscale' => self::SUBSCALE_FATIGUE_RU, 'sign' => 1, 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function overallRanges(): array
    {
        return [
            ['min' => -16, 'max' => 20, 'key' => 'no_risk'],
            ['min' => 21, 'max' => 35, 'key' => 'moderate'],
            ['min' => 36, 'max' => 50, 'key' => 'high'],
            ['min' => 51, 'max' => 64, 'key' => 'very_high'],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string, rangeKey: string}>
     */
    public function subscaleRanges(): array
    {
        return [
            ['min' => 7, 'max' => 14, 'key' => 'low', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['min' => 15, 'max' => 21, 'key' => 'medium', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['min' => 22, 'max' => 28, 'key' => 'high', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['min' => 29, 'max' => 35, 'key' => 'very_high', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['min' => 6, 'max' => 13, 'key' => 'low', 'rangeKey' => self::RANGE_RESILIENCE],
            ['min' => 14, 'max' => 21, 'key' => 'medium', 'rangeKey' => self::RANGE_RESILIENCE],
            ['min' => 22, 'max' => 30, 'key' => 'high', 'rangeKey' => self::RANGE_RESILIENCE],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function overallInterpretationsUz(): array
    {
        return [
            'no_risk' => [
                'title' => 'Xavf yo\'q',
                'text' => 'Umumiy profilaktika.',
            ],
            'moderate' => [
                'title' => 'O\'rtacha zo\'riqish',
                'text' => 'Guruhiy trening: stressni boshqarish, vaqt menejmenti.',
            ],
            'high' => [
                'title' => 'Yuqori zo\'riqish',
                'text' => 'Individual suhbat, 2 haftada bir marta kuzatuv, dinamikani qayd etish.',
            ],
            'very_high' => [
                'title' => 'Juda yuqori zo\'riqish',
                'text' => 'Kechiktirilmaydigan individual ish, oilaviy kontekstni o\'rganish, mutaxassisga yo\'llash masalasini ko\'rib chiqish.',
            ],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function overallInterpretationsRu(): array
    {
        return [
            'no_risk' => [
                'title' => 'Риска нет',
                'text' => 'Общая профилактика.',
            ],
            'moderate' => [
                'title' => 'Умеренное напряжение',
                'text' => 'Групповой тренинг: управление стрессом, тайм-менеджмент.',
            ],
            'high' => [
                'title' => 'Высокое напряжение',
                'text' => 'Индивидуальная беседа, наблюдение раз в 2 недели, фиксация динамики.',
            ],
            'very_high' => [
                'title' => 'Очень высокое напряжение',
                'text' => 'Неотложная индивидуальная работа, изучение семейного контекста, рассмотрение направления к специалисту.',
            ],
        ];
    }

    /**
     * @return list<array{key: string, title: string, text: string, rangeKey: string}>
     */
    public function subscaleInterpretationsUz(): array
    {
        return [
            ['key' => 'low', 'title' => 'Past', 'text' => 'Emotsional holat barqaror, tashvish darajasi normal chegarada.', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['key' => 'medium', 'title' => 'O\'rta', 'text' => 'Vaziyatga bog\'liq zo\'riqish. Odatiy o\'quv stressining ko\'rinishi.', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['key' => 'high', 'title' => 'Yuqori', 'text' => 'Barqaror emotsional zo\'riqish. Individual suhbat va stressni boshqarish bo\'yicha trening tavsiya etiladi.', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['key' => 'very_high', 'title' => 'Juda yuqori', 'text' => 'Aniq emotsional zo\'riqish. Individual psixologik ish majburiy; ko\'rsatma bo\'lganda tegishli tibbiy mutaxassisga yo\'naltiriladi.', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['key' => 'low', 'title' => 'Past', 'text' => 'Kutilmagan vaziyatlarda qo\'l qovushtirib qolish, o\'zini boshqarish ko\'nikmalarining yetishmasligi. Bu — asosiy interventsiya nishoni.', 'rangeKey' => self::RANGE_RESILIENCE],
            ['key' => 'medium', 'title' => 'O\'rta', 'text' => 'Chidamlilik yetarli, ammo uzoq muddatli yuklamada tugab qolishi mumkin.', 'rangeKey' => self::RANGE_RESILIENCE],
            ['key' => 'high', 'title' => 'Yuqori', 'text' => 'Kuchli shaxsiy resurs. Bunday talabalar tengdoshlar yordami (peer support) tizimiga jalb qilinishi mumkin.', 'rangeKey' => self::RANGE_RESILIENCE],
        ];
    }

    /**
     * @return list<array{key: string, title: string, text: string, rangeKey: string}>
     */
    public function subscaleInterpretationsRu(): array
    {
        return [
            ['key' => 'low', 'title' => 'Низкий', 'text' => 'Эмоциональное состояние стабильно, уровень тревоги в пределах нормы.', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['key' => 'medium', 'title' => 'Средний', 'text' => 'Напряжение, зависящее от ситуации. Проявление обычного учебного стресса.', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['key' => 'high', 'title' => 'Высокий', 'text' => 'Устойчивое эмоциональное напряжение. Рекомендуется индивидуальная беседа и тренинг по управлению стрессом.', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['key' => 'very_high', 'title' => 'Очень высокий', 'text' => 'Выраженное эмоциональное напряжение. Обязательна индивидуальная психологическая работа; при показаниях — направление к профильному специалисту.', 'rangeKey' => self::RANGE_ANXIETY_FATIGUE],
            ['key' => 'low', 'title' => 'Низкий', 'text' => 'Растерянность в неожиданных ситуациях, недостаток навыков саморегуляции. Это — основная цель вмешательства.', 'rangeKey' => self::RANGE_RESILIENCE],
            ['key' => 'medium', 'title' => 'Средний', 'text' => 'Устойчивости достаточно, но при длительной нагрузке может истощаться.', 'rangeKey' => self::RANGE_RESILIENCE],
            ['key' => 'high', 'title' => 'Высокий', 'text' => 'Сильный личностный ресурс. Такие студенты могут быть вовлечены в систему поддержки сверстников (peer support).', 'rangeKey' => self::RANGE_RESILIENCE],
        ];
    }
}
