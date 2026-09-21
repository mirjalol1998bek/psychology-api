<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

/**
 * KSM-20 — kommunikativ xususiyatlar va shaxslararo munosabatlar
 * so'rovnomasi. 4 subshkala (har biri 5 bayonot, 5-25 ball). UMUMIY BALL
 * YO'Q — A/B/D bitta ijobiy yo'nalishdagi jadvalni bo'lishadi, C
 * (konfliktlilik) teskari ma'nodagi o'z jadvaliga ega (bir xil sonli
 * oraliq, boshqa talqin). Teskari ballanadigan savol yo'q.
 */
final class Ksm20Data
{
    public const SUBSCALE_COMMUNICATION_UZ = 'Muloqotchanlik';
    public const SUBSCALE_EMPATHY_UZ = 'Empatiya';
    public const SUBSCALE_CONFLICT_UZ = 'Konfliktlilik';
    public const SUBSCALE_LEADERSHIP_UZ = 'Tashkilotchilik va liderlik';

    public const SUBSCALE_COMMUNICATION_RU = 'Общительность';
    public const SUBSCALE_EMPATHY_RU = 'Эмпатия';
    public const SUBSCALE_CONFLICT_RU = 'Конфликтность';
    public const SUBSCALE_LEADERSHIP_RU = 'Организаторские способности и лидерство';

