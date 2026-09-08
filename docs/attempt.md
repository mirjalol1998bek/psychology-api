# Attempt

Talabaning bitta metodika bo'yicha urinishi.

| Maydon | Tip | Izoh |
|---|---|---|
| `student` | ManyToOne `User`, not null | |
| `assignment` | ?ManyToOne `Assignment` | boshlashda topilgan ochiq biriktirish |
| `quiz` | ManyToOne `Quiz`, not null | til bo'yicha tanlanadi |
| `status` | `AttemptStatus` | `not_started` / `in_progress` / `submitted` / `reviewed` |
| `answers` | OneToMany `AttemptAnswer`, cascade + orphanRemoval | |
| `result` | OneToOne `AssessmentResult`, cascade | yakunlangach |
| `createdAt` / `updatedAt` / `submittedAt` | datetime | |

Unikal: (`student`, `quiz`) — bir quiz bo'yicha bitta urinish.
`getStudyLanguage()` (quiz'dan), `getIsSubmitted()`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/attempts` | `StudentAttemptProvider` (o'zi / xodim hammasi) |
| `GET /api/attempts/{id}` | egasi yoki `ROLE_PSYCHOLOGIST` |
| `POST /api/attempts/start` | `{categoryId}` → `AttemptStartAction` |
| `POST /api/attempts/{id}/answers` | `{answers:[...]}` → `AttemptSaveAnswersAction` |
| `POST /api/attempts/{id}/submit` | `AttemptSubmitAction` (ballash) |

Oqim: [`assessment-flow.md`](assessment-flow.md).
