<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

final class TemperamentUzData
{
    public const RESULT_KEYS = ['Xolerik', 'Sangvinik', 'Flegmatik', 'Melanxolik'];

    /**
     * @return array<string, list<string>>
     */
    public function blocks(): array
    {
        return [
            'Xolerik' => [
                'Saramjon',
                'O‘zimni tuta olmayman tez jahlim chiqadi',
                'Betoqatman',
                'Odamlar bilan munosabatim keskin',
                'Qat’iyatli va tashabbuskorman',
                'O‘jar va qaysarman',
                'Tortishuv va bahslarda topqirman',
                'Bir xil tempda ishlamayman',
                'Tavakkalchilikka moyilligim bor',
                'Yomon narsalarni eslab yurmayman',
                'Nutqim tez bo‘linuvchan ohangga ega',
                'Muvozanatsiz tez qizishib ketishga moyilman',
                'Tez urishib ketadigan janjalkash',
                'Kamchiliklarga murosasizman',
                'Ifodali mimika egasiman',
                'Tez harakat qilaman va qarorga kelaman',
                'Yangilikka muttasil intilaman',
                'Harakatlarim keskin',
                'O‘z oldimga qo‘ygan maqsadni albatta amalga oshiraman',
                'Kayfiyatim tez o‘zgarishga moyil',
            ],
            'Sangvinik' => [
                'Dilkash va quvnoqman',
                'Tirishqoq va ishbilarmonman',
                'Ko‘p hollarda boshlagan ishimni oxiriga yetkazmayman',
                'O‘zimni yuqori baholayman',
                'Yangilikni tez o‘zlashtirib olaman',
                'Qiziqish va intilishlarim beqaror',
                'Muvaffaqiyatsizlikni tez unutaman',
                'Turli sharoitga tez moslashaman',
                'Har qanday yangi ishga qiziqish bilan kirishaman',
                'Yangi ishga tez kirishaman va tez birdan ikkinchisiga o‘ta olaman',
                'Agar ish qiziqtirmay qolsa tez soviyman',
                'Bir xil tempdagi ishlarda tez toliqaman',
                'Muomalaga tez kirishaman',
                'Chidamli va mehnatsevarman',
                'Nutqim baland tez, mimika va imo-ishoralarga boy',
                'Qiyin vaziyatda o‘zimni tuta bilaman',
                'Doimo tetik kayfiyatda yuraman',
                'Tez qarorga kelib undan voz kechaman',
                'Tez chalg‘iyman',
                'Behuda shoshilaman',
            ],
            'Flegmatik' => [
                'Osoyishta va sovuqqonman',
                'Ishda tartibli va izchilman',
                'Ehtiyotkor va aql bilan ish tutaman',
                'Sabr-toqat bilan kuta olaman',
                'Bo‘lar-bo‘lmas narsalar haqida gapirmay sukut saqlay olaman',
                'Nutqim osoyishta bir xil tempda va hech qanday ifodali harakatlarga ega emas',
                'Chidamli va o‘zimni tuta bilaman',
                'Boshlagan ishni oxiriga yetkaza olaman',
                'Behudaga kuch sarflamayman',
                'Ish rejimi va kun tartibiga qat’iy rioya qilaman',
                'Ehtirosni hayajonni osongina yengaman',
                'Tanqid va maqtovga e’tibor qilmayman',
                'Yuvosh va ko‘ngilchanman',
                'Qiziqish va munosabatlarim barqaror',
                'Ishga sekin moslashib boshqa ishga o‘tishga qiynalaman',
                'Munosabatlarim turli tuman',
                'Har bir narsada tartib va intizom bo‘lishini hohlayman',
                'Yangi sharoitga qiyinchilik bilan moslashaman',
                'Kayfiyatim barqaror',
                'Vazmin tabiatli og‘ir-bosiqman',
            ],
            'Melanxolik' => [
                'Uyatchan va tortinchoqman',
                'Yangi sharoitda o‘zimni yo‘qotib qo‘yaman',
                'Notanish kishilar bilan aloqa o‘rnatishga qiynalaman',
                'O‘z kuchimga ishonmayman',
                'Yolg‘izlikni yoqtiraman',
                'Muvaffaqiyatsizlikda tez tushkunlikka berilaman',
                'Tez charchayman',
                'Nutqim sekin va kuchsiz',
                'Boshqalar ta’siriga tez berilaman',
                'Ta’sirlanuvchan tez yig‘layman',
                'Tanqid va maqtovni tez qabul qilaman',
                'O‘zimga va boshqalarga nisbatan talabchanman',
                'Shubha va gumonga tez berilaman',
                'Tez xafa bo‘laman va arazlayman',
                'Har bir narsani o‘zimga tez qabul qilaman',
                'Muloqotga kirishishga qiynalaman',
                'Boshqalarga fikrimni aytishni yoqtirmayman',
                'Sust va kam faollik bilan ajralib turaman',
                'Tez bo’ysunaman, itoatkorman',
                'Boshqalarda yordam hissini uyg‘otishga harakat qilaman',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function labels(): array
    {
        return [
            'Xolerik' => 'Xolerik',
            'Sangvinik' => 'Sangvinik',
            'Flegmatik' => 'Flegmatik',
            'Melanxolik' => 'Melanxolik',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function interpretations(): array
    {
        return [
            'Xolerik' => 'Xoleriklar hissiyotli, qiziqqon, ammo gina saqlamaydigan kishilardir. Ular tashabbuskor, harakatchan va mas’uliyatli. Yangi ishga tez kirishadi va kundalik zerikarli ishlarni yoqtirmaydi. Ekstrimal turdagi haydash yoki suvostida suzish kurslari uchun sertifikat bersangiz yoqishi mumkin. Xoleriklar qiyin vaziyatlardan chiqib ketishni yaxshi ko‘radi. Shu sababdan ular uchun qandaydir murakkab o‘yin tayyorlashingiz ham mumkin. Xoleriklar o‘zlarining ahamiyatli inson ekanligiga ishonadi. Shuning uchun sovg‘a qimmat bo‘lmasa-da, o‘rami hashamdor bo‘lishiga harakat qiling. Uni dabdaba bilan, masalan, she’r aytib, katta sharlar bilan bering. Xoleriklar ichida rahbarlari ko‘p.Ularga ismi yozilgan ruchka, stol ustiga kanselyariya to‘plami, charm hamyon kabilarni berishingiz mumkin.',
            'Sangvinik' => 'Sangviniklar – hayosevar, quvnoq kishilar. Biror yangi narsani o‘rganishga qiziqadi. Tez aloqaga kirishadi. Hammaga xush kayfiyat ulashadi. Sangvinik uchun qandaydir qiziqarli shahar yoki mamlakatga sayohat ajoyib sovg‘a bo‘ladi. Sangviniklar kreativlikni yoqtiradi, shuning uchun sovg‘angiz hamyonbop bo‘lsa-da, original bo‘lishiga intiling. O‘z qo‘llari bilan tayyorlagan narsani juda qadrlashadi.Sangviniklar yumorni ham yaxshi ko‘radi. Kulgili gap yozilgan futbolka, hazilomuz buyumlarni sovg‘a qilsangiz, xursand bo‘lishi aniq.',
            'Flegmatik' => 'Flegmatik – sokin va bosiq odam. Ishda mantiqli va detallarga e’tiborli. Uni tizginidan chiqarish deyarli ilojsiz. U yaxshigina oilaparvar. Bunday kishilar sabrli, oqko‘ngil bo‘ladi. Flegmatiklar aqlli, ammo bu xususiyati bilan maqtanmaydi. Mantiqiy vazifalarni yoqtiradi. O‘ylantiradigan filmga chipta sovg‘a qilsangiz xursand bo‘ladi. Masalan, yangi detektiv yoki kutilmagan tarzda tugaydigan filmga. Flegmatiklar qiziqarli boshqotirmalarni yaxshi ko‘radi. Nima demoqchiligimizni tushungandirsiz? Shuningdek, flegmatiklar konservativ hamda amaliyotni yoqtiruvchilardir. Ularga uy uchun texnika yoki gadjet ma’qul bo‘ladi. Masalan, qahva maydalagich, powyer-bank, termos, ko‘p funksiyali pult, sok chiqargich va hokazolar.',
            'Melanxolik' => 'Bu ta’sirchan, hissiyotli, romantik va sezgir odam. Melanxoliklar tabiatan introvert – ular o‘z olamida yashaydi. Orzu qilish, fantaziya qilishni, dunyoning nomukammalligi haqida o‘ylashni yoqtiradi. Melanxolikka vintaj (o‘tgan asrlarga xos) yoki antikvar buyumlar yoqadi. Unga san’at asarlarini sovg‘a qilsangiz bo‘ladi. Masalan, suratlar, haykalchalar, tumorlar. Shuningdek, hissiyotli va juda chuqur ma’noli roman taqdim etishingiz mumkin. Melanxoliklar odatda ijodkor bo‘ladi, shuning uchun “o‘z qo‘ling bilan yasa” turidagi to‘plam, antistress albom, bo‘yoqlar sovg‘a qilsangiz xursand bo‘ladi. O‘z fikrlarini yozib borishi uchun sifatli, charm muqovali kundalik ham ular uchun yoqimli sovg‘a.',
        ];
    }
}
