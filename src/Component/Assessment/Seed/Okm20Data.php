<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

/**
 * OKM-20 — o'quv-kasbiy motivatsiya so'rovnomasi. 4 subshkala (har biri
 * 5 bayonot, 5–25 ball) + ishorali umumiy indeks: IMI = (A+B) − (C+D)
 * (−40..+40). Teskari ballanadigan savol yo'q.
 */
final class Okm20Data
{
    public const SUBSCALE_COGNITIVE_UZ = 'Bilish (kognitiv) motivlari';
    public const SUBSCALE_PROFESSIONAL_UZ = 'Kasbiy motivlar';
    public const SUBSCALE_PRAGMATIC_UZ = 'Tashqi-pragmatik motivlar';
    public const SUBSCALE_PRESTIGE_UZ = 'Prestij va o\'zini namoyon qilish';

    public const SUBSCALE_COGNITIVE_RU = 'Познавательные (когнитивные) мотивы';
    public const SUBSCALE_PROFESSIONAL_RU = 'Профессиональные мотивы';
    public const SUBSCALE_PRAGMATIC_RU = 'Внешне-прагматические мотивы';
    public const SUBSCALE_PRESTIGE_RU = 'Престиж и самопрезентация';

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
     * @return list<array{text: string, subscale: string, sign: int}>
     */
    public function questionsUz(): array
    {
        return [
            ['text' => 'Yangi bilim olish jarayonining o\'zi menga zavq beradi.', 'subscale' => self::SUBSCALE_COGNITIVE_UZ, 'sign' => 1],
            ['text' => 'Kelgusi kasbimni chuqur egallashni istayman.', 'subscale' => self::SUBSCALE_PROFESSIONAL_UZ, 'sign' => 1],
            ['text' => 'Diplom olish men uchun asosiy maqsad.', 'subscale' => self::SUBSCALE_PRAGMATIC_UZ, 'sign' => -1],
            ['text' => 'Guruhdagi eng yaxshi talabalardan biri bo\'lishni xohlayman.', 'subscale' => self::SUBSCALE_PRESTIGE_UZ, 'sign' => -1],
            ['text' => 'Dasturda bo\'lmagan adabiyotlarni ham o\'z tashabbusim bilan o\'qiyman.', 'subscale' => self::SUBSCALE_COGNITIVE_UZ, 'sign' => 1],
            ['text' => 'Kasbimga oid amaliy ko\'nikmalarni egallashga alohida e\'tibor beraman.', 'subscale' => self::SUBSCALE_PROFESSIONAL_UZ, 'sign' => 1],
            ['text' => 'Asosan stipendiyani yo\'qotmaslik uchun o\'qiyman.', 'subscale' => self::SUBSCALE_PRAGMATIC_UZ, 'sign' => -1],
            ['text' => 'Ustozlar va guruhdoshlar meni bilimdon deb hisoblashini istayman.', 'subscale' => self::SUBSCALE_PRESTIGE_UZ, 'sign' => -1],
            ['text' => 'Murakkab ilmiy masalalar ustida bosh qotirishni yoqtiraman.', 'subscale' => self::SUBSCALE_COGNITIVE_UZ, 'sign' => 1],
            ['text' => 'O\'z sohamning yetuk mutaxassisi bo\'lishni maqsad qilganman.', 'subscale' => self::SUBSCALE_PROFESSIONAL_UZ, 'sign' => 1],
            ['text' => 'Ko\'proq ota-onam talab qilgani uchun o\'qiyman.', 'subscale' => self::SUBSCALE_PRAGMATIC_UZ, 'sign' => -1],
            ['text' => 'Ilmiy anjuman, olimpiada va tanlovlarda qatnashish menga qiziq.', 'subscale' => self::SUBSCALE_PRESTIGE_UZ, 'sign' => -1],
            ['text' => 'Fanlar bo\'yicha savollarim ko\'p bo\'ladi va javobini izlayman.', 'subscale' => self::SUBSCALE_COGNITIVE_UZ, 'sign' => 1],
            ['text' => 'Amaliyot va malaka mashg\'ulotlarini sabrsizlik bilan kutaman.', 'subscale' => self::SUBSCALE_PROFESSIONAL_UZ, 'sign' => 1],
            ['text' => 'Imtihondan o\'tsam bo\'lgani, chuqur bilim men uchun ikkinchi darajali.', 'subscale' => self::SUBSCALE_PRAGMATIC_UZ, 'sign' => -1],
            ['text' => 'Ijtimoiy faoliyat menga o\'zimni namoyon qilish imkonini beradi.', 'subscale' => self::SUBSCALE_PRESTIGE_UZ, 'sign' => -1],
            ['text' => 'Mustaqil o\'rganish va izlanish menga yoqadi.', 'subscale' => self::SUBSCALE_COGNITIVE_UZ, 'sign' => 1],
            ['text' => 'Kelajakda aynan shu sohada ishlashni aniq rejalashtirganman.', 'subscale' => self::SUBSCALE_PROFESSIONAL_UZ, 'sign' => 1],
            ['text' => 'Agar diplomni o\'qimasdan olish mumkin bo\'lsa, shunday qilardim.', 'subscale' => self::SUBSCALE_PRAGMATIC_UZ, 'sign' => -1],
            ['text' => 'Boshqalar orasida ajralib turish men uchun muhim.', 'subscale' => self::SUBSCALE_PRESTIGE_UZ, 'sign' => -1],
        ];
    }

