# ScoreRange

`SCORE_SCALE` metodikasi uchun ball oralig'i → natija kaliti (Zung, Ibodullayev).

| Maydon | Tip | Izoh |
|---|---|---|
| `category` | ManyToOne `Category`, not null | |
| `minScore` | smallint | |
| `maxScore` | smallint | |
| `resultKey` | string | `AssessmentInterpretation.resultKey` bilan mos |
| `studyLanguage` | ?`StudyLanguage` | `null` = barcha tillar uchun |

`containsScore(int): bool` = `min <= score <= max`.

`ScoreScaleScorer` mos oraliqni topadi (til `null` yoki teng **va**
`containsScore`); topilmasa `ScoreRangeNotFoundException` (konfiguratsiya xatosi).

## API

`GET` — auth; `POST`/`PATCH`/`DELETE` — `ROLE_PSYCHOLOGIST`. Filtr: `category`.
Jadval: `score_range`.
