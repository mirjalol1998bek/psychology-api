# StudyGroup

Guruh — fakultetga tegishli, ta'lim tili bor.

| Maydon | Tip | Izoh |
|---|---|---|
| `faculty` | ManyToOne `Faculty`, **not null** | |
| `name` | string | masalan `22-11` |
| `studyLanguage` | `StudyLanguage` = `uz` | talabaning tilini belgilaydi |
| `externalId` | ?string | HEMIS ID |
| `students` | OneToMany `User` (mappedBy `studyGroup`) | |
| audit | `createdAt`, `updatedAt` | |

Serializatsiya: `getStudentCount(): int`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/study_groups`, `.../{id}` | auth |
| `POST` / `PATCH` / `DELETE` | `ROLE_ADMIN` |

Filtr: `SearchFilter` (`name`, `faculty`, `studyLanguage`), `OrderFilter`.
