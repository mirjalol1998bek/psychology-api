# Assignment

Kategoriyani (metodikani) guruhga biriktirish + muddat.

| Maydon | Tip | Izoh |
|---|---|---|
| `category` | ManyToOne `Category`, not null | |
| `studyGroup` | ManyToOne `StudyGroup`, not null | |
| `startAt` | datetime | |
| `endAt` | ?datetime | `null` = muddatsiz |
| `isActive` | bool = true | `getIsActive()` |
| `createdAt` | datetime | |

`isOpenAt(moment): bool` = `isActive && startAt <= moment && (endAt === null || moment <= endAt)`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/assignments` | `StudentAssignmentProvider` — xodimga hammasi, talabaga o'z guruhi + hozir ochiq |
| `GET /api/assignments/{id}` | auth |
| `POST` / `PATCH` / `DELETE` | `ROLE_PSYCHOLOGIST` |

Filtr: `category`, `studyGroup`, `isActive`.

## O'chirish

Guruhning ba'zi talabalari testni allaqachon topshirgan bo'lsa ham `DELETE`
ishlaydi: `attempt.assignment_id` FK **`ON DELETE SET NULL`** — urinish va
natija saqlanadi (talaba Natijalar'da ko'rishda davom etadi), faqat
biriktirish bilan bog'liqlik uziladi. Urinishni ham o'chirish kerak bo'lsa —
alohida `DELETE /api/attempts/{id}` (`ROLE_ADMIN`).
