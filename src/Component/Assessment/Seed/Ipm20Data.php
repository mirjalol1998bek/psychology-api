<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

/**
 * IPM-20 — universitetga ijtimoiy-psixologik moslashuv so'rovnomasi.
 * 4 subshkala (har biri 5 bayonot, 5–25 ball) + umumiy indeks (20–100).
 */
final class Ipm20Data
{
    public const SUBSCALE_ACADEMIC_UZ = 'Akademik moslashuv';
    public const SUBSCALE_SOCIAL_UZ = 'Ijtimoiy-guruhiy moslashuv';
    public const SUBSCALE_EMOTIONAL_UZ = 'Shaxsiy-emotsional moslashuv';
    public const SUBSCALE_DOMESTIC_UZ = 'Maishiy-muhitiy moslashuv';

    public const SUBSCALE_ACADEMIC_RU = 'Академическая адаптация';
    public const SUBSCALE_SOCIAL_RU = 'Социально-групповая адаптация';
    public const SUBSCALE_EMOTIONAL_RU = 'Личностно-эмоциональная адаптация';
    public const SUBSCALE_DOMESTIC_RU = 'Бытовая адаптация';

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
     * @return list<array{text: string, reversed: bool, subscale: string}>
     */
    public function questionsUz(): array
    {
        return [
            ['text' => 'Universitetdagi o\'quv yuklamasini uddalay olaman.', 'reversed' => false, 'subscale' => self::SUBSCALE_ACADEMIC_UZ],
            ['text' => 'Guruhdoshlarim bilan tez til topishdim.', 'reversed' => false, 'subscale' => self::SUBSCALE_SOCIAL_UZ],
            ['text' => 'So\'nggi paytlarda o\'zimni ruhan tetik his qilaman.', 'reversed' => false, 'subscale' => self::SUBSCALE_EMOTIONAL_UZ],
            ['text' => 'Yashash sharoitim o\'qishimga xalaqit bermaydi.', 'reversed' => false, 'subscale' => self::SUBSCALE_DOMESTIC_UZ],
            ['text' => 'Ma\'ruza va amaliy mashg\'ulotlarni tushunish menga qiyinchilik tug\'diradi.', 'reversed' => true, 'subscale' => self::SUBSCALE_ACADEMIC_UZ],
            ['text' => 'Guruhda o\'zimni begona his qilaman.', 'reversed' => true, 'subscale' => self::SUBSCALE_SOCIAL_UZ],
            ['text' => 'Arzimagan narsadan ham tez asabiylashaman.', 'reversed' => true, 'subscale' => self::SUBSCALE_EMOTIONAL_UZ],
            ['text' => 'Universitetga o\'z vaqtida yetib kelish men uchun muammo emas.', 'reversed' => false, 'subscale' => self::SUBSCALE_DOMESTIC_UZ],
            ['text' => 'O\'quv topshiriqlarini belgilangan muddatda bajaraman.', 'reversed' => false, 'subscale' => self::SUBSCALE_ACADEMIC_UZ],
            ['text' => 'Guruhimda menga yordam beradigan odamlar bor.', 'reversed' => false, 'subscale' => self::SUBSCALE_SOCIAL_UZ],
            ['text' => 'Kelajagim haqida ishonch bilan o\'ylayman.', 'reversed' => false, 'subscale' => self::SUBSCALE_EMOTIONAL_UZ],
            ['text' => 'Kutubxona, laboratoriya va boshqa imkoniyatlardan bemalol foydalanaman.', 'reversed' => false, 'subscale' => self::SUBSCALE_DOMESTIC_UZ],
            ['text' => 'Imtihon va nazorat ishlari meni qattiq qo\'rqitadi.', 'reversed' => true, 'subscale' => self::SUBSCALE_ACADEMIC_UZ],
            ['text' => 'Guruh tadbirlari va yig\'ilishlarida faol qatnashaman.', 'reversed' => false, 'subscale' => self::SUBSCALE_SOCIAL_UZ],
            ['text' => 'Ko\'pincha sababsiz tushkunlikka tushaman.', 'reversed' => true, 'subscale' => self::SUBSCALE_EMOTIONAL_UZ],
            ['text' => 'Moddiy sharoitim o\'qishimga to\'sqinlik qilmoqda.', 'reversed' => true, 'subscale' => self::SUBSCALE_DOMESTIC_UZ],
            ['text' => 'Tanlagan ta\'lim yo\'nalishim menga mos ekaniga ishonaman.', 'reversed' => false, 'subscale' => self::SUBSCALE_ACADEMIC_UZ],
            ['text' => 'Guruhdoshlarim bilan darsdan tashqarida ham muloqot qilaman.', 'reversed' => false, 'subscale' => self::SUBSCALE_SOCIAL_UZ],
            ['text' => 'Kayfiyatim odatda barqaror bo\'ladi.', 'reversed' => false, 'subscale' => self::SUBSCALE_EMOTIONAL_UZ],
            ['text' => 'Universitetning ichki tartib qoidalariga oson moslashdim.', 'reversed' => false, 'subscale' => self::SUBSCALE_DOMESTIC_UZ],
        ];
    }

