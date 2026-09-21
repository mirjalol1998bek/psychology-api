# Ballash — `src/Component/Assessment/Scoring/`

## Strategy

`ScorerInterface` (`#[AutoconfigureTag('app.assessment_scorer')]`):

- `supports(InstrumentType $type): bool`
- `score(Attempt $attempt): ScoredResult`

`ScorerResolver` barcha scorer'larni `#[AutowireIterator]` orqali oladi va
`Category.instrumentType` bo'yicha mosini qaytaradi (`ScorerNotFoundException`).

`ScoredResult` (`readonly`): `resultKey`, `?score`, `breakdown` (`list<BreakdownItem>`).

## Algoritmlar

| `InstrumentType` | Scorer | Algoritm | `breakdown` |
|---|---|---|---|
| `TEMPERAMENT_STATEMENTS` | `CategoryTallyScorer` | Har bayonot = "Ha/Yo'q". Tanlangan "Ha" variantlar `AnswerOption.categoryKey` bo'yicha sanaladi. Eng ko'p ballli kategoriya — natija. | kategoriya → ball |
| `TEMPERAMENT_CHOICE` | `CategoryTallyScorer` | Har savolga bitta javob; `option.categoryKey` bo'yicha sanaladi; argmax. | kategoriya → ball |
| `FIGURE_CHOICE` | `FigureChoiceScorer` | Bitta figura tanlanadi; `option.categoryKey` (yo'q bo'lsa `text`) — natija. | bo'sh |
| `SCORE_SCALE` | `ScoreScaleScorer` | Tanlangan variant ballari yig'iladi. Teskari savolda (`Question.getIsReversed()`) `(maxOptionScore + minOptionScore) - optionScore`. Yig'indi `ScoreRange` oralig'iga tushadi → `resultKey`. | Subshkalasiz: `[{label:'score', value: total}]`. Subshkalali (pastga qarang): har subshkala uchun bitta element |

### Subshkalali `SCORE_SCALE` (masalan IPM-20)

Ba'zi so'rovnomalar savollarni nomlangan subshkalalarga bo'ladi (masalan
"Akademik moslashuv", "Ijtimoiy-guruhiy moslashuv") va har biri uchun
ALOHIDA ball/daraja chiqaradi, umumiy indeks bilan birga:

- `Question.subscaleKey` — savol qaysi subshkalaga tegishli (bo'sh =
  faqat umumiy ballga kiradi, lekin subshkalasi bo'lgan savol ham
  UMUMIY ballga kiradi — ikkisiga ham qo'shiladi).
- Umumiy ball uchun `ScoreRange`/`AssessmentInterpretation.subscaleKey = ''`
  (odatdagidek).
- Subshkala ballari uchun `subscaleKey = Question.subscaleRangeKey`
  qiymati — **nomiga bog'liq emas** (bir nechta subshkala bitta jadvalni
  bo'lishishi mumkin). Standart `'*'` — barcha subshkalalar bir xil
  oraliq/talqin to'plamini ishlatadi (IPM-20: barcha 4 subshkala 5–25).
  Subshkalalar TENG BO'LMAGAN o'lchamda bo'lsa (EHS-20: A/B 7-35, C 6-30)
  — har guruh o'z maxsus kalitiga ega (masalan `'anxiety_fatigue'`,
  `'resilience'`), shu kalit bilan alohida `ScoreRange`/`Interpretation`
  yaratiladi. `resultKey` umumiydan alohida nom fazosida (masalan
  subshkalada `high` bo'lishi umumiydagi `high`ga aralashmaydi —
  `subscaleKey` composite unikal kalitga kiradi).
- `ScoreScaleScorer` javoblarni `Question.subscaleKey` (ko'rinadigan nom)
  bo'yicha guruhlaydi, har guruh uchun o'sha guruhning
  `subscaleRangeKey`'siga mos oraliq/talqinni topadi va
  `BreakdownItem(label: subscaleKey, value, resultKey, title, description)`
  yaratadi — `label` savolning subscaleKey qiymati (talaba tilidagi to'liq
  nom, chunki har til o'z Quiz'ida alohida saqlanadi).
- Subshkalasi yo'q metodikalarda bu guruhlash bo'sh qoladi — eski
  `[{label:'score', value: total}]` xatti-harakat o'zgarmaydi.

### Ishorali umumiy indeks (masalan OKM-20: IMI = (A+B) − (C+D))

Ba'zi subshkalali metodikalarda UMUMIY ko'rsatkich oddiy yig'indi emas —
ba'zi subshkalalar **musbat**, ba'zilari **manfiy** qo'shiladi:

- `Question.overallSign` — shu savol UMUMIY ballga +1 yoki −1 bilan
  qo'shiladimi (standart `+1`, ya'ni oddiy yig'indi — IPM-20 o'zgarishsiz
  qoladi). Subshkalaning o'z ballini (5–25) o'zgartirmaydi —
  `subscaleTotals()` doim ishorasiz, xom yig'indi.
