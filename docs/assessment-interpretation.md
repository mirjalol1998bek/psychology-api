# AssessmentInterpretation

Natija matni: temperament tipi / figura tavsifi / ball oralig'i xulosasi. uz/ru.

| Maydon | Tip | Izoh |
|---|---|---|
| `category` | ManyToOne `Category`, not null | |
| `resultKey` | string | scorer qaytargan kalit (`Doira`, `mild`, `Xolerik`, ...) |
| `studyLanguage` | `StudyLanguage` | |
| `title` | ?string | `AssessmentResult.label` ga tushadi |
| `text` | text | `AssessmentResult.description` ga tushadi |

Unikal: (`category`, `resultKey`, `studyLanguage`).
Jadval: `assessment_interpretation`.

`AttemptSubmitter` `findOneBy(category, resultKey, attempt.studyLanguage)` bilan
qidiradi. Topilmasa natija `label = resultKey`, `description = ''`.

## API

`GET` — auth; `POST`/`PATCH`/`DELETE` — `ROLE_PSYCHOLOGIST`.
Filtr: `category`, `resultKey`, `studyLanguage`.
