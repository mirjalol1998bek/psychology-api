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
| `POST /api/attempts/{id}/reset` | **`ROLE_PSYCHOLOGIST`** — javob+natijani tozalab qayta topshirishga ruxsat |
| `DELETE /api/attempts/{id}` | **`ROLE_ADMIN`** — urinish + natijani butunlay o'chirish |

`assignment` FK — **`ON DELETE SET NULL`**: biriktirish (`Assignment`) o'chirilsa
urinish saqlanadi, `assignment_id` → `NULL` bo'ladi.

**Bir marta qoidasi:** `AttemptStarter::start()` mavjud urinishni qaytaradi, shuning
uchun talaba yakunlangan testni qayta topshira olmaydi. Faqat xodim `reset` (yoki
admin `DELETE`) qilsa — talaba yana bir marta topshiradi. Talabaning o'zi
`reset` qila olmaydi (frontend'da "Qayta topshirish" tugmasi yo'q).

Oqim: [`assessment-flow.md`](assessment-flow.md).