    /** A/B/D (ijobiy yo'nalish) uchun jadval kaliti — C dan farqli, chunki
     * bir xil son oralig'i boshqa ma'noni bildiradi (yuqori ball = salbiy). */
    public const RANGE_TRAIT = 'trait';
    public const RANGE_CONFLICT = 'conflict';

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
            ['text' => 'Notanish odamlar bilan oson suhbat boshlayman.', 'subscale' => self::SUBSCALE_COMMUNICATION_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Boshqa odamning kayfiyatini yuzidan sezaman.', 'subscale' => self::SUBSCALE_EMPATHY_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Bahsda o\'z fikrimda qattiq turaman, yon bermayman.', 'subscale' => self::SUBSCALE_CONFLICT_UZ, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Guruhda biror ish tashkil qilish kerak bo\'lsa, tashabbusni o\'z qo\'limga olaman.', 'subscale' => self::SUBSCALE_LEADERSHIP_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Ko\'p odam yig\'ilgan joyda o\'zimni erkin his qilaman.', 'subscale' => self::SUBSCALE_COMMUNICATION_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Do\'stimning muammosi meni ham bezovta qiladi.', 'subscale' => self::SUBSCALE_EMPATHY_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Menga tanbeh berilsa, keskin javob qaytaraman.', 'subscale' => self::SUBSCALE_CONFLICT_UZ, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Boshqalarni biror maqsad atrofida birlashtira olaman.', 'subscale' => self::SUBSCALE_LEADERSHIP_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Yangi tanishlar orttirish menga qiyin emas.', 'subscale' => self::SUBSCALE_COMMUNICATION_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Suhbatdoshimni oxirigacha tinglayman, gapini bo\'lmayman.', 'subscale' => self::SUBSCALE_EMPATHY_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Guruhdoshlarim bilan tez-tez kelishmovchilikka boraman.', 'subscale' => self::SUBSCALE_CONFLICT_UZ, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Menga topshirilgan jamoaviy ishni oxiriga yetkazaman.', 'subscale' => self::SUBSCALE_LEADERSHIP_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Yozishmadan ko\'ra yuzma-yuz muloqotni afzal ko\'raman.', 'subscale' => self::SUBSCALE_COMMUNICATION_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Kimdir qiyin ahvolda qolsa, yordam berishga harakat qilaman.', 'subscale' => self::SUBSCALE_EMPATHY_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Menga qarshi chiqilsa, jahlim tez chiqadi.', 'subscale' => self::SUBSCALE_CONFLICT_UZ, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Guruhdoshlarim ko\'pincha mendan maslahat so\'rashadi.', 'subscale' => self::SUBSCALE_LEADERSHIP_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Uzoq vaqt yolg\'iz qolsam zerikaman.', 'subscale' => self::SUBSCALE_COMMUNICATION_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'O\'zimni boshqa odamning o\'rniga qo\'yib ko\'ra olaman.', 'subscale' => self::SUBSCALE_EMPATHY_UZ, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Kechirim so\'rash men uchun juda og\'ir.', 'subscale' => self::SUBSCALE_CONFLICT_UZ, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Tadbir yoki loyihani boshqarish menga yoqadi.', 'subscale' => self::SUBSCALE_LEADERSHIP_UZ, 'rangeKey' => self::RANGE_TRAIT],
        ];
    }

    /**
     * @return list<array{text: string, subscale: string, rangeKey: string}>
     */
    public function questionsRu(): array
    {
        return [
            ['text' => 'Я легко начинаю разговор с незнакомыми людьми.', 'subscale' => self::SUBSCALE_COMMUNICATION_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Я чувствую настроение другого человека по его лицу.', 'subscale' => self::SUBSCALE_EMPATHY_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'В споре я твёрдо стою на своём мнении, не уступаю.', 'subscale' => self::SUBSCALE_CONFLICT_RU, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Если в группе нужно что-то организовать, я беру инициативу в свои руки.', 'subscale' => self::SUBSCALE_LEADERSHIP_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Я чувствую себя свободно в местах с большим скоплением людей.', 'subscale' => self::SUBSCALE_COMMUNICATION_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Проблема моего друга беспокоит и меня.', 'subscale' => self::SUBSCALE_EMPATHY_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Если мне делают замечание, я резко отвечаю.', 'subscale' => self::SUBSCALE_CONFLICT_RU, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Я могу объединить окружающих вокруг какой-то цели.', 'subscale' => self::SUBSCALE_LEADERSHIP_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Заводить новые знакомства для меня несложно.', 'subscale' => self::SUBSCALE_COMMUNICATION_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Я выслушиваю собеседника до конца, не перебиваю.', 'subscale' => self::SUBSCALE_EMPATHY_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Я часто вступаю в разногласия с одногруппниками.', 'subscale' => self::SUBSCALE_CONFLICT_RU, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Я довожу порученную мне коллективную работу до конца.', 'subscale' => self::SUBSCALE_LEADERSHIP_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Я предпочитаю личное общение переписке.', 'subscale' => self::SUBSCALE_COMMUNICATION_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Если кто-то оказывается в трудной ситуации, я стараюсь помочь.', 'subscale' => self::SUBSCALE_EMPATHY_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Если мне противоречат, я быстро выхожу из себя.', 'subscale' => self::SUBSCALE_CONFLICT_RU, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Одногруппники часто спрашивают у меня совета.', 'subscale' => self::SUBSCALE_LEADERSHIP_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Если я долго остаюсь один(одна), мне становится скучно.', 'subscale' => self::SUBSCALE_COMMUNICATION_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Я могу поставить себя на место другого человека.', 'subscale' => self::SUBSCALE_EMPATHY_RU, 'rangeKey' => self::RANGE_TRAIT],
            ['text' => 'Просить прощения для меня очень тяжело.', 'subscale' => self::SUBSCALE_CONFLICT_RU, 'rangeKey' => self::RANGE_CONFLICT],
            ['text' => 'Мне нравится руководить мероприятием или проектом.', 'subscale' => self::SUBSCALE_LEADERSHIP_RU, 'rangeKey' => self::RANGE_TRAIT],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string, rangeKey: string}>
     */
    public function subscaleRanges(): array
    {
        return [
            ['min' => 5, 'max' => 10, 'key' => 'low', 'rangeKey' => self::RANGE_TRAIT],
            ['min' => 11, 'max' => 15, 'key' => 'medium', 'rangeKey' => self::RANGE_TRAIT],
            ['min' => 16, 'max' => 20, 'key' => 'above_average', 'rangeKey' => self::RANGE_TRAIT],
            ['min' => 21, 'max' => 25, 'key' => 'high', 'rangeKey' => self::RANGE_TRAIT],
            ['min' => 5, 'max' => 10, 'key' => 'low', 'rangeKey' => self::RANGE_CONFLICT],
            ['min' => 11, 'max' => 15, 'key' => 'medium', 'rangeKey' => self::RANGE_CONFLICT],
            ['min' => 16, 'max' => 20, 'key' => 'high', 'rangeKey' => self::RANGE_CONFLICT],
            ['min' => 21, 'max' => 25, 'key' => 'very_high', 'rangeKey' => self::RANGE_CONFLICT],
        ];
    }

    /**
     * @return array{title: string, text: string}
     */
    public function noOverallInterpretationUz(): array
    {
        return [
            'title' => 'Kommunikativ profil',
            'text' => 'Bitta umumiy ball hisoblanmaydi — natija 4 ta mustaqil yo\'nalish (muloqotchanlik, empatiya, konfliktlilik, tashkilotchilik) bo\'yicha pastda ko\'rsatilgan. Ularning kombinatsiyasiga qarab psixolog talabaning kommunikativ profilini aniqlaydi: barcha ijobiy ko\'rsatkichlar yuqori va konfliktlilik o\'rtacha bo\'lsa — konstruktiv-lider profili; faqat empatiya yuqori, boshqalari past bo\'lsa — hamdard-passiv; tashkilotchilik va konfliktlilik yuqori, empatiya past bo\'lsa — dominant-konfliktogen; barcha ijobiy ko\'rsatkichlar past bo\'lsa — izolyatsion profil haqida so\'z boradi.',
        ];
    }

    /**
     * @return array{title: string, text: string}
     */
    public function noOverallInterpretationRu(): array
    {
        return [
            'title' => 'Коммуникативный профиль',
            'text' => 'Единый общий балл не подсчитывается — результат показан ниже по 4 независимым направлениям (общительность, эмпатия, конфликтность, организаторские способности). В зависимости от их сочетания психолог определяет коммуникативный профиль студента: все положительные показатели высокие, а конфликтность средняя — конструктивно-лидерский профиль; высока только эмпатия, остальные низкие — сочувствующе-пассивный; организаторские способности и конфликтность высокие, эмпатия низкая — доминантно-конфликтогенный; все положительные показатели низкие — изоляционный профиль.',
        ];
    }

    /**
     * @return list<array{key: string, title: string, text: string, rangeKey: string}>
     */
    public function subscaleInterpretationsUz(): array
    {
        return [
            ['key' => 'high', 'title' => 'Yuqori', 'text' => 'Sifat aniq ifodalangan, shaxsning kuchli tomoni.', 'rangeKey' => self::RANGE_TRAIT],
            ['key' => 'above_average', 'title' => 'O\'rtadan yuqori', 'text' => 'Sifat yetarli darajada shakllangan.', 'rangeKey' => self::RANGE_TRAIT],
            ['key' => 'medium', 'title' => 'O\'rta', 'text' => 'Sifat vaziyatga bog\'liq namoyon bo\'ladi, rivojlantirish mumkin.', 'rangeKey' => self::RANGE_TRAIT],
            ['key' => 'low', 'title' => 'Past', 'text' => 'Sifat shakllanmagan; kommunikativ trening tavsiya etiladi.', 'rangeKey' => self::RANGE_TRAIT],
            ['key' => 'very_high', 'title' => 'Juda yuqori', 'text' => 'Konfliktogen shaxs. Guruhdagi keskinlikning ehtimoliy manbai. Chora: konfliktologik trening, individual ish, kurator kuzatuvi.', 'rangeKey' => self::RANGE_CONFLICT],
            ['key' => 'high', 'title' => 'Yuqori', 'text' => 'Nizolarga moyillik bor, o\'zini boshqarish ko\'nikmalarini rivojlantirish kerak.', 'rangeKey' => self::RANGE_CONFLICT],
            ['key' => 'medium', 'title' => 'O\'rta (norma)', 'text' => 'O\'z manfaatini himoya qila oladi, ammo nizoni keskinlashtirmaydi. Optimal variant.', 'rangeKey' => self::RANGE_CONFLICT],
            ['key' => 'low', 'title' => 'Past', 'text' => 'Nizodan qochish, o\'z pozitsiyasini himoya qila olmaslik. Bu ham muammo: assertivlik treningi tavsiya etiladi.', 'rangeKey' => self::RANGE_CONFLICT],
        ];
    }

    /**
     * @return list<array{key: string, title: string, text: string, rangeKey: string}>
     */
    public function subscaleInterpretationsRu(): array
    {
        return [
            ['key' => 'high', 'title' => 'Высокий', 'text' => 'Качество ярко выражено, сильная сторона личности.', 'rangeKey' => self::RANGE_TRAIT],
            ['key' => 'above_average', 'title' => 'Выше среднего', 'text' => 'Качество сформировано в достаточной степени.', 'rangeKey' => self::RANGE_TRAIT],
            ['key' => 'medium', 'title' => 'Средний', 'text' => 'Качество проявляется ситуативно, можно развивать.', 'rangeKey' => self::RANGE_TRAIT],
            ['key' => 'low', 'title' => 'Низкий', 'text' => 'Качество не сформировано; рекомендуется коммуникативный тренинг.', 'rangeKey' => self::RANGE_TRAIT],
            ['key' => 'very_high', 'title' => 'Очень высокий', 'text' => 'Конфликтогенная личность. Возможный источник напряжённости в группе. Мера: конфликтологический тренинг, индивидуальная работа, наблюдение куратора.', 'rangeKey' => self::RANGE_CONFLICT],
            ['key' => 'high', 'title' => 'Высокий', 'text' => 'Есть склонность к конфликтам, нужно развивать навыки самоконтроля.', 'rangeKey' => self::RANGE_CONFLICT],
            ['key' => 'medium', 'title' => 'Средний (норма)', 'text' => 'Способен(на) отстаивать свои интересы, но не обостряет конфликт. Оптимальный вариант.', 'rangeKey' => self::RANGE_CONFLICT],
            ['key' => 'low', 'title' => 'Низкий', 'text' => 'Избегание конфликта, неспособность отстаивать свою позицию. Это тоже проблема: рекомендуется тренинг ассертивности.', 'rangeKey' => self::RANGE_CONFLICT],
        ];
    }
}
