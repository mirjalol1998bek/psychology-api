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
| `SCORE_SCALE` | `ScoreScaleScorer` | Tanlangan variant ballari yig'iladi. Teskari savolda (`Question.getIsReversed()`) `(maxOptionScore + minOptionScore) - optionScore` (Zung: `5 - ball`). Yig'indi `ScoreRange` oralig'iga tushadi → `resultKey`. | Subshkalasiz: `[{label:'score', value: total}]`. Subshkalali (pastga qarang): har subshkala uchun bitta element |

### Subshkalali `SCORE_SCALE` (masalan IPM-20)

Ba'zi so'rovnomalar savollarni nomlangan subshkalalarga bo'ladi (masalan
"Akademik moslashuv", "Ijtimoiy-guruhiy moslashuv") va har biri uchun
ALOHIDA ball/daraja chiqaradi, umumiy indeks bilan birga:

- `Question.subscaleKey` — savol qaysi subshkalaga tegishli (bo'sh =
  faqat umumiy ballga kiradi, lekin subshkalasi bo'lgan savol ham
  UMUMIY ballga kiradi — ikkisiga ham qo'shiladi).
- Umumiy ball uchun `ScoreRange`/`AssessmentInterpretation.subscaleKey = ''`
  (odatdagidek).
- Subshkala ballari uchun `subscaleKey = '*'` — **nomiga bog'liq emas**
  (barcha subshkalalar bir xil oraliq/talqin to'plamini ishlatadi, chunki
  IPM-20'da barcha 4 subshkala bir xil 5–25 shkala va chegaralarga ega).
  `resultKey` umumiydan alohida nom fazosida (masalan subshkalada `high`
  bo'lishi umumiydagi `high`ga aralashmaydi — `subscaleKey` composite
  unikal kalitga kiradi).
- `ScoreScaleScorer` javoblarni `subscaleKey` bo'yicha guruhlaydi, har
  guruh uchun `subscaleKey='*'` oraliq/talqinni topadi va
  `BreakdownItem(label: subscaleKey, value, resultKey, title, description)`
  yaratadi — `label` savolning subscaleKey qiymati (talaba tilidagi to'liq
  nom, chunki har til o'z Quiz'ida alohida saqlanadi).
- Subshkalasi yo'q metodikalarda (Zung) bu guruhlash bo'sh qoladi — eski
  `[{label:'score', value: total}]` xatti-harakat o'zgarmaydi.

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
