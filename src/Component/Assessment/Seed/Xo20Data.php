<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

/**
 * XO-20 — xatar omillari skrining so'rovnomasi. 5 subshkala (har biri 4
 * bayonot, 4-20 ball) + oddiy umumiy yig'indi (UMUMIY XATAR INDEKSI,
 * 20-100). Teskari ballanadigan savol yo'q. A-D bitta umumiy subshkala
 * jadvalini bo'lishadi; E (psixologik yordamga ehtiyoj) — bandning
 * "15+ bo'lsa majburiy individual suhbat" degan alohida qat'iy qoidasi
 * borligi uchun o'z jadvaliga ega.
 */
final class Xo20Data
{
    public const SUBSCALE_NORMS_UZ = 'Ijtimoiy normalarga munosabat';
    public const SUBSCALE_AGGRESSION_UZ = 'Agressivlik va o\'zini tuta olmaslik';
    public const SUBSCALE_ADDICTION_UZ = 'Zararli odatlar va bog\'liqliklarga moyillik';
    public const SUBSCALE_ISOLATION_UZ = 'Ijtimoiy izolyatsiya';
    public const SUBSCALE_PSYCH_NEED_UZ = 'Psixologik yordamga ehtiyoj';

    public const SUBSCALE_NORMS_RU = 'Отношение к социальным нормам';
    public const SUBSCALE_AGGRESSION_RU = 'Агрессивность и несдержанность';
    public const SUBSCALE_ADDICTION_RU = 'Склонность к вредным привычкам и зависимостям';
    public const SUBSCALE_ISOLATION_RU = 'Социальная изоляция';
    public const SUBSCALE_PSYCH_NEED_RU = 'Потребность в психологической помощи';

    /** A-D (4 bayonot, 4-20) uchun jadval kaliti — E dan farqli, chunki
     * E'ning yuqori bandlarida qat'iy "individual suhbat" qoidasi bor. */
    public const RANGE_RISK = 'risk';
    public const RANGE_PSYCH_NEED = 'psych_need';

    public const OPTIONS_UZ = [
        '1 — menga mutlaqo to\'g\'ri kelmaydi',
        '2 — ko\'pincha to\'g\'ri kelmaydi',
        '3 — qisman to\'g\'ri keladi',
        '4 — ko\'pincha to\'g\'ri keladi',
        '5 — menga to\'liq to\'g\'ri keladi',
    ];

    public const OPTIONS_RU = [
        '1 — совершенно не соответствует мне',
        '2 — чаще не соответствует мне',
        '3 — отчасти соответствует мне',
        '4 — чаще соответствует мне',
        '5 — полностью соответствует мне',
    ];

