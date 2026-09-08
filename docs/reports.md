# Hisobotlar / eksport

Psixolog va admin guruh kesimida natija va pasportlarni jadval ko'rinishida
oladi (CSV eksport frontend'da).

## Guruh natijalari

`GET /api/admin/group_results?studyGroup={id}&category={id}` — **`ROLE_PSYCHOLOGIST`**.

`src/Component/Assessment/Report/GroupResultReporter` — `AssessmentResult` ni
`attempt.student.studyGroup` + `attempt.quiz.category` bo'yicha qidiradi,
`GroupResultRow` (`readonly`) massivini beradi:

```json
[{ "studentId": 2, "hemisId": "50001", "fullName": "Talaba 1",
   "resultKey": "Doira", "label": "Doira", "score": null,
   "submittedAt": "2026-09-08T06:41:16+00:00" }]
```

`score` — faqat `SCORE_SCALE` metodikalarida to'ladi.

## Guruh pasportlari

Alohida endpoint kerak emas — mavjud kolleksiya filtri:

`GET /api/student_passports?student.studyGroup={id}` — **`ROLE_PSYCHOLOGIST`**.

Bu operatsiya `passport:read:staff` guruhini ham qo'shadi, shu sababli har
qatorda `student` (ism, guruh) va `completeness` ko'rinadi.
