# User

Talaba / psixolog / admin. Skeletdagi `User` **kengaytirilgan** (qayta yozilmagan).

## Maydonlar (skelet + qo'shilgan)

| Maydon | Tip | Izoh |
|---|---|---|
| `email` | string, **unikal** | JWT identifikatori. Talabada sintetik: `{hemisId}@students.uzswlu.uz` |
| `password` | string | HEMIS talabasida tasodifiy (login uchun emas) |
| `roles` | json | `RoleEnum` qiymatlari |
| `hemisId` | ?string, unikal | HEMIS identifikatori |
| `fullName` | ?string | |
| `studyLanguage` | ?`StudyLanguage` | yo'q bo'lsa `studyGroup` dan olinadi |
| `image` | ?string | |
| `isActive` | bool = true | `getIsActive()` |
| `studyGroup` | ManyToOne `StudyGroup` | talaba uchun |
| audit | `createdAt/updatedAt/By`, `deletedAt/By` (yumshoq o'chirish) |

## Metodlar

`getStudyLanguage()` (guruhdan meros), `getFaculty()` (guruh → fakultet),
`getPrimaryRole(): RoleEnum`, `hasRole(RoleEnum): bool`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/users` | `ROLE_ADMIN` |
| `GET /api/users/{id}` | o'zi yoki `ROLE_ADMIN` |
| `POST /api/users` | ochiq (skelet) |
| `PATCH /api/users/{id}` | o'zi yoki `ROLE_ADMIN` |
| `DELETE /api/users/{id}` | o'zi yoki `ROLE_ADMIN` (yumshoq) |
| `POST /api/users/auth`, `.../refreshToken`, `/api/users/about_me` | auth |
| `POST /api/students` | **`ROLE_ADMIN`** — `StudentCreateAction` + `StudentFactory`; `hemisId` unikal tekshiriladi |

## Invariantlar

- `email` har doim mavjud va unikal.
- Talaba yaratishda rol `[ROLE_STUDENT]`, `isActive = true`.