- OKM-20'da: A/B (ichki motivlar) savollari `overallSign=1`,
  C/D (tashqi/aralash) savollari `overallSign=-1` — natijada umumiy
  `sumScore()` aynan `(A+B)-(C+D)` ni beradi, `ScoreRange` esa manfiy
  qiymatlarni ham qabul qiladi (masalan `−40..−11`).

### Umumiy ballsiz subshkalali metodika (masalan KSM-20, QY-16)

Ba'zi so'rovnomalarda yagona UMUMIY ko'rsatkich mantiqan mavjud emas —
natija faqat mustaqil subshkalalar bilan chiqadi (KSM-20: muloqotchanlik,
empatiya, konfliktlilik, tashkilotchilik — birlashtirib bo'lmaydigan
4 ta mustaqil xususiyat; QY-16: 4 qadriyat bloki — har biri mustaqil
ustuvorlik darajasi, umumiy "reyting" ma'nosiz):

- Bunday kategoriya uchun umumiy `ScoreRange` (`subscaleKey=''`) ataylab
  **yaratilmaydi** — faqat subshkala `ScoreRange`lari bo'ladi.
- `ScoreScaleScorer::score()` bunday holatni `hasOverallRange()` bilan
  avtomatik aniqlaydi (kategoriyada `subscaleKey=''` bo'lgan `ScoreRange`
  yo'qligini tekshiradi) va `ScoreScaleScorer::NO_OVERALL_RESULT_KEY`
  (`'subscale_only'`) qiymatini `resultKey` sifatida, `null` ni `score`
  sifatida qaytaradi — `sumScore()`/`matchRange()` umuman chaqirilmaydi.
- Kategoriya uchun bitta `AssessmentInterpretation` (`subscaleKey=''`,
  `resultKey='subscale_only'`, har til uchun) seed qilinadi — bu odatiy
  "umumiy natija" o'rnini bosadigan, "natija subshkalalar bilan
  ko'rsatilgan" degan umumiy izoh matni (frontendda katta sarlavha va
  tavsif shu yerdan chiqadi).
- Frontendda `score === null` bo'lsa "Umumiy ball" ko'rsatkichi
  ko'rsatilmaydi (`TestResultView.vue`), faqat subshkala breakdown
  chiqadi — kodga tegilmadi, chunki `score` allaqachon `?int` edi.

### Ranjirlash metodikasi (QY-16) — Question/Option'ning boshqacha ma'nosi

QY-16'da savol-javob modeli boshqa `SCORE_SCALE` metodikalardan farqli:
har `Question` — bitta ranjirlanadigan element (masalan bitta qadriyat),
uning `AnswerOption`lari esa "1-o'rin"..."16-o'rin" degan pozitsiyalar
(`score` = pozitsiya raqami). Talaba HAR elementga alohida pozitsiya
belgilaydi (frontendda esa bitta ro'yxatni surib-tushirib tartiblash
sifatida ko'rinadi — `TakeTestView.vue`'da alohida `ranking_list`
format). Ballash tomoni — oddiy `subscaleKey` bo'yicha yig'indi
(`ScoreScaleScorer`, boshqa hech narsa o'zgarmagan): pozitsiya raqami
qancha kichik bo'lsa (ya'ni talaba shu elementni yuqoriroqqa qo'ysa),
uning subshkalasi (blok) yig'indisi shuncha kichik va ustuvor chiqadi.

`Tally` — value object (`array<string,int>` ni inkapsulyatsiya qiladi):
`register`, `add`, `topKey`, `toBreakdown`.

## Yakuniy hisob — `AttemptSubmitter::submit()`

1. `assertNotSubmitted()` — `AttemptStatus::isFinished()` bo'lsa `AttemptLockedException`.
2. `ScorerResolver->resolve(category.instrumentType)->score(attempt)` → `ScoredResult`.
3. `AssessmentInterpretationRepository::findOneBy(category, resultKey, studyLanguage)` →
   `title` + `text`. Topilmasa `label = resultKey`, `description = ''`.
4. `AssessmentResult` yaratiladi (`label`, `description`, `score`, `breakdown`),
   `Attempt.result` ga o'rnatiladi (cascade persist).
5. `Attempt.status = Submitted`, `submittedAt = now`.
6. `AttemptManager::save($attempt, true)`.

State Processor emas — migratsiyadan qayta hisoblash ham ishlashi uchun servisda.

## Yangi metodika qo'shish (masalan **Ibodullayev shkalasi**)

Kod **yozilmaydi**. Admin API yoki seed orqali:

1. `POST /api/categories` — `{name, instrumentType: "SCORE_SCALE", position}`
2. `POST /api/quizzes` — `{category, title, studyLanguage}`
3. `POST /api/questions` — har biri `{quiz, type: "SCALE", text, isReversed, options:[{text, score}]}`
4. `POST /api/score_ranges` — `{category, minScore, maxScore, resultKey}` har oraliq uchun
5. `POST /api/assessment_interpretations` — `{category, resultKey, studyLanguage, title, text}`
6. `POST /api/assignments` — kategoriyani guruhga biriktirish

Manba: `../psychology-front/src/data/assessments/*`,
`../psychology-front/src/utils/scoring.ts`.
