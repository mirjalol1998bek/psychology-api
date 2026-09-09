# Faculty

Fakultet (HEMIS'dan yoki admin qo'lda).

| Maydon | Tip | Izoh |
|---|---|---|
| `name` | string | |
| `externalId` | ?string, unikal | HEMIS ID |
| `groups` | OneToMany `StudyGroup` (mappedBy `faculty`) | |
| audit | `createdAt`, `updatedAt` | |

Serializatsiya: `getGroupCount(): int`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/faculties`, `GET /api/faculties/{id}` | auth |
| `POST` / `PATCH` / `DELETE` | `ROLE_ADMIN` |
| `POST /api/admin/hemis/faculties` | `ROLE_ADMIN` — HEMIS'dan sinxron |
| `GET /api/admin/hemis/faculties/{id}/groups` | `ROLE_ADMIN` — HEMIS guruhlari ro'yxati |
| `POST /api/admin/hemis/faculties/{id}/groups/{groupExternalId}` | `ROLE_ADMIN` — guruh + talabalarni import |

To'liq: [`hemis-sync.md`](hemis-sync.md). `externalId` = HEMIS department `id`.
Filtr: `SearchFilter` (`name`, `externalId`).
