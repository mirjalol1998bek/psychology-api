# Test topshirish oqimi

## 1. Biriktirish (psixolog/admin)

`POST /api/assignments` — `{category, studyGroup, startAt, endAt?}`.
`Assignment.isOpenAt(now)` = `isActive && startAt <= now && (endAt == null || now <= endAt)`.

## 2. Talaba biriktirilganlarni ko'radi

`GET /api/assignments` → `StudentAssignmentProvider`:
xodimga hammasi; talabaga — o'z guruhi va **hozir ochiq** bo'lganlar.

## 3. Urinishni boshlash

`POST /api/attempts/start` — `{categoryId}` → `AttemptStartAction` → `AttemptStarter`:

- Talaba guruhi + kategoriya bo'yicha ochiq `Assignment` qidiriladi
  (`AssignmentNotFoundException` = 403 bo'lmasa).
- `QuizProvider::forCategoryAndLanguage(category, student.studyLanguage)` —
  mos til yo'q bo'lsa uz.
- Mavjud `Attempt` (student+quiz unikal) bo'lsa qaytariladi, aks holda
  `AttemptFactory` yangi `Attempt(status=InProgress)` yaratadi.

## 4. Javoblarni saqlash

`POST /api/attempts/{id}/answers` — `{answers:[{questionId, optionIds:[], text?}]}` →
`AttemptSaveAnswersAction` (`AccessDeniedHttpException` boshqa talaba urinishida) →
`AttemptAnswerRecorder`:

- `AttemptLockedException` (409) agar urinish yakunlangan bo'lsa.
- Eski javoblar tozalanadi (`orphanRemoval`), yangilari yoziladi.
  Noma'lum `questionId` → `UnknownQuestionException` (400).

Bir necha marta chaqirsa bo'ladi (autosave / to'liq almashtirish).

## 5. Yakunlash

`POST /api/attempts/{id}/submit` → `AttemptSubmitAction` → `AttemptSubmitter`
(ballash — [`assessment-scoring.md`](assessment-scoring.md)). Natija `Attempt.result`.

Yakunlangach talaba qayta topshira olmaydi (§3 mavjud urinish qaytariladi).
Xodim `POST /api/attempts/{id}/reset` (`ROLE_PSYCHOLOGIST`) qilsa — javob+natija
tozalanadi, status `in_progress`, talaba yana bir marta topshiradi. Admin
`DELETE /api/attempts/{id}` — urinishni butunlay o'chiradi (xato natijani tozalash).

## 6. Natijalar

- `GET /api/attempts` — `StudentAttemptProvider` (talabaga o'zi, xodimga hammasi).
- `GET /api/assessment_results` — `StudentResultProvider` (xuddi shunday).
- `GET /api/assessment_results/{id}` — egasi yoki psixolog.

## Xatoliklar

| Holat | Istisno | HTTP |
|---|---|---|
| Ochiq assignment yo'q | `AssignmentNotFoundException` | 403 |
| Til bo'yicha (va uz) quiz yo'q | `QuizNotFoundException` | 404 |
| Yakunlangan urinishni tahrirlash | `AttemptLockedException` | 409 |
| Noma'lum savol | `UnknownQuestionException` | 400 |
| `SCORE_SCALE` da mos oraliq yo'q | `ScoreRangeNotFoundException` | 500 (konfiguratsiya xatosi) |
