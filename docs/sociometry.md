# Sotsiometriya (7-metodika)

Boshqa 9 ta metodikadan farqli: talaba **o'zi** haqida emas, guruhdoshlari
haqida javob beradi ("kim bilan birga ishlashni istardingiz?" kabi), va
individual natija ma'nosiz — haqiqiy tahlil **guruh darajasida**, barcha
a'zolarning javoblarini birlashtirib.

Manba: rasmiy hujjat (`7-қадам.docx`, 2026-09-21 taqdim etilgan).

## Talaba tomoni — mavjud Attempt/Quiz tizimi orqali

`ObservationCard`dan farqli o'laroq bu yerda filler = subject = talaba (xuddi
boshqa metodikalar kabi), shu sabab **mavjud Category/Quiz/Question/Attempt
oqimi to'liq qayta ishlatiladi** — yangi entity yo'q:

- `InstrumentType::Sociometry` (`SOCIOMETRY`).
- `QuestionType::PeerChoice` (`PEER_CHOICE`) — 3 savol (mezon), variantsiz
  (`SLIDER_DUAL` kabi `hasOptions()=false`). Javob — tanlangan guruhdosh
  user ID'lari ro'yxati (afzallik tartibida, ≤3 ta), `AttemptAnswer.textValue`da
  JSON `["12","45","78"]`.
