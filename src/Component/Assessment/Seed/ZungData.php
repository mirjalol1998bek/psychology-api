<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

final class ZungData
{
    public const OPTIONS = [
        'Juda kam yoki hech qachon',
        'Ba\'zan',
        'Ko\'pincha',
        'Deyarli doim yoki doim',
    ];

    /**
     * @return list<array{text: string, reversed: bool}>
     */
    public function questions(): array
    {
        return [
            ['text' => 'Meni g\'amginlik va tushkunlik bosadi', 'reversed' => false],
            ['text' => 'Ertalab o\'zimni eng yaxshi his qilaman', 'reversed' => true],
            ['text' => 'Yig\'lagim keladi yoki yig\'lab yuboraman', 'reversed' => false],
            ['text' => 'Kechalari yaxshi uxlay olmayman', 'reversed' => false],
            ['text' => 'Ishtahim avvalgidek yaxshi', 'reversed' => true],
            ['text' => 'Qarama-qarshi jins bilan muloqot qilish menga hali ham yoqadi', 'reversed' => true],
            ['text' => 'Vaznim kamayayotganini sezaman', 'reversed' => false],
            ['text' => 'Ich qotishidan aziyat chekaman', 'reversed' => false],
            ['text' => 'Yuragim odatdagidan tezroq uradi', 'reversed' => false],
            ['text' => 'Hech sababsiz charchoq his qilaman', 'reversed' => false],
            ['text' => 'Miyam avvalgidek tiniq ishlaydi', 'reversed' => true],
            ['text' => 'Odatdagi ishlarni bajarish menga qiyinchilik tug\'dirmaydi', 'reversed' => true],
            ['text' => 'Bezovtaman va bir joyda tinch o\'tira olmayman', 'reversed' => false],
            ['text' => 'Kelajakka umid bilan qarayman', 'reversed' => true],
            ['text' => 'Avvalgidan ko\'ra jahldorroq bo\'lib qoldim', 'reversed' => false],
            ['text' => 'Qaror qabul qilish menga oson', 'reversed' => true],
            ['text' => 'O\'zimni kerakli va foydali odam deb his qilaman', 'reversed' => true],
            ['text' => 'Hayotim mazmunli va to\'kis', 'reversed' => true],
            ['text' => 'O\'lsam, atrofdagilarga yengilroq bo\'lardi deb o\'ylayman', 'reversed' => false],
            ['text' => 'Ilgari yoqadigan narsalar hozir ham menga zavq beradi', 'reversed' => true],
        ];
    }

    /**
     * @return list<array{min: int, max: int, key: string}>
     */
    public function ranges(): array
    {
        return [
            ['min' => 20, 'max' => 39, 'key' => 'normal'],
            ['min' => 40, 'max' => 47, 'key' => 'mild'],
            ['min' => 48, 'max' => 55, 'key' => 'moderate'],
            ['min' => 56, 'max' => 80, 'key' => 'severe'],
        ];
    }

    /**
     * @return array<string, array{title: string, text: string}>
     */
    public function interpretations(): array
    {
        return [
            'normal' => [
                'title' => 'Depressiya belgilari sezilmadi',
                'text' => 'Natijangiz me\'yor doirasida. Depressiv holatning ahamiyatli belgilari aniqlanmadi. Bu test tashxis emas — kayfiyatingiz pasayib, uzoq davom etsa, psixolog bilan bemalol bog\'laning.',
            ],
            'mild' => [
                'title' => 'Yengil depressiv holat',
                'text' => 'Yengil darajadagi depressiv belgilar mavjud. Uyqu, ishtaha, kayfiyat va faollikka e\'tibor bering. Belgilar ikki haftadan ortiq davom etsa, psixolog maslahatiga yoziling.',
            ],
            'moderate' => [
                'title' => 'O\'rtacha depressiv holat',
                'text' => 'O\'rtacha darajadagi depressiv belgilar aniqlandi. Universitet psixologi bilan uchrashuv belgilashni tavsiya qilamiz. Murojaat bo\'limi orqali yozishingiz mumkin.',
            ],
            'severe' => [
                'title' => 'Ifodalangan depressiv holat',
                'text' => 'Ifodalangan depressiv belgilar mavjud. Iltimos, imkon qadar tezroq psixolog yoki psixoterapevt bilan bog\'laning. Yordam so\'rash — kuchsizlik emas.',
            ],
        ];
    }
}
