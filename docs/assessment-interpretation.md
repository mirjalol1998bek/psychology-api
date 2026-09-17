# AssessmentInterpretation

Natija matni: temperament tipi / figura tavsifi / ball oralig'i xulosasi. uz/ru.

| Maydon | Tip | Izoh |
|---|---|---|
| `category` | ManyToOne `Category`, not null | |
| `resultKey` | string | scorer qaytargan kalit (`Doira`, `mild`, `Xolerik`, ...) |
| `subscaleKey` | string = `''` | `''` = umumiy natija uchun; `'*'` = subshkala ballari uchun (nomiga bog'liq bo'lmagan) umumiy talqin — [`assessment-scoring.md`](assessment-scoring.md) |
| `studyLanguage` | `StudyLanguage` | |
| `title` | ?string | `AssessmentResult.label` ga tushadi (umumiy) yoki breakdown elementi ichida (subshkala) |
| `text` | text | `AssessmentResult.description` ga tushadi (umumiy) yoki breakdown elementi ichida (subshkala) |

Unikal: (`category`, `subscaleKey`, `resultKey`, `studyLanguage`).
Jadval: `assessment_interpretation`.

`AttemptSubmitter` umumiy natija uchun
`findOneBy(category, subscaleKey: '', resultKey, attempt.studyLanguage)`
bilan qidiradi. Topilmasa natija `label = resultKey`, `description = ''`.
Subshkala talqinini `ScoreScaleScorer` o'zi `subscaleKey: '*'` bilan qidiradi
(breakdown elementiga qo'shadi).

## API

`GET` — auth; `POST`/`PATCH`/`DELETE` — `ROLE_PSYCHOLOGIST`.
Filtr: `category`, `resultKey`, `studyLanguage`.
