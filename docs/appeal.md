# Appeal

Talaba → psixolog murojaati (anonim yoki ismli) + javob.

| Maydon | Tip | Izoh |
|---|---|---|
| `student` | ManyToOne `User`, not null | anonimda ham saqlanadi (javob uchun) |
| `mode` | `AppealMode` | `named` / `anonymous` |
| `topic` | `AppealTopic` | `question` / `appointment` / `stress` / `other` |
| `message` | text (max 2000) | |
| `wantsAppointment` | bool = false | `getWantsAppointment()` |
| `status` | `AppealStatus` | `open` / `answered` |
| `reply` | ?text | |
| `repliedBy` | ?ManyToOne `User` | javob bergan psixolog |
| `repliedAt` | ?datetime | |
| `createdAt` | datetime | |

`getIsAnonymous(): bool`, `getSenderName(): ?string` — anonimda `null`.
`student` faqat `appeal:read:staff` guruhida; anonimda serializatsiya
qilinmaydi.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/appeals` | `AppealCollectionProvider` — talabaga o'zi, xodimga hammasi |
| `GET /api/appeals/{id}` | egasi yoki `ROLE_PSYCHOLOGIST` |
| `POST /api/appeals` | auth — `AppealCreateProcessor` → `AppealCreator` (student = joriy, xodimlarga bildirishnoma) |
| `POST /api/appeals/{id}/reply` | `ROLE_PSYCHOLOGIST` — `AppealReplyAction` → `AppealReplyWriter` (status `answered`, talabaga bildirishnoma) |

Filtr: `status`, `topic`, `student`.
