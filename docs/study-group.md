# StudyGroup

Guruh — fakultetga tegishli, ta'lim tili bor.

| Maydon | Tip | Izoh |
|---|---|---|
| `faculty` | ManyToOne `Faculty`, **not null** | |
| `name` | string | masalan `22-11` |
| `studyLanguage` | `StudyLanguage` = `uz` | talabaning tilini belgilaydi |
| `externalId` | ?string, **unikal** | HEMIS group `id` (sinxron kaliti) |
| `students` | OneToMany `User` (mappedBy `studyGroup`) | |
| `tutor` | ?ManyToOne `User`, `onDelete: SET NULL` | Guruh tyutori — HEMIS `employee-list`dagi `tutorGroups`dan proaktiv sinxronlanadi ([`hemis-sync.md`](hemis-sync.md)). Bitta tyutor 10-20+ guruhga biriktirilishi mumkin (many-to-one) |
| audit | `createdAt`, `updatedAt` | |

Serializatsiya: `getStudentCount(): int`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/study_groups`, `.../{id}` | auth |
| `POST` / `PATCH` / `DELETE` | `ROLE_ADMIN` |
| `POST /api/admin/hemis/groups/{id}/students` | `ROLE_ADMIN` — guruh talabalarini HEMIS'dan sinxron ([`hemis-sync.md`](hemis-sync.md)) |
| `GET /api/tutor/students` | `ROLE_TUTOR` — joriy tyutorga biriktirilgan barcha guruhlar talabalari (`{id, fullName, hemisId, image, studyGroup:{id,name}}`, guruh nomi bo'yicha tartiblangan). Test natijalarini qamramaydi — shu maqsadda ataylab yengil, alohida javob shakli (`TutorStudentsAction`, entity graph emas) |

Filtr: `SearchFilter` (`name`, `faculty`, `studyLanguage`, `externalId`), `OrderFilter`.
