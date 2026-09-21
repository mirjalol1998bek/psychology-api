<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

/**
 * QY-16 — qadriyat yo'nalishlari so'rovnomasi (ranjirlash metodikasi).
 * 16 ta qadriyat, talaba har biriga 1 (eng muhim) dan 16 (eng kam muhim)
 * gacha o'rin beradi (har o'rin faqat bir marta ishlatiladi). 4 blok
 * (har biri 4 qadriyat) — blok bo'yicha o'rinlar yig'indisi (10-58) hisoblanadi;
 * YIG'INDI QANCHALIK KICHIK BO'LSA, BLOK SHUNCHALIK USTUVOR. UMUMIY BALL YO'Q
 * (KSM-20 kabi) — natija faqat 4 blok bilan chiqadi.
 *
 * Barcha 4 blokning son oralig'i bir xil (10-58, 4 pog'ona) bo'lsa-da,
 * har birining talqin matni butunlay boshqa — shu sababli EHS-20/XO-20
 * naqshi bo'yicha har blok o'z alohida `rangeKey`'siga ega (umumiy `'*'`
 * emas), aks holda 4 blokning bir xil pog'ona kaliti bitta talqin
 * qatoriga to'qnashib qolardi.
 *
 * Savol/variant modeli boshqa SCORE_SCALE metodikalardan farqli: har savol
 * BITTA qadriyatning o'zi, variantlari esa 1-16 o'rin (`score` = o'rin
 * raqami) — frontendda oddiy shkala emas, maxsus tartiblash interfeysi
 * bilan to'ldiriladi.
 */
final class Qy16Data
{
    public const BLOCK_PERSONAL_UZ = 'Shaxsiy hayot va farovonlik qadriyatlari';
    public const BLOCK_PROFESSIONAL_UZ = 'Kasbiy-o\'quv va o\'zini rivojlantirish qadriyatlari';
    public const BLOCK_STATUS_UZ = 'Ijtimoiy-status qadriyatlari';
    public const BLOCK_MORAL_UZ = 'Ma\'naviy-ijtimoiy qadriyatlar';

    public const BLOCK_PERSONAL_RU = 'Ценности личной жизни и благополучия';
    public const BLOCK_PROFESSIONAL_RU = 'Профессионально-учебные ценности и саморазвитие';
    public const BLOCK_STATUS_RU = 'Ценности социального статуса';
    public const BLOCK_MORAL_RU = 'Духовно-социальные ценности';

    public const RANGE_PERSONAL = 'personal';
    public const RANGE_PROFESSIONAL = 'professional';
    public const RANGE_STATUS = 'status';
    public const RANGE_MORAL = 'moral';