    /**
     * @return list<array{text: string, subscale: string, rangeKey: string}>
     */
    public function questionsUz(): array
    {
        return [
            ['text' => 'Qoidalarni buzish ba\'zi hollarda oqlanadi deb o\'ylayman.', 'subscale' => self::SUBSCALE_NORMS_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Jahlim chiqqanda o\'zimni tuta olmayman.', 'subscale' => self::SUBSCALE_AGGRESSION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Atrofimda zararli odatlarga berilgan tanishlarim bor.', 'subscale' => self::SUBSCALE_ADDICTION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Universitetda men bilan samimiy gaplashadigan odam yo\'q.', 'subscale' => self::SUBSCALE_ISOLATION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'So\'nggi paytda ruhiy holatim meni tashvishga solmoqda.', 'subscale' => self::SUBSCALE_PSYCH_NEED_UZ, 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['text' => 'Intizomiy ogohlantirish yoki tanbeh olgan holatlarim bo\'lgan.', 'subscale' => self::SUBSCALE_NORMS_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Bahs-munozara janjalga aylanib ketgan holatlar bo\'lgan.', 'subscale' => self::SUBSCALE_AGGRESSION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Charchoq yoki tashvishni chalg\'itish uchun turli vositalarga murojaat qilgim keladi.', 'subscale' => self::SUBSCALE_ADDICTION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Bo\'sh vaqtimni asosan yolg\'iz o\'tkazaman.', 'subscale' => self::SUBSCALE_ISOLATION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Muammolarimni hech kimga aytolmayman.', 'subscale' => self::SUBSCALE_PSYCH_NEED_UZ, 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['text' => 'Darslarni sababsiz qoldirgan holatlarim bo\'lgan.', 'subscale' => self::SUBSCALE_NORMS_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Ba\'zan atrofdagilarga qo\'pol muomala qilaman.', 'subscale' => self::SUBSCALE_AGGRESSION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Kechalari uzoq vaqt telefon yoki o\'yin bilan band bo\'lib, uyqum buziladi.', 'subscale' => self::SUBSCALE_ADDICTION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Guruhda meni tushunmaydilar deb hisoblayman.', 'subscale' => self::SUBSCALE_ISOLATION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Psixologga murojaat qilish kerakligi haqida o\'ylaganman.', 'subscale' => self::SUBSCALE_PSYCH_NEED_UZ, 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['text' => 'Universitet talablarini ortiqcha va asossiz deb hisoblayman.', 'subscale' => self::SUBSCALE_NORMS_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Kayfiyatim kun davomida keskin o\'zgarib turadi.', 'subscale' => self::SUBSCALE_AGGRESSION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Vaqtimning katta qismi ijtimoiy tarmoqlarda o\'tadi va buni nazorat qila olmayman.', 'subscale' => self::SUBSCALE_ADDICTION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'O\'zimni jamoadan chetda his qilaman.', 'subscale' => self::SUBSCALE_ISOLATION_UZ, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Menga hozir yordam va qo\'llab-quvvatlash zarur.', 'subscale' => self::SUBSCALE_PSYCH_NEED_UZ, 'rangeKey' => self::RANGE_PSYCH_NEED],
        ];
    }

    /**
     * @return list<array{text: string, subscale: string, rangeKey: string}>
     */
    public function questionsRu(): array
    {
        return [
            ['text' => 'Я считаю, что нарушение правил иногда оправдано.', 'subscale' => self::SUBSCALE_NORMS_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Когда я злюсь, я не могу сдержать себя.', 'subscale' => self::SUBSCALE_AGGRESSION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Среди моих знакомых есть люди с вредными привычками.', 'subscale' => self::SUBSCALE_ADDICTION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'В университете нет человека, с которым я могу искренне поговорить.', 'subscale' => self::SUBSCALE_ISOLATION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'В последнее время моё психическое состояние вызывает у меня тревогу.', 'subscale' => self::SUBSCALE_PSYCH_NEED_RU, 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['text' => 'У меня были случаи дисциплинарных замечаний или выговоров.', 'subscale' => self::SUBSCALE_NORMS_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Бывали случаи, когда спор перерастал в конфликт.', 'subscale' => self::SUBSCALE_AGGRESSION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Мне хочется прибегать к разным средствам, чтобы отвлечься от усталости или тревоги.', 'subscale' => self::SUBSCALE_ADDICTION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Свободное время я провожу в основном в одиночестве.', 'subscale' => self::SUBSCALE_ISOLATION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Я не могу никому рассказать о своих проблемах.', 'subscale' => self::SUBSCALE_PSYCH_NEED_RU, 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['text' => 'Бывали случаи, когда я пропускал(а) занятия без причины.', 'subscale' => self::SUBSCALE_NORMS_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Иногда я грубо обращаюсь с окружающими.', 'subscale' => self::SUBSCALE_AGGRESSION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'По ночам я долго провожу время с телефоном или играми, из-за чего нарушается сон.', 'subscale' => self::SUBSCALE_ADDICTION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Я считаю, что в группе меня не понимают.', 'subscale' => self::SUBSCALE_ISOLATION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Я думал(а) о том, что мне нужно обратиться к психологу.', 'subscale' => self::SUBSCALE_PSYCH_NEED_RU, 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['text' => 'Я считаю требования университета чрезмерными и необоснованными.', 'subscale' => self::SUBSCALE_NORMS_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Моё настроение резко меняется в течение дня.', 'subscale' => self::SUBSCALE_AGGRESSION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Большая часть моего времени уходит на социальные сети, и я не могу это контролировать.', 'subscale' => self::SUBSCALE_ADDICTION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Я чувствую себя отстранённым(ой) от коллектива.', 'subscale' => self::SUBSCALE_ISOLATION_RU, 'rangeKey' => self::RANGE_RISK],
            ['text' => 'Мне сейчас нужна помощь и поддержка.', 'subscale' => self::SUBSCALE_PSYCH_NEED_RU, 'rangeKey' => self::RANGE_PSYCH_NEED],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function overallRanges(): array
    {
        return [
            ['min' => 20, 'max' => 35, 'key' => 'low'],
            ['min' => 36, 'max' => 55, 'key' => 'medium'],
            ['min' => 56, 'max' => 75, 'key' => 'high'],
            ['min' => 76, 'max' => 100, 'key' => 'very_high'],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string, rangeKey: string}>
     */
    public function subscaleRanges(): array
    {
        return [
            ['min' => 4, 'max' => 7, 'key' => 'low', 'rangeKey' => self::RANGE_RISK],
            ['min' => 8, 'max' => 11, 'key' => 'medium', 'rangeKey' => self::RANGE_RISK],
            ['min' => 12, 'max' => 15, 'key' => 'high', 'rangeKey' => self::RANGE_RISK],
            ['min' => 16, 'max' => 20, 'key' => 'very_high', 'rangeKey' => self::RANGE_RISK],
            ['min' => 4, 'max' => 7, 'key' => 'low', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['min' => 8, 'max' => 11, 'key' => 'medium', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['min' => 12, 'max' => 15, 'key' => 'high', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['min' => 16, 'max' => 20, 'key' => 'very_high', 'rangeKey' => self::RANGE_PSYCH_NEED],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function overallInterpretationsUz(): array
    {
        return [
            'low' => [
                'title' => 'Past',
                'text' => 'Maxsus chora talab qilinmaydi. Umumiy profilaktik tadbirlar.',
            ],
            'medium' => [
                'title' => 'O\'rta',
                'text' => 'E\'tibor guruhi. Guruhiy profilaktik trening, kurator kuzatuvi, 3 oydan keyin qayta o\'lchash.',
            ],
            'high' => [
                'title' => 'Yuqori',
                'text' => 'Xavf guruhi. Individual suhbat majburiy, individual ish rejasi tuziladi, oylik monitoring.',
            ],
            'very_high' => [
                'title' => 'Juda yuqori',
                'text' => 'Kechiktirilmaydigan individual ish. Yoshlar bilan ishlash bo\'limi va kurator xabardor qilinadi (natijalar mazmunini oshkor qilmagan holda), zarurat bo\'lsa tegishli mutaxassisga yo\'llanma beriladi.',
            ],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function overallInterpretationsRu(): array
    {
        return [
            'low' => [
                'title' => 'Низкий',
                'text' => 'Особые меры не требуются. Общие профилактические мероприятия.',
            ],
            'medium' => [
                'title' => 'Средний',
                'text' => 'Группа внимания. Групповой профилактический тренинг, наблюдение куратора, повторное измерение через 3 месяца.',
            ],
            'high' => [
                'title' => 'Высокий',
                'text' => 'Группа риска. Обязательна индивидуальная беседа, составляется план индивидуальной работы, ежемесячный мониторинг.',
            ],
            'very_high' => [
                'title' => 'Очень высокий',
                'text' => 'Немедленная индивидуальная работа. Информируются отдел по работе с молодёжью и куратор (без разглашения содержания результатов), при необходимости выдаётся направление к специалисту.',
            ],
        ];
    }

    /**
     * @return list<array{key: string, title: string, text: string, rangeKey: string}>
     */
    public function subscaleInterpretationsUz(): array
    {
        return [
            ['key' => 'low', 'title' => 'Past', 'text' => 'Ushbu yo\'nalish bo\'yicha xatar belgilari aniqlanmadi.', 'rangeKey' => self::RANGE_RISK],
            ['key' => 'medium', 'title' => 'O\'rta', 'text' => 'Alohida belgilar mavjud, kuzatuv talab etiladi.', 'rangeKey' => self::RANGE_RISK],
            ['key' => 'high', 'title' => 'Yuqori', 'text' => 'Aniq ifodalangan xatar omili; interventsiyaning nishoni.', 'rangeKey' => self::RANGE_RISK],
            ['key' => 'very_high', 'title' => 'Juda yuqori', 'text' => 'Ustuvor muammo; individual ish rejasining birinchi bandi.', 'rangeKey' => self::RANGE_RISK],
            ['key' => 'low', 'title' => 'Past', 'text' => 'Psixologik yordamga alohida ehtiyoj bildirilmagan.', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['key' => 'medium', 'title' => 'O\'rta', 'text' => 'Ba\'zan qo\'llab-quvvatlashga ehtiyoj sezadi; kuzatuv tavsiya etiladi.', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['key' => 'high', 'title' => 'Yuqori', 'text' => 'Yordamga ochiq ehtiyoj bildirilgan. Qat\'iy qoida: ball 15 ga yetsa, bir hafta ichida albatta individual suhbat o\'tkaziladi.', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['key' => 'very_high', 'title' => 'Juda yuqori', 'text' => 'Yordamga aniq va kuchli ehtiyoj. Qat\'iy qoida: bir hafta ichida individual suhbat majburiy.', 'rangeKey' => self::RANGE_PSYCH_NEED],
        ];
    }

    /**
     * @return list<array{key: string, title: string, text: string, rangeKey: string}>
     */
    public function subscaleInterpretationsRu(): array
    {
        return [
            ['key' => 'low', 'title' => 'Низкий', 'text' => 'Признаков риска по этому направлению не выявлено.', 'rangeKey' => self::RANGE_RISK],
            ['key' => 'medium', 'title' => 'Средний', 'text' => 'Есть отдельные признаки, требуется наблюдение.', 'rangeKey' => self::RANGE_RISK],
            ['key' => 'high', 'title' => 'Высокий', 'text' => 'Явно выраженный фактор риска; мишень для вмешательства.', 'rangeKey' => self::RANGE_RISK],
            ['key' => 'very_high', 'title' => 'Очень высокий', 'text' => 'Приоритетная проблема; первый пункт плана индивидуальной работы.', 'rangeKey' => self::RANGE_RISK],
            ['key' => 'low', 'title' => 'Низкий', 'text' => 'Особой потребности в психологической помощи не выражено.', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['key' => 'medium', 'title' => 'Средний', 'text' => 'Иногда ощущается потребность в поддержке; рекомендуется наблюдение.', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['key' => 'high', 'title' => 'Высокий', 'text' => 'Выражена открытая потребность в помощи. Строгое правило: при балле 15 и выше индивидуальная беседа обязательна в течение недели.', 'rangeKey' => self::RANGE_PSYCH_NEED],
            ['key' => 'very_high', 'title' => 'Очень высокий', 'text' => 'Явная и сильная потребность в помощи. Строгое правило: индивидуальная беседа обязательна в течение недели.', 'rangeKey' => self::RANGE_PSYCH_NEED],
        ];
    }
}
