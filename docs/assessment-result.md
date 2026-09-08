# AssessmentResult

Hisoblangan natija — `AttemptSubmitter` yaratadi.

| Maydon | Tip | Izoh |
|---|---|---|
| `attempt` | OneToOne `Attempt` (inversedBy `result`), not null | |
| `resultKey` | string | scorer kaliti |
| `label` | string | `AssessmentInterpretation.title` yoki `resultKey` |
| `description` | text | `AssessmentInterpretation.text` yoki `''` |
| `score` | ?int | faqat `SCORE_SCALE` |
| `breakdown` | json — `list<{label: string, value: int}>` | denormallashtirilgan snapshot (tech.md §12 assotsiativ massiv istisnosi) |
| `createdAt` | datetime | |

Jadval: `assessment_result`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/assessment_results` | `StudentResultProvider` (o'zi / xodim hammasi) |
| `GET /api/assessment_results/{id}` | egasi yoki `ROLE_PSYCHOLOGIST` |

Filtr: `attempt.student`, `attempt.quiz.category`, `resultKey`.