- `SociometryScorer implements ScorerInterface` — individual attempt uchun
  shaxsiy natija yo'q, `ScoreScaleScorer::NO_OVERALL_RESULT_KEY` bilan bo'sh
  breakdown (KSM-20/QY-16/Dembo-Rubinshteyn'dagi "umumiy ballsiz" naqshi).
  Talaba faqat "Rahmat! Javoblaringiz muvaffaqiyatli qabul qilindi." degan
  sodda matnni ko'radi (`AssessmentInterpretation`, `resultKey='subscale_only'`)
  — kim ko'rishi haqidagi texnik tafsilot matnga chiqarilmaydi
  (`TestResultView.vue` breakdown bo'sh bo'lganda alohida, sodda "rahmat"
  ko'rinishini chizadi — pastga qarang).
- Admin `POST /api/assignments` orqali kategoriyani guruhga oddiy tarzda
  biriktiradi — hech qanday maxsus kod kerak emas (`assignment.md`).
- **Salbiy tanlovlar v1'da yo'q** — rasmiy hujjatning o'zi buni "zarurat
  bo'lganda" qo'shiladigan istisno sifatida ta'riflaydi, standart emas.

`GET /api/students/groupmates` (`ROLE_STUDENT`) — `TakeTestView.vue`dagi
tanlov ro'yxati uchun joriy talabaning guruhdoshlari (`{id, fullName}`,
o'zisiz). `Question`/`AnswerOption` orqali emas — guruh tarkibi dinamik va
Category yaratilganda noma'lum, shu sabab alohida yengil endpoint.

## Guruh darajasidagi tahlil — `SociometryReporter`

`Category/Quiz/Attempt` orqali xom ma'lumot yig'iladi, lekin **hisoblash va
ko'rish butunlay mustaqil** — `GroupResultReporter`ga o'xshamaydi (u har
talaba uchun MUSTAQIL natijani qator qilib chiqaradi; bu yerda talabalar
o'rtasidagi JUFTLIK — kim kimni tanladi — muhim).

### Mezonlar bo'yicha birlashtirish (union) — hujjat aniq belgilamagan joyda qilingan qaror

Rasmiy hujjatda 3 mezon uchun 3 ta ALOHIDA sotsiomatritsa tuziladi deyilgan,
lekin "Hisoblanadigan indekslar" bo'limi **bitta** umumiy formulalar to'plami
beradi (mezon bo'yicha emas). Shu noaniqlikni hal qilish uchun:

- Bitta guruhdoshni **istalgan mezonda** tanlash — "aloqa" sifatida
  hisoblanadi (union, mezonlar bo'yicha takrorlanish yo'qotiladi/dedupe).
  Bu M/R/Si/Ei formulalarini so'zma-so'z (maxraj `N-1`, natija 0-1
  oralig'ida) saqlab qoladi — agar 3 mezonni oddiy qo'shib chiqilsa, maxraj
  `3(N-1)` bo'lishi kerak edi, hujjatda buni ko'rsatuvchi hech narsa yo'q.
- Bu — amaliy sotsiometriyada ham keng tarqalgan yondashuv (bir nechta
  mezonni "do'stlik tarmog'i"ga birlashtirish).

### Formulalar (`SociometryReporter.php`)

| Ko'rsatkich | Formula | Izoh |
|---|---|---|
| `M` (received) | Guruhdoshlar sonidan, kim bu talabani **istalgan** mezonda tanlagan (dedupe) | |
| `R` (given) | Shu talaba **istalgan** mezonda tanlagan guruhdoshlar soni (dedupe) | |
| `Si` | `M / (N-1)` | |
| `Ei` | `R / (N-1)` | |
| `Cn` (guruh jipsligi) | `2·X / (N·(N-1))` | `X` — o'zaro (mutual) juftliklar soni, dedupe |
| `Kiz` (izolyatsiya) | `(M=0 bo'lgan talabalar soni) / N × 100%` | |

Faqat **topshirilgan** (`AssessmentResult` bor) urinishlar hisobga olinadi —
`AssessmentResultRepository::findByGroupAndCategory()` orqali (`GroupResultReporter`
bilan bir xil so'rov). Topshirmagan talaba `submitted=false` bilan qatorda
chiqadi (u hali ham BOSHQALAR tomonidan tanlangan bo'lishi mumkin).

### Kategoriyalar — ustuvorlik tartibi

Hujjatdagi "1-2 ta tanlov" va "0 ta tanlov" ANIQ sonlar, "o'rtachadan
yuqori"/"2 baravar ko'p" esa NISBIY. Kichik guruhlarda bular ziddiyatga
kelishi mumkin (masalan o'rtacha=1 bo'lsa, 2 ta tanlov ham "2 baravar ko'p"
sharti, ham "1-2" sharti). Shu sabab tekshirish tartibi ustuvorlik bilan:

1. `M == 0` → `izolyatsiyadagilar`
2. `M <= 2` → `etibordan_chetdagilar`
3. `M >= 2 × o'rtacha` → `yulduzlar`
4. `M > o'rtacha` → `afzal_korilganlar`
5. aks holda → `ortacha` (hujjatda nomlanmagan, neytral)

"Rad etilganlar" kategoriyasi yo'q — salbiy tanlovlarga bog'liq, v1'da yo'q.

## API

| Operatsiya | Ruxsat | Izoh |
|---|---|---|
| `GET /api/students/groupmates` | `ROLE_STUDENT` | O'z guruhi (o'zisiz), `{id, fullName}` ro'yxati |
| `GET /api/admin/sociometry?studyGroup={id}` | `ROLE_PSYCHOLOGIST` | `SociometryGroupReport` — `{totalStudents, submittedCount, participationRate, cohesion, isolationRate, rows: SociometryStudentRow[]}` |

Talaba va tyutor `/api/admin/sociometry`ga kira olmaydi (403) — natija faqat
psixolog/admin uchun (foydalanuvchi bilan aniqlashtirib olindi, xuddi
`observation-card.md`dagi kabi).

## Frontend

- `src/data/assessments/sociometry.ts` — kategoriya matni (rang/ikonka) va
  jipslik darajasi talqini, backend bilan qo'lda sinxron.
- `TakeTestView.vue`'ning `peer_choice` bo'limi — har mezon uchun
  `v-chip`lar (guruhdoshlar), bosish tartibi = afzallik tartibi (1/2/3
  raqami chipda ko'rinadi). `AnswerMap` `Record<string, number>` bo'lib
  qolishi uchun tanlov `q${i}_1`/`q${i}_2`/`q${i}_3` alohida raqamli
  kalitlar sifatida saqlanadi (dual_slider'ning `_ob`/`_dd` naqshi kabi).
- `SociometryGroupResultsView.vue` (`/results/sotsiometriya/:facultyId/:groupId`)
  — umumiy `InstrumentGroupResultsView.vue`dan ALOHIDA komponent (data shakli
  tubdan boshqa — juftlik, individual resultKey emas). Router'da bu yo'l
  generic `results/:instrument/:facultyId/:groupId`dan OLDIN turadi (static
  segment ustuvor).
- `TestResultView.vue` (talaba natija ekrani) — `breakdown` bo'sh VA
  `score` yo'q bo'lgan har qanday natija (hozircha faqat Sotsiometriya,
  lekin instrumentga bog'liq emas — kelajakdagi shunga o'xshash metodika
  ham avtomatik shu bilan render bo'ladi) alohida, sodda "rahmat" kartasi
  bilan ko'rsatiladi (katta ✓ belgi, "NATIJA TAHLILI" sarlavhasi va
  breakdown grafigisiz) — oddiy hero+tahlil ko'rinishi o'rniga.
