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

Filtr: `SearchFilter` (`name`, `externalId`).
