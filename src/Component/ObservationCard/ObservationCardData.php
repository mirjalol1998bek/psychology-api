<?php

declare(strict_types=1);

namespace App\Component\ObservationCard;

/**
 * 10-metodika: Kurator va psixologning kuzatuv kartasi (ekspert bahosi).
 * Kategoriya/Quiz/Question emas — Attempt tizimidan mustaqil (talaba emas,
 * tyutor to'ldiradi; guruhga biriktirilmaydi, har talaba uchun bir marta).
 * Shu sabab band matnlari shu yerda qattiq kodlangan (rasmiy matn, uz).
 */
final class ObservationCardData
{
    /**
     * @var array<string, string>
     */
    public const INDICATORS = [
        'attendance' => 'Darslarga davomat (sababsiz qoldirishlar)',
        'participation' => 'Mashg‘ulotlardagi faollik va ishtirok',
        'performance_decline' => 'O‘zlashtirish dinamikasi (pasayish belgilari)',
        'assignment_timeliness' => 'Topshiriqlarni o‘z vaqtida bajarish',
        'group_status' => 'Guruhdagi mavqei (chetlanish, yolg‘izlik)',
        'peer_conflicts' => 'Guruhdoshlar bilan nizoli holatlar',
        'faculty_relations' => 'Professor-o‘qituvchilar bilan munosabat',
        'emotional_state' => 'Emotsional fon (tushkunlik, asabiylik, yig‘loqilik)',
        'behavior_change' => 'Xulq-atvordagi keskin o‘zgarishlar',
        'appearance' => 'Tashqi ko‘rinish va o‘ziga e’tibor',
        'discipline_violations' => 'Intizomiy qoidabuzarliklar',
        'dormitory_behavior' => 'Turar joydagi (yotoqxonadagi) xulq-atvor',
        'financial_hardship' => 'Moddiy qiyinchilik belgilari',
        'family_contact' => 'Oila bilan aloqa (uzilish, ziddiyat belgilari)',
        'staff_contact_readiness' => 'Kurator va psixolog bilan aloqaga tayyorlik',
    ];

    public const MIN_SCORE = 0;
    public const MAX_SCORE = 3;

    /**
     * 9-ko'rsatkich 3 ball bilan baholansa — jami balldan qat'i nazar,
     * darhol individual suhbat talab qilinadi (rasmiy qoida).
     */
    public const ALERT_KEY = 'behavior_change';
    public const ALERT_SCORE = 3;

    /**
     * @var list<array{min: int, max: int, key: string, title: string, action: string}>
     */
    public const BANDS = [
        [
            'min' => 0,
            'max' => 10,
            'key' => 'none',
            'title' => 'Xavotirli belgilar yo‘q',
            'action' => 'Odatiy kuzatuv.',
        ],
        [
            'min' => 11,
            'max' => 22,
            'key' => 'attention',
            'title' => 'Alohida muammoli sohalar bor',
            'action' => 'E’tibor guruhi. Kurator bilan birgalikda individual suhbat rejalashtiriladi.',
        ],
        [
            'min' => 23,
            'max' => 34,
            'key' => 'risk',
            'title' => 'Ko‘p sohada muammo',
            'action' => 'Xavf guruhi. Individual ish rejasi, oylik monitoring, ota-onalar bilan aloqa '
                . '(talabaning roziligi bilan).',
        ],
        [
            'min' => 35,
            'max' => 45,
            'key' => 'systemic',
            'title' => 'Tizimli noblagopoluchiye',
            'action' => 'Kompleks chora: psixolog, kurator, dekanat va yoshlar bilan ishlash bo‘limining '
                . 'birgalikdagi ishi.',
        ],
    ];
}
