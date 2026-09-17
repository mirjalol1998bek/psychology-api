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

## Maxfiylik — kim nimani ko'radi

`GET` (`IS_AUTHENTICATED_FULLY`, hamma uchun ochiq — talaba ham o'z psixologi
kalendarini ko'rishi kerak, **bo'sh vaqtlarni topib qabulga yozilish uchun**).
Lekin band slotning **kimga tegishli ekani boshqalarga sir**:

- `ROLE_PSYCHOLOGIST`/`ROLE_ADMIN` — barcha maydonlar to'liq.
- Talaba, **o'z** sloti (`student == joriy foydalanuvchi`) — to'liq.
- Talaba, **boshqa birovning** band sloti — `student`/`title`/`room`
  `null` qilib beriladi (`status=booked` qoladi — shunchaki "band" ko'rinadi).
- `free` / `cancelled` (egasiz) slotlar — hech kimga sir emas, to'liq beriladi.

Bajaruvchi: `AppointmentSlotCollectionProvider` (ORM'ning standart
`CollectionProvider`'ini o'raydi — filtr/paginatsiya saqlanadi, natija talaba
uchun nusxalanib maydonlari `null` qilinadi, DB'ga yozilmaydi). `Get` (bitta
slot) esa `security` bilan cheklangan:
`object.getStudent() == user || object.getStudent() == null || is_granted('ROLE_PSYCHOLOGIST')`.

`POST /api/appeals/{id}/book` orqali yaratilgan band slotlar doim
**aniq 2 soatlik** (`endTime = startTime + 2h`) — [`appeal.md`](appeal.md)
dagi "Qabulga yozilish oqimi"ga qarang.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/appointment_slots`, `.../{id}` | auth (yuqoridagi maxfiylik qoidasi bilan) |
| `POST` / `PATCH` / `DELETE` | `ROLE_PSYCHOLOGIST` |

Filtr: `DateFilter` (`date`).
