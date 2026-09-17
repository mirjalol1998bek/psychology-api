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
| `status` | `UserStatusEnum` = `active` | `pending`/`active`/`rejected` — kirish ruxsati (`auth.md`) |
| `studyGroup` | ManyToOne `StudyGroup` | talaba uchun |
| audit | `createdAt/updatedAt/By`, `deletedAt/By` (yumshoq o'chirish) |

## Metodlar

`getStudyLanguage()` (guruhdan meros), `getFaculty()` (guruh → fakultet),
`getPrimaryRole(): RoleEnum`, `hasRole(RoleEnum): bool`.

`Faculty::name`/`id`'da `user:read` guruhi bor — shu tufayli `User`
normalize qilinganda ichma-ich `faculty` (va `studyGroup.faculty`) to'liq
`{id, name}` obyekti sifatida keladi, sof IRI emas (API Platform: nested
resource'ning hech bir maydoni joriy guruh filtridan o'tmasa, IRI'ga
tushib qoladi). Shu orqali front `about_me`dan fakultet nomini oladi.

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
| `POST /api/users/{id}/approve` | **`ROLE_ADMIN`** — `UserApproveAction`; body `{"role":"ROLE_PSYCHOLOGIST"\|"ROLE_ADMIN"}`; `status=active` + rol |
| `POST /api/users/{id}/reject` | **`ROLE_ADMIN`** — `UserRejectAction`; `status=rejected` |

`GET /api/users` da `SearchFilter` `status` (`exact`) — admin `?status=pending`
bilan tasdiq kutayotganlarni oladi.

## Invariantlar

- `email` har doim mavjud va unikal.
- Talaba yaratishda rol `[ROLE_STUDENT]`, `isActive = true`.
