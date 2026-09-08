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