    /**
     * @return list<array{text: string, reversed: bool, subscale: string}>
     */
    public function questionsRu(): array
    {
        return [
            ['text' => 'Я справляюсь с учебной нагрузкой в университете.', 'reversed' => false, 'subscale' => self::SUBSCALE_ACADEMIC_RU],
            ['text' => 'Я быстро нашёл общий язык с однокурсниками.', 'reversed' => false, 'subscale' => self::SUBSCALE_SOCIAL_RU],
            ['text' => 'В последнее время я чувствую себя психически бодрым.', 'reversed' => false, 'subscale' => self::SUBSCALE_EMOTIONAL_RU],
            ['text' => 'Мои жилищные условия не мешают учёбе.', 'reversed' => false, 'subscale' => self::SUBSCALE_DOMESTIC_RU],
            ['text' => 'Мне трудно понимать лекции и практические занятия.', 'reversed' => true, 'subscale' => self::SUBSCALE_ACADEMIC_RU],
            ['text' => 'Я чувствую себя чужим в группе.', 'reversed' => true, 'subscale' => self::SUBSCALE_SOCIAL_RU],
            ['text' => 'Я быстро раздражаюсь по мелочам.', 'reversed' => true, 'subscale' => self::SUBSCALE_EMOTIONAL_RU],
            ['text' => 'Своевременный приезд в университет не является для меня проблемой.', 'reversed' => false, 'subscale' => self::SUBSCALE_DOMESTIC_RU],
            ['text' => 'Я выполняю учебные задания в установленный срок.', 'reversed' => false, 'subscale' => self::SUBSCALE_ACADEMIC_RU],
            ['text' => 'В моей группе есть люди, которые мне помогают.', 'reversed' => false, 'subscale' => self::SUBSCALE_SOCIAL_RU],
            ['text' => 'Я с уверенностью думаю о своём будущем.', 'reversed' => false, 'subscale' => self::SUBSCALE_EMOTIONAL_RU],
            ['text' => 'Я свободно пользуюсь библиотекой, лабораторией и другими возможностями.', 'reversed' => false, 'subscale' => self::SUBSCALE_DOMESTIC_RU],
            ['text' => 'Экзамены и контрольные работы сильно пугают меня.', 'reversed' => true, 'subscale' => self::SUBSCALE_ACADEMIC_RU],
            ['text' => 'Я активно участвую в групповых мероприятиях и собраниях.', 'reversed' => false, 'subscale' => self::SUBSCALE_SOCIAL_RU],
            ['text' => 'Часто я без причины впадаю в уныние.', 'reversed' => true, 'subscale' => self::SUBSCALE_EMOTIONAL_RU],
            ['text' => 'Моё материальное положение мешает учёбе.', 'reversed' => true, 'subscale' => self::SUBSCALE_DOMESTIC_RU],
            ['text' => 'Я уверен, что выбранное направление обучения мне подходит.', 'reversed' => false, 'subscale' => self::SUBSCALE_ACADEMIC_RU],
            ['text' => 'Я общаюсь с однокурсниками и вне занятий.', 'reversed' => false, 'subscale' => self::SUBSCALE_SOCIAL_RU],
            ['text' => 'Моё настроение обычно стабильно.', 'reversed' => false, 'subscale' => self::SUBSCALE_EMOTIONAL_RU],
            ['text' => 'Я легко адаптировался к внутренним правилам университета.', 'reversed' => false, 'subscale' => self::SUBSCALE_DOMESTIC_RU],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function overallRanges(): array
    {
        return [
            ['min' => 81, 'max' => 100, 'key' => 'high'],
            ['min' => 61, 'max' => 80, 'key' => 'satisfactory'],
            ['min' => 41, 'max' => 60, 'key' => 'medium'],
            ['min' => 20, 'max' => 40, 'key' => 'low'],
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
            'high' => [
                'title' => 'Yuqori',
                'text' => 'To\'liq moslashgan. Muhitda o\'zini erkin his qiladi, resurslari yetarli. Chora: umumiy profilaktika, ijtimoiy faollikka jalb qilish.',
            ],
            'satisfactory' => [
                'title' => 'Qoniqarli',
                'text' => 'Moslashuv asosan yakunlangan, ayrim yo\'nalishlarda qiyinchiliklar bor. Chora: past subshkala bo\'yicha maslahat suhbati.',
            ],
            'medium' => [
                'title' => 'O\'rta (kuchsiz)',
                'text' => 'Moslashuv jarayoni cho\'zilgan, dezadaptatsiya xavfi mavjud. Chora: guruhiy trening, kurator kuzatuvi, 3 oydan keyin qayta diagnostika.',
            ],
            'low' => [
                'title' => 'Past',
                'text' => 'Aniq dezadaptatsiya. Chora: individual psixologik ish rejasi, oylik monitoring, zarurat bo\'lsa mutaxassisga yo\'llash.',
            ],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function overallInterpretationsRu(): array
    {
        return [
            'high' => [
                'title' => 'Высокий',
                'text' => 'Полностью адаптирован. Студент свободно чувствует себя в среде, его ресурсов достаточно. Мера: общая профилактика, вовлечение в общественную активность.',
            ],
            'satisfactory' => [
                'title' => 'Удовлетворительный',
                'text' => 'Адаптация в основном завершена, по отдельным направлениям есть трудности. Мера: консультативная беседа по слабой субшкале.',
            ],
            'medium' => [
                'title' => 'Средний (слабый)',
                'text' => 'Процесс адаптации затянулся, есть риск дезадаптации. Мера: групповой тренинг, наблюдение куратора, повторная диагностика через 3 месяца.',
            ],
            'low' => [
                'title' => 'Низкий',
                'text' => 'Выраженная дезадаптация. Мера: индивидуальный план психологической работы, ежемесячный мониторинг, при необходимости — направление к специалисту.',
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
                'title' => 'Yuqori',
                'text' => 'Ushbu yo\'nalish talabaning kuchli tomoni, resurs sifatida ishlatilishi mumkin.',
            ],
            'above_average' => [
                'title' => 'O\'rtadan yuqori',
                'text' => 'Muammo yo\'q, alohida ish talab qilinmaydi.',
            ],
            'medium' => [
                'title' => 'O\'rta',
                'text' => 'Aniq qiyinchiliklar bor; suhbatda shu yo\'nalishga e\'tibor qaratiladi.',
            ],
            'low' => [
                'title' => 'Past',
                'text' => 'Muammoli yo\'nalish; interventsiyaning asosiy nishoni hisoblanadi.',
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
                'title' => 'Высокий',
                'text' => 'Это направление — сильная сторона студента, может использоваться как ресурс.',
            ],
            'above_average' => [
                'title' => 'Выше среднего',
                'text' => 'Проблем нет, отдельная работа не требуется.',
            ],
            'medium' => [
                'title' => 'Средний',
                'text' => 'Есть явные трудности; в беседе стоит уделить внимание этому направлению.',
            ],
            'low' => [
                'title' => 'Низкий',
                'text' => 'Проблемное направление; основная цель для вмешательства.',
            ],
        ];
    }
}
