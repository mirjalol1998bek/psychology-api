# AppointmentSlot

Psixolog qabul kalendari sloti.

| Maydon | Tip | Izoh |
|---|---|---|
| `psychologist` | ManyToOne `User`, not null | |
| `student` | ?ManyToOne `User` | band bo'lganda |
| `date` | date | |
| `startTime` / `endTime` | string `HH:MM` | |
| `status` | `AppointmentStatus` | `free` / `booked` / `cancelled` |
| `title` | ?string | |
| `room` | ?string | |
| `createdAt` | datetime | |

Jadval: `appointment_slot`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/appointment_slots`, `.../{id}` | auth |
| `POST` / `PATCH` / `DELETE` | `ROLE_PSYCHOLOGIST` |

Filtr: `DateFilter` (`date`).
