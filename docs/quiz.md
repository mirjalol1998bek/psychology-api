# Quiz

Kategoriya ichidagi test — bitta til varianti.

| Maydon | Tip | Izoh |
|---|---|---|
| `category` | ManyToOne `Category`, not null | |
| `title` | string | |
| `description` | ?string | |
| `studyLanguage` | `StudyLanguage` | bitta kategoriyada uz va ru quiz bo'lishi mumkin |
| `timeLimitMinutes` | smallint = 0 | 0 = cheklovsiz |
| `isActive` | bool = true | |
| `questions` | OneToMany `Question`, cascade persist+remove, `OrderBy position` | |
| audit | `createdAt`, `updatedAt` | |

`getQuestionCount()`, `addQuestion()`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/quizzes` | auth |
| `GET /api/quizzes/{id}` | auth — `quiz:read:full` (savollar + variantlar bilan) |
| `POST`, `PATCH` | `ROLE_PSYCHOLOGIST` |
| `DELETE` | `ROLE_ADMIN` |

`QuizProvider::forCategoryAndLanguage()` — talaba tili bo'yicha, yo'q bo'lsa uz.
