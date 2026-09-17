# Appeal

Talaba → psixolog murojaati (anonim yoki ismli) + javob.

| Maydon | Tip | Izoh |
|---|---|---|
| `student` | ManyToOne `User`, not null | anonimda ham saqlanadi (javob uchun) |
| `mode` | `AppealMode` | `named` / `anonymous` |
| `topic` | `AppealTopic` | `question` / `appointment` / `stress` / `other` |
| `message` | text (max 2000) | |
| `wantsAppointment` | bool = false | `getWantsAppointment()` |
| `preferredDate` | ?date | talaba so'ragan sana (`wantsAppointment` bo'lsa) |
| `preferredTime` | ?string `HH:MM` | talaba so'ragan vaqt |
| `appointmentSlot` | ?ManyToOne `AppointmentSlot`, `ON DELETE SET NULL` | psixolog band qilgach to'ldiriladi; `Groups` yo'q (faqat pastdagi tekis getter'lar orqali ko'rinadi) |
| `status` | `AppealStatus` | `open` / `answered` |
| `reply` | ?text | |
| `repliedBy` | ?ManyToOne `User` | javob bergan psixolog |
| `repliedAt` | ?datetime | |
| `createdAt` | datetime | |

`getIsAnonymous(): bool`, `getSenderName(): ?string` — anonimda `null`.
`student` faqat `appeal:read:staff` guruhida; anonimda serializatsiya
qilinmaydi.

`appointmentSlot` bog'langanda tekis (flattened) o'qish uchun:
`getAppointmentDate()`, `getAppointmentStartTime()`, `getAppointmentEndTime()`,
`getAppointmentStatus()` — hammasi `?string`, slot yo'q bo'lsa `null`.

## Qabulga yozilish oqimi

1. Talaba murojaat yuboradi: `wantsAppointment=true` + `preferredDate`/`preferredTime`
   (qachon kelmoqchi bo'lsa).
2. Psixolog `Murojaat` sahifasida shu vaqtni ko'radi, **"Qabul qilish"** tugmasi
   bilan tasdiqlaydi (kerak bo'lsa sana/vaqtni o'zgartirib).
3. `POST /api/appeals/{id}/book` — `AppealBookAppointmentAction` →
   `AppealAppointmentBooker`:
   - Kiritilgan 2 soatlik blokni **to'liq qamrab oluvchi `free` slot**
     (psixolog belgilagan bo'sh vaqt oralig'i) borligini tekshiradi — topilmasa
     `422` ("bu vaqtda psixolog qabul soatlari yo'q").
   - Shu psixolog uchun kiritilgan sana/vaqtda **band emasligini** tekshiradi
     (bor bo'lsa `409`).
   - Yangi `AppointmentSlot` yaratadi: `student` = murojaat egasi,
     `status=booked`, `endTime = startTime + 2 soat` (**har doim aniq 2 soat —
     uzunroq bo'lmaydi**; masalan 10:00 → 12:00).
   - Qamrab olgan `free` slotni band qilingan blokka moslab
     qisqartiradi/bo'ladi (`AvailabilityWindowConsumer`) —
     [`appointment-slot.md`](appointment-slot.md)ga qarang.
   - `appeal.appointmentSlot` shu slotga bog'lanadi; `reply` bo'sh bo'lsa
     avtomatik tasdiq matni yoziladi, `status=answered`.
   - Talabaga bildirishnoma (`NotificationType::AppealAppointmentBooked`).

Qabul kalendarida (`AppointmentSlot`) bu slot **faqat shu talabaga** to'liq
ko'rinadi — boshqa har kimga (talaba) faqat "band" holati, egasi/sarlavhasi
yashirin (pastga qarang).

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/appeals` | `AppealCollectionProvider` — talabaga o'zi, xodimga hammasi |
| `GET /api/appeals/{id}` | egasi yoki `ROLE_PSYCHOLOGIST` |
| `POST /api/appeals` | auth — `AppealCreateProcessor` → `AppealCreator` (student = joriy, xodimlarga bildirishnoma) |
| `POST /api/appeals/{id}/reply` | `ROLE_PSYCHOLOGIST` — `AppealReplyAction` → `AppealReplyWriter` (status `answered`, talabaga bildirishnoma) |
| `POST /api/appeals/{id}/book` `{date, startTime}` | `ROLE_PSYCHOLOGIST` — `AppealBookAppointmentAction` → `AppealAppointmentBooker` (yuqoridagi oqim) |

Filtr: `status`, `topic`, `student`.
