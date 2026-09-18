# ScoreRange

`SCORE_SCALE` metodikasi uchun ball oralig'i → natija kaliti (masalan Ibodullayev shkalasi).

| Maydon | Tip | Izoh |
|---|---|---|
| `category` | ManyToOne `Category`, not null | |
| `minScore` | smallint | |
| `maxScore` | smallint | |
| `resultKey` | string | `AssessmentInterpretation.resultKey` bilan mos |
| `studyLanguage` | ?`StudyLanguage` | `null` = barcha tillar uchun |
| `subscaleKey` | string = `''` | `''` = umumiy ball uchun oraliq; `'*'` = subshkala ballari uchun (nomiga bog'liq bo'lmagan) umumiy oraliq — [`assessment-scoring.md`](assessment-scoring.md) |

`containsScore(int): bool` = `min <= score <= max`.

`ScoreScaleScorer` mos oraliqni topadi (til `null` yoki teng, `subscaleKey`
teng **va** `containsScore`); topilmasa `ScoreRangeNotFoundException`
(konfiguratsiya xatosi).

## API

`GET` — auth; `POST`/`PATCH`/`DELETE` — `ROLE_PSYCHOLOGIST`. Filtr: `category`.
Jadval: `score_range`.