    /**
     * @return list<array{text: string, block: string, rangeKey: string}>
     */
    public function valuesUz(): array
    {
        return [
            ['text' => 'Sog\'liq (jismoniy va ruhiy)', 'block' => self::BLOCK_PERSONAL_UZ, 'rangeKey' => self::RANGE_PERSONAL],
            ['text' => 'Baxtli oilaviy hayot', 'block' => self::BLOCK_PERSONAL_UZ, 'rangeKey' => self::RANGE_PERSONAL],
            ['text' => 'Sevgi va yaqin insonlar bilan munosabat', 'block' => self::BLOCK_PERSONAL_UZ, 'rangeKey' => self::RANGE_PERSONAL],
            ['text' => 'Ichki uyg\'unlik va xotirjamlik', 'block' => self::BLOCK_PERSONAL_UZ, 'rangeKey' => self::RANGE_PERSONAL],
            ['text' => 'Bilim va ma\'rifat, keng dunyoqarash', 'block' => self::BLOCK_PROFESSIONAL_UZ, 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['text' => 'Qiziqarli ish, kasbiy o\'zini namoyon qilish', 'block' => self::BLOCK_PROFESSIONAL_UZ, 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['text' => 'Ijodiy faoliyat', 'block' => self::BLOCK_PROFESSIONAL_UZ, 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['text' => 'Mustaqillik va qaror qabul qilish erkinligi', 'block' => self::BLOCK_PROFESSIONAL_UZ, 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['text' => 'Moddiy farovonlik', 'block' => self::BLOCK_STATUS_UZ, 'rangeKey' => self::RANGE_STATUS],
            ['text' => 'Jamiyatdagi obro\' va e\'tirof', 'block' => self::BLOCK_STATUS_UZ, 'rangeKey' => self::RANGE_STATUS],
            ['text' => 'Sadoqatli do\'stlar', 'block' => self::BLOCK_STATUS_UZ, 'rangeKey' => self::RANGE_STATUS],
            ['text' => 'Faol hayot tarzi, taassurotlarga boylik', 'block' => self::BLOCK_STATUS_UZ, 'rangeKey' => self::RANGE_STATUS],
            ['text' => 'Ma\'naviy-axloqiy kamolot', 'block' => self::BLOCK_MORAL_UZ, 'rangeKey' => self::RANGE_MORAL],
            ['text' => 'Vatanga xizmat qilish', 'block' => self::BLOCK_MORAL_UZ, 'rangeKey' => self::RANGE_MORAL],
            ['text' => 'Boshqalarga foyda keltirish, mehr-oqibat', 'block' => self::BLOCK_MORAL_UZ, 'rangeKey' => self::RANGE_MORAL],
            ['text' => 'An\'ana va milliy qadriyatlarga sodiqlik', 'block' => self::BLOCK_MORAL_UZ, 'rangeKey' => self::RANGE_MORAL],
        ];
    }

    /**
     * @return list<array{text: string, block: string, rangeKey: string}>
     */
    public function valuesRu(): array
    {
        return [
            ['text' => 'Здоровье (физическое и психическое)', 'block' => self::BLOCK_PERSONAL_RU, 'rangeKey' => self::RANGE_PERSONAL],
            ['text' => 'Счастливая семейная жизнь', 'block' => self::BLOCK_PERSONAL_RU, 'rangeKey' => self::RANGE_PERSONAL],
            ['text' => 'Любовь и отношения с близкими людьми', 'block' => self::BLOCK_PERSONAL_RU, 'rangeKey' => self::RANGE_PERSONAL],
            ['text' => 'Внутренняя гармония и спокойствие', 'block' => self::BLOCK_PERSONAL_RU, 'rangeKey' => self::RANGE_PERSONAL],
            ['text' => 'Знания и просвещение, широкий кругозор', 'block' => self::BLOCK_PROFESSIONAL_RU, 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['text' => 'Интересная работа, профессиональная самореализация', 'block' => self::BLOCK_PROFESSIONAL_RU, 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['text' => 'Творческая деятельность', 'block' => self::BLOCK_PROFESSIONAL_RU, 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['text' => 'Самостоятельность и свобода принятия решений', 'block' => self::BLOCK_PROFESSIONAL_RU, 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['text' => 'Материальное благополучие', 'block' => self::BLOCK_STATUS_RU, 'rangeKey' => self::RANGE_STATUS],
            ['text' => 'Авторитет и признание в обществе', 'block' => self::BLOCK_STATUS_RU, 'rangeKey' => self::RANGE_STATUS],
            ['text' => 'Верные друзья', 'block' => self::BLOCK_STATUS_RU, 'rangeKey' => self::RANGE_STATUS],
            ['text' => 'Активный образ жизни, богатство впечатлений', 'block' => self::BLOCK_STATUS_RU, 'rangeKey' => self::RANGE_STATUS],
            ['text' => 'Духовно-нравственное совершенствование', 'block' => self::BLOCK_MORAL_RU, 'rangeKey' => self::RANGE_MORAL],
            ['text' => 'Служение Родине', 'block' => self::BLOCK_MORAL_RU, 'rangeKey' => self::RANGE_MORAL],
            ['text' => 'Приносить пользу другим, милосердие', 'block' => self::BLOCK_MORAL_RU, 'rangeKey' => self::RANGE_MORAL],
            ['text' => 'Верность традициям и национальным ценностям', 'block' => self::BLOCK_MORAL_RU, 'rangeKey' => self::RANGE_MORAL],
        ];
    }

    /**
     * 1 (eng muhim) dan 16 (eng kam muhim) gacha o'rin variantlari.
     *
     * @return list<string>
     */
    public function positionOptionsUz(): array
    {
        $options = [];

        for ($i = 1; $i <= 16; $i++) {
            $options[] = $i . '-o\'rin';
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    public function positionOptionsRu(): array
    {
        $options = [];

        for ($i = 1; $i <= 16; $i++) {
            $options[] = $i . '-е место';
        }

        return $options;
    }

    /**
     * @return array{title: string, text: string}
     */
    public function noOverallInterpretationUz(): array
    {
        return [
            'title' => 'Qadriyatlar ierarxiyasi',
            'text' => 'Bitta umumiy ball hisoblanmaydi — natija 4 blokning har biri bo\'yicha pastda ko\'rsatilgan. Har blokning o\'rinlar yig\'indisi qancha kichik bo\'lsa, o\'sha blok talaba uchun shunchalik ustuvor.',
        ];
    }

    /**
     * @return array{title: string, text: string}
     */
    public function noOverallInterpretationRu(): array
    {
        return [
            'title' => 'Иерархия ценностей',
            'text' => 'Единый общий балл не подсчитывается — результат по каждому из 4 блоков показан ниже. Чем меньше сумма мест блока, тем он приоритетнее для студента.',
        ];
    }

    /**
     * Har blok uchun bir xil 4 pog'onali oraliq (10-58), lekin har biri
     * o'z `rangeKey`'sida — shu sababli talqin matnlari to'qnashmaydi.
     *
     * @return list<array{min: int, max: int, key: string, rangeKey: string}>
     */
    public function blockRanges(): array
    {
        $tiers = [
            ['min' => 10, 'max' => 21, 'key' => 'very_priority'],
            ['min' => 22, 'max' => 33, 'key' => 'priority'],
            ['min' => 34, 'max' => 45, 'key' => 'medium'],
            ['min' => 46, 'max' => 58, 'key' => 'low_priority'],
        ];
        $ranges = [];

        foreach ([self::RANGE_PERSONAL, self::RANGE_PROFESSIONAL, self::RANGE_STATUS, self::RANGE_MORAL] as $rangeKey) {
            foreach ($tiers as $tier) {
                $ranges[] = $tier + ['rangeKey' => $rangeKey];
            }
        }

        return $ranges;
    }

    /**
     * @return list<array{key: string, title: string, text: string, rangeKey: string}>
     */
    public function blockInterpretationsUz(): array
    {
        return [
            ['key' => 'very_priority', 'title' => 'Juda ustuvor', 'text' => 'Yo\'nalganlik oila, yaqinlar va shaxsiy barqarorlikka qaratilgan. Bu — madaniyatimizga xos, odatiy va qulay variant. Kasbiy motivatsiyani mustahkamlash foydali.', 'rangeKey' => self::RANGE_PERSONAL],
            ['key' => 'priority', 'title' => 'Ustuvor', 'text' => 'Shaxsiy hayot va yaqinlar bilan munosabatlar muhim o\'rin tutadi, ammo yagona ustuvorlik emas.', 'rangeKey' => self::RANGE_PERSONAL],
            ['key' => 'medium', 'title' => 'O\'rtacha', 'text' => 'Shaxsiy farovonlik boshqa yo\'nalishlar bilan bir qatorda o\'rta darajada ahamiyatga ega.', 'rangeKey' => self::RANGE_PERSONAL],
            ['key' => 'low_priority', 'title' => 'Kam ustuvor', 'text' => 'Shaxsiy hayot va yaqinlar bilan munosabatlar hozircha orqa planda qolmoqda — bu vaqtinchalik yoki boshqa yo\'nalishlarga chuqur singib ketganlik bilan bog\'liq bo\'lishi mumkin.', 'rangeKey' => self::RANGE_PERSONAL],

            ['key' => 'very_priority', 'title' => 'Juda ustuvor', 'text' => 'Kasbiy identifikatsiya faol shakllanmoqda. Bunday talabalar ilmiy va loyihaviy faoliyatga jalb qilinadi.', 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['key' => 'priority', 'title' => 'Ustuvor', 'text' => 'Kasbiy va o\'quv rivojlanishi muhim yo\'nalishlardan biri, izchil qo\'llab-quvvatlash foydali.', 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['key' => 'medium', 'title' => 'O\'rtacha', 'text' => 'Kasbiy-o\'quv rivojlanishiga o\'rtacha ahamiyat beriladi, boshqa yo\'nalishlar bilan muvozanatda.', 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['key' => 'low_priority', 'title' => 'Kam ustuvor', 'text' => 'Kasbiy-o\'quv rivojlanishi hozircha ustuvor emas — o\'quv motivatsiyasini oshirish choralari ko\'rib chiqilishi mumkin (OKM-20 bilan solishtiring).', 'rangeKey' => self::RANGE_PROFESSIONAL],

            ['key' => 'very_priority', 'title' => 'Juda ustuvor', 'text' => 'Tashqi tan olinish va moddiy natijaga yo\'nalganlik. Motivatsiyaning tashqi manbalarga bog\'liqligi xavfi bor; mazmunli motivlarni rivojlantirish kerak.', 'rangeKey' => self::RANGE_STATUS],
            ['key' => 'priority', 'title' => 'Ustuvor', 'text' => 'Ijtimoiy maqom va tan olinish muhim o\'rin tutadi, boshqa yo\'nalishlar bilan birga rivojlantirilishi mumkin.', 'rangeKey' => self::RANGE_STATUS],
            ['key' => 'medium', 'title' => 'O\'rtacha', 'text' => 'Ijtimoiy-status yo\'nalishi o\'rtacha ahamiyatga ega, ortiqcha bog\'liqlik xavfi kuzatilmaydi.', 'rangeKey' => self::RANGE_STATUS],
            ['key' => 'low_priority', 'title' => 'Kam ustuvor', 'text' => 'Tashqi tan olinish va moddiy status hozircha ustuvor emas — bu ko\'pincha mazmunli ichki motivatsiya ustunligidan darak beradi.', 'rangeKey' => self::RANGE_STATUS],

            ['key' => 'very_priority', 'title' => 'Juda ustuvor', 'text' => 'Prosotsial yo\'nalganlik. Volontyorlik, ustozlik va ijtimoiy loyihalarda o\'zini namoyon qilishi mumkin.', 'rangeKey' => self::RANGE_MORAL],
            ['key' => 'priority', 'title' => 'Ustuvor', 'text' => 'Ma\'naviy-axloqiy va ijtimoiy foyda keltirish qadriyatlari muhim o\'rin tutadi.', 'rangeKey' => self::RANGE_MORAL],
            ['key' => 'medium', 'title' => 'O\'rtacha', 'text' => 'Ma\'naviy-ijtimoiy yo\'nalishga o\'rtacha ahamiyat beriladi.', 'rangeKey' => self::RANGE_MORAL],
            ['key' => 'low_priority', 'title' => 'Kam ustuvor', 'text' => 'Ma\'naviy-ijtimoiy qadriyatlar hozircha orqa planda — bu shaxsiy yoki kasbiy ustuvorliklar bilan bog\'liq bo\'lishi mumkin.', 'rangeKey' => self::RANGE_MORAL],
        ];
    }

    /**
     * @return list<array{key: string, title: string, text: string, rangeKey: string}>
     */
    public function blockInterpretationsRu(): array
    {
        return [
            ['key' => 'very_priority', 'title' => 'Очень приоритетно', 'text' => 'Направленность на семью, близких людей и личную стабильность. Это — привычный и удобный для нашей культуры вариант. Полезно укреплять профессиональную мотивацию.', 'rangeKey' => self::RANGE_PERSONAL],
            ['key' => 'priority', 'title' => 'Приоритетно', 'text' => 'Личная жизнь и отношения с близкими занимают важное место, но не являются единственным приоритетом.', 'rangeKey' => self::RANGE_PERSONAL],
            ['key' => 'medium', 'title' => 'Средне', 'text' => 'Личное благополучие имеет среднюю значимость наравне с другими направлениями.', 'rangeKey' => self::RANGE_PERSONAL],
            ['key' => 'low_priority', 'title' => 'Менее приоритетно', 'text' => 'Личная жизнь и отношения с близкими пока отходят на второй план — это может быть временным или связано с глубокой вовлечённостью в другие направления.', 'rangeKey' => self::RANGE_PERSONAL],

            ['key' => 'very_priority', 'title' => 'Очень приоритетно', 'text' => 'Активно формируется профессиональная идентификация. Такие студенты вовлекаются в научную и проектную деятельность.', 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['key' => 'priority', 'title' => 'Приоритетно', 'text' => 'Профессиональное и учебное развитие — одно из важных направлений, полезна последовательная поддержка.', 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['key' => 'medium', 'title' => 'Средне', 'text' => 'Профессионально-учебному развитию придаётся среднее значение, в балансе с другими направлениями.', 'rangeKey' => self::RANGE_PROFESSIONAL],
            ['key' => 'low_priority', 'title' => 'Менее приоритетно', 'text' => 'Профессионально-учебное развитие пока не приоритетно — можно рассмотреть меры по повышению учебной мотивации (сравните с ОУПМ-20).', 'rangeKey' => self::RANGE_PROFESSIONAL],

            ['key' => 'very_priority', 'title' => 'Очень приоритетно', 'text' => 'Направленность на внешнее признание и материальный результат. Есть риск зависимости мотивации от внешних источников; нужно развивать содержательные мотивы.', 'rangeKey' => self::RANGE_STATUS],
            ['key' => 'priority', 'title' => 'Приоритетно', 'text' => 'Социальный статус и признание занимают важное место, могут развиваться наряду с другими направлениями.', 'rangeKey' => self::RANGE_STATUS],
            ['key' => 'medium', 'title' => 'Средне', 'text' => 'Направление социального статуса имеет среднюю значимость, риска чрезмерной зависимости не наблюдается.', 'rangeKey' => self::RANGE_STATUS],
            ['key' => 'low_priority', 'title' => 'Менее приоритетно', 'text' => 'Внешнее признание и материальный статус пока не приоритетны — это часто говорит о преобладании содержательной внутренней мотивации.', 'rangeKey' => self::RANGE_STATUS],

            ['key' => 'very_priority', 'title' => 'Очень приоритетно', 'text' => 'Просоциальная направленность. Может проявляться в волонтёрстве, наставничестве и социальных проектах.', 'rangeKey' => self::RANGE_MORAL],
            ['key' => 'priority', 'title' => 'Приоритетно', 'text' => 'Духовно-нравственные ценности и польза для других занимают важное место.', 'rangeKey' => self::RANGE_MORAL],
            ['key' => 'medium', 'title' => 'Средне', 'text' => 'Духовно-социальному направлению придаётся среднее значение.', 'rangeKey' => self::RANGE_MORAL],
            ['key' => 'low_priority', 'title' => 'Менее приоритетно', 'text' => 'Духовно-социальные ценности пока отходят на второй план — это может быть связано с личными или профессиональными приоритетами.', 'rangeKey' => self::RANGE_MORAL],
        ];
    }
}