    /**
     * @return list<array{text: string, subscale: string, sign: int}>
     */
    public function questionsRu(): array
    {
        return [
            ['text' => 'Сам процесс получения новых знаний доставляет мне удовольствие.', 'subscale' => self::SUBSCALE_COGNITIVE_RU, 'sign' => 1],
            ['text' => 'Я хочу глубоко овладеть своей будущей профессией.', 'subscale' => self::SUBSCALE_PROFESSIONAL_RU, 'sign' => 1],
            ['text' => 'Получение диплома — моя главная цель.', 'subscale' => self::SUBSCALE_PRAGMATIC_RU, 'sign' => -1],
            ['text' => 'Я хочу быть одним из лучших студентов в группе.', 'subscale' => self::SUBSCALE_PRESTIGE_RU, 'sign' => -1],
            ['text' => 'Я по собственной инициативе читаю литературу, не входящую в программу.', 'subscale' => self::SUBSCALE_COGNITIVE_RU, 'sign' => 1],
            ['text' => 'Я уделяю особое внимание освоению практических навыков по профессии.', 'subscale' => self::SUBSCALE_PROFESSIONAL_RU, 'sign' => 1],
            ['text' => 'Учусь в основном для того, чтобы не потерять стипендию.', 'subscale' => self::SUBSCALE_PRAGMATIC_RU, 'sign' => -1],
            ['text' => 'Я хочу, чтобы преподаватели и однокурсники считали меня знающим.', 'subscale' => self::SUBSCALE_PRESTIGE_RU, 'sign' => -1],
            ['text' => 'Мне нравится размышлять над сложными научными вопросами.', 'subscale' => self::SUBSCALE_COGNITIVE_RU, 'sign' => 1],
            ['text' => 'Я поставил цель стать зрелым специалистом в своей области.', 'subscale' => self::SUBSCALE_PROFESSIONAL_RU, 'sign' => 1],
            ['text' => 'Учусь в основном потому, что этого требуют родители.', 'subscale' => self::SUBSCALE_PRAGMATIC_RU, 'sign' => -1],
            ['text' => 'Мне интересно участвовать в научных конференциях, олимпиадах и конкурсах.', 'subscale' => self::SUBSCALE_PRESTIGE_RU, 'sign' => -1],
            ['text' => 'У меня возникает много вопросов по предметам, и я ищу на них ответы.', 'subscale' => self::SUBSCALE_COGNITIVE_RU, 'sign' => 1],
            ['text' => 'Я с нетерпением жду практики и практических занятий.', 'subscale' => self::SUBSCALE_PROFESSIONAL_RU, 'sign' => 1],
            ['text' => 'Мне достаточно сдать экзамен, глубокие знания для меня вторичны.', 'subscale' => self::SUBSCALE_PRAGMATIC_RU, 'sign' => -1],
            ['text' => 'Общественная деятельность даёт мне возможность проявить себя.', 'subscale' => self::SUBSCALE_PRESTIGE_RU, 'sign' => -1],
            ['text' => 'Мне нравится самостоятельно изучать и исследовать.', 'subscale' => self::SUBSCALE_COGNITIVE_RU, 'sign' => 1],
            ['text' => 'Я чётко планирую работать именно в этой сфере в будущем.', 'subscale' => self::SUBSCALE_PROFESSIONAL_RU, 'sign' => 1],
            ['text' => 'Если бы диплом можно было получить, не учась, я бы так и сделал(а).', 'subscale' => self::SUBSCALE_PRAGMATIC_RU, 'sign' => -1],
            ['text' => 'Для меня важно выделяться среди других.', 'subscale' => self::SUBSCALE_PRESTIGE_RU, 'sign' => -1],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function overallRanges(): array
    {
        return [
            ['min' => 11, 'max' => 40, 'key' => 'favorable'],
            ['min' => 1, 'max' => 10, 'key' => 'balanced'],
            ['min' => -10, 'max' => 0, 'key' => 'external'],
            ['min' => -40, 'max' => -11, 'key' => 'maladaptive'],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function subscaleRanges(): array
    {
        return [
            ['min' => 21, 'max' => 25, 'key' => 'high'],
            ['min' => 16, 'max' => 20, 'key' => 'above_average'],
            ['min' => 11, 'max' => 15, 'key' => 'medium'],
            ['min' => 5, 'max' => 10, 'key' => 'low'],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function overallInterpretationsUz(): array
    {
        return [
            'favorable' => [
                'title' => 'Qulay ichki profil',
                'text' => 'O\'qish o\'z-o\'zidan qiziqarli va kasbiy jihatdan mazmunli. Chora: ilmiy to\'garak, loyihalarga jalb qilish.',
            ],
            'balanced' => [
                'title' => 'Muvozanatli profil',
                'text' => 'Ichki va tashqi motivlar teng. Chora: kasbiy identifikatsiyani mustahkamlovchi tadbirlar, amaliyot bilan bog\'lash.',
            ],
            'external' => [
                'title' => 'Tashqi motivatsiya ustunligi',
                'text' => 'O\'qish majburiyat sifatida qabul qilinmoqda. Chora: kasbiy yo\'naltiruvchi suhbat, muvaffaqiyat vaziyatini yaratish.',
            ],
            'maladaptive' => [
                'title' => 'Nomuvofiq profil',
                'text' => 'Kasb tanlash xatosi ehtimoli yuqori, o\'qishni tashlab ketish xavfi bor. Chora: individual kasbiy maslahat, zarurat bo\'lsa yo\'nalish o\'zgartirish masalasini muhokama qilish.',
            ],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function overallInterpretationsRu(): array
    {
        return [
            'favorable' => [
                'title' => 'Благоприятный внутренний профиль',
                'text' => 'Учёба сама по себе интересна и профессионально осмысленна. Мера: привлечение к научному кружку, проектной деятельности.',
            ],
            'balanced' => [
                'title' => 'Сбалансированный профиль',
                'text' => 'Внутренние и внешние мотивы уравновешены. Мера: мероприятия по укреплению профессиональной идентичности, связь с практикой.',
            ],
            'external' => [
                'title' => 'Преобладание внешней мотивации',
                'text' => 'Учёба воспринимается как обязанность. Мера: профориентационная беседа, создание ситуации успеха.',
            ],
            'maladaptive' => [
                'title' => 'Несоответствующий профиль',
                'text' => 'Высокая вероятность ошибки в выборе профессии, риск ухода из учёбы. Мера: индивидуальная профессиональная консультация, при необходимости — обсуждение смены направления.',
            ],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function subscaleInterpretationsUz(): array
    {
        return [
            'high' => [
                'title' => 'Juda yuqori',
                'text' => 'Motivning yetakchi (dominant) mavqei.',
            ],
            'above_average' => [
                'title' => 'Yuqori',
                'text' => 'Motiv barqaror ifodalangan.',
            ],
            'medium' => [
                'title' => 'O\'rta',
                'text' => 'Motiv vaziyatga bog\'liq ravishda namoyon bo\'ladi.',
            ],
            'low' => [
                'title' => 'Past',
                'text' => 'Motiv deyarli ishlamaydi.',
            ],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function subscaleInterpretationsRu(): array
    {
        return [
            'high' => [
                'title' => 'Очень высокий',
                'text' => 'Ведущее (доминирующее) положение мотива.',
            ],
            'above_average' => [
                'title' => 'Высокий',
                'text' => 'Мотив устойчиво выражен.',
            ],
            'medium' => [
                'title' => 'Средний',
                'text' => 'Мотив проявляется в зависимости от ситуации.',
            ],
            'low' => [
                'title' => 'Низкий',
                'text' => 'Мотив практически не работает.',
            ],
        ];
    }
}
