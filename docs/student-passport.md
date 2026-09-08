# StudentPassport

Ijtimoiy-psixologik pasport so'rovnomasi — har talabada bitta.
Manba: `psychology-front/.../Ижтимоий_психологик_портрет_база_.xlsx`.

| Maydon | Tip | Izoh |
|---|---|---|
| `student` | OneToOne `User`, not null | |
| `birthDate` | ?date | |
| `currentAddress` | ?string(512) | |
| `phone` | ?string(32) | |
| `familyStatus` | ?`FamilyStatus` | `married` / `single` |
| `livingEnvironment` | ?`LivingEnvironment` | `calm` / `problematic` |
| `talents` | ?text | |
| `parentsInfo` | ?text | |
| `tutorInfo` | ?text | |
| `updatedAt` | ?datetime | |

`getCompleteness(): int` — 7 majburiy maydon bo'yicha foiz.
Jadval: `student_passport`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/student_passport` | `CurrentUserPassportProvider` — mavjud bo'lmasa bo'sh (saqlanmagan) obyekt |
| `PUT /api/student_passport` | `PassportPutProcessor` — `student` = joriy foydalanuvchi |
| `GET /api/student_passports` | `ROLE_PSYCHOLOGIST` (ro'yxat, eksport uchun) |

Filtr: `student`, `student.studyGroup`.
