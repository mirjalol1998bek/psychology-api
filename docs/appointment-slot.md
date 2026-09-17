# AppointmentSlot

Psixolog qabul kalendari sloti.

| Maydon | Tip | Izoh |
|---|---|---|
| `psychologist` | ManyToOne `User`, not null | |
| `student` | ?ManyToOne `User` | band bo'lganda |
| `date` | date | |
| `startTime` / `endTime` | string `HH:MM` | |
| `status` | `AppointmentStatus` | `free` / `booked` / `cancelled` |
| `title` | ?string | psixologning shaxsiy izohi — talabaga hech qachon ko'rsatilmaydi |
| `room` | ?string | |
| `createdAt` | datetime | |

Jadval: `appointment_slot`.

`status=free` slot — psixolog **band qilish mumkin bo'lgan vaqt oralig'i**
(masalan "dushanba, 14:00–18:00"). `endTime` bu holda **shart**
(`AppointmentSlot::validate()` — Assert\Callback — `endTime` yo'q yoki
`startTime`dan katta bo'lmasa `422` beradi). Talaba shu oraliq ichidan
istalgan vaqtga (2 soatlik blok sig'adigan qilib) qabulga yozilishi mumkin —
pastga qarang.

## Maxfiylik — kim nimani ko'radi

`GET` (`IS_AUTHENTICATED_FULLY`, hamma uchun ochiq — talaba ham o'z psixologi
kalendarini ko'rishi kerak, **bo'sh vaqtlarni topib qabulga yozilish uchun**).
Talabaga psixologning **shaxsiy yozuvlari (sarlavha) va o'zgalarga tegishli
tafsilotlar hech qachon ko'rsatilmaydi**:

- `ROLE_PSYCHOLOGIST`/`ROLE_ADMIN` — barcha maydonlar to'liq.
- Talaba, **o'z** band sloti (`student == joriy foydalanuvchi`) — to'liq.
- Talaba, **boshqa birovning** band sloti — `student`/`title`/`room`
  `null` qilib beriladi (`status=booked` qoladi — vaqt oralig'i bilan
  birga shunchaki "band" ko'rinadi).
- Talaba, `free` (bo'sh vaqt oralig'i) — `title`/`room` `null` qilib
  beriladi (psixologning ichki yozuvi sir), faqat sana/vaqt/`status` ko'rinadi.
- Talaba, `cancelled` — **butunlay chiqarib tashlanadi** (bu psixologning
  ichki holati, talabaga hech qanday ahamiyati yo'q).

Bajaruvchi: `AppointmentSlotCollectionProvider` (ORM'ning standart
`CollectionProvider`'ini o'raydi — filtr/paginatsiya saqlanadi, natija talaba
uchun nusxalanib maydonlari `null` qilinadi/cancelled chiqarib tashlanadi,
DB'ga yozilmaydi). `Get` (bitta slot) esa `security` bilan cheklangan:
`object.getStudent() == user || object.getStudent() == null || is_granted('ROLE_PSYCHOLOGIST')`.

## Qabulga yozilish — bo'sh oraliqni "iste'mol qilish"

`POST /api/appeals/{id}/book` orqali yaratilgan band slotlar doim
**aniq 2 soatlik** (`endTime = startTime + 2h`) — [`appeal.md`](appeal.md)
dagi "Qabulga yozilish oqimi"ga qarang. Bron qilinganda so'ralgan 2 soatlik
blokni **to'liq qamrab oluvchi `free` slot topilishi shart** (bo'lmasa `422`).
Topilgach shu `free` slot band qilingan blokka moslab **qisqartiriladi /
bo'linadi**:

- Blok butun oraliqni egallasa — `free` slot o'chiriladi.
- Blok oraliqning boshida — `free.startTime` blok oxiriga suriladi.
- Blok oraliqning oxirida — `free.endTime` blok boshiga suriladi.
- Blok o'rtada — oraliq ikkiga bo'linadi (blokdan oldingi va keyingi qismlar
  alohida `free` slot bo'lib qoladi).

Bajaruvchi: `AvailabilityWindowConsumer`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/appointment_slots`, `.../{id}` | auth (yuqoridagi maxfiylik qoidasi bilan) |
| `POST` / `PATCH` / `DELETE` | `ROLE_PSYCHOLOGIST` |

Filtr: `DateFilter` (`date`).
