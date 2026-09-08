# Category

Bitta metodika (Temperament, Psixogeometrik, Zung, ...) + ballash algoritmi.

| Maydon | Tip | Izoh |
|---|---|---|
| `name` | string | |
| `description` | ?string | |
| `instrumentType` | `InstrumentType` | **ballash algoritmi** (4 qiymat) — [`assessment-scoring.md`](assessment-scoring.md) |
| `position` | smallint | tartib |
| `isActive` | bool = true | `getIsActive()` |
| `quizzes` | OneToMany `Quiz` | (cascade emas) |
| `scoreRanges` | OneToMany `ScoreRange`, cascade persist+remove | faqat `SCORE_SCALE` |
| `interpretations` | OneToMany `AssessmentInterpretation`, cascade persist+remove | uz/ru natija matni |
| audit | `createdAt`, `updatedAt` | |

`addScoreRange()`, `addInterpretation()` — teskari tomonni ham o'rnatadi
(cascade to'g'ri ishlashi uchun).

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/categories`, `.../{id}` | auth |
| `POST`, `PATCH` | `ROLE_PSYCHOLOGIST` |
| `DELETE` | `ROLE_ADMIN` |

Filtr: `instrumentType`, `isActive`; `OrderFilter` (`position`, `name`).
