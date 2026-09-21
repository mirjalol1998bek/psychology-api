# HEMIS org sinxroni

Fakultet / guruh / talabalarni HEMIS **talaba REST API**'dan (`student.uzswlu.uz`)
bizning bazaga ko'chirish. OAuth (kirish) bilan aralashtirmang — bu alohida,
**token** bilan ishlaydi.

## Konfiguratsiya

| O'zgaruvchi | Joyi | Qiymat |
|---|---|---|
| `HEMIS_API_BASE_URL` | `.env` | `https://student.uzswlu.uz/rest/v1` |
| `HEMIS_API_TOKEN` | **`.env.local`** | maxfiy token |

TLS: `config/certs/hemis-ca-chain.pem` (HEMIS oraliq sertifikatni yubormaydi).

## HEMIS API xususiyatlari

- Javob konverti: `{"success":bool,"error":?string,"data":{"items":[],"pagination":{}}}`
- Sahifalash: `?page=N&limit=M` (`pagination.pageCount`)
- **Ko'p so'rovda `403`** ("Sizga ushbu harakatni bajarishga ruxsat etilmagan") —
  `HemisApiClient` sekinlashib qayta urinadi (3 marta, backoff).
- `data/department-list` — `structureType.code === "11"` → **fakultet**
- `data/group-list?_department=<facultyExtId>` — fakultet guruhlari. **Diqqat:**
  eski (bitirgan) guruhlarni ham `active:true` bilan qaytaradi (bir fakultetda
  300–600 ta). CLI nomi " Y" bilan tugaganlarni tashlaydi; UI'da guruhlar
  **to'plab import qilinmaydi** — admin kerakligini tanlaydi.
- `data/student-list?_group=<groupExtId>` — bitta guruhning **hozir o'qiyotgan**
  (`studentStatus.code === "11"`) talabalari. `student_id_number` → `User.hemisId`.
- `data/employee-list?type=employee` — xodimlar, har birida `tutorGroups`
  (`list<{id,name,educationLang}>`) — tyutor sifatida biriktirilgan guruhlar.
  **Diqqat:** bitta xodim bir nechta shtat yozuviga ega bo'lishi mumkin (turli
  lavozim/bo'lim) — sahifalashda **bir nechta marta** qaytadi, har safar bir
  xil `tutorGroups` bilan. `employee_id_number` bo'yicha guruhlab, tutorGroups
  birlashtiriladi (`HemisApiClient::fetchTutors()`).
- **`data/student-list?_department=<facultyExtId>`** — fakultetning **barcha**
  hozirgi talabalari (sahifalangan). Har yozuvda `group.id` / `group.name` /
  `group.educationLang` bor. **Asosiy yo'l:** guruhlarni shundan yig'amiz —
  faqat talabasi bor guruhlar yaratiladi, eski/bo'sh guruhlar tegilmaydi.
  Bir fakultet ≈ 5–10 sahifa (`limit=200`), butun universitet ≈ 40 so'rov.
- Ta'lim tili: `educationLang.code` — `"12"` → `ru`, boshqasi → `uz`.

## Moslashtirish (upsert)

| Bizniki | HEMIS kaliti | Yangilanadi |
|---|---|---|
| `Faculty.externalId` | department `id` | `name` |
| `StudyGroup.externalId` (UNIQUE) | group `id` | `name`, `studyLanguage`, `faculty` |
| `User.hemisId` | `student_id_number` | `fullName`, `image`, `studyGroup` |
| `User.hemisId` (tyutor) | `employee_id_number` | `fullName`, `image`; `StudyGroup.tutor` (har `tutorGroups` a'zosi uchun) |

Yangi talaba: `StudentFactory` (email `{student_id_number}@students.uzswlu.uz`,
tasodifiy parol, `ROLE_STUDENT`, `status=active`). **Mavjud foydalanuvchining
roli/holati o'zgartirilmaydi.**

Yangi tyutor: `UserFactory::createFromHemisEmployee()` (email
`{employee_id_number}@hemis.uzswlu.uz`, `status=pending`, `roles=[]` — xuddi
OAuth orqali kirgan xodim kabi, [`hemis-auth.md`](hemis-auth.md)). Admin
keyin `POST /api/users/{id}/approve {"role":"ROLE_TUTOR"}` bilan tasdiqlaydi.
Xodim keyin HEMIS orqali o'zi kirsa, `hemisId` bo'yicha **shu yozuv**
qayta ishlatiladi (rol/holat saqlanadi). Guruh biriktiruvi mavjud
`StudyGroup`larga (`externalId` bo'yicha) darhol o'rnatiladi — guruh hali
import qilinmagan bo'lsa, o'sha biriktiruv o'tkazib yuboriladi.

## Endpointlar (`ROLE_ADMIN`)

| Yo'l | Vazifasi |
|---|---|
| `POST /api/admin/hemis/faculties` | barcha fakultetlarni sinxronlash → `{created, updated}` |
| `POST /api/admin/hemis/students` | butun universitet — fon rejimida (`202`) |
| `POST /api/admin/hemis/faculties/{id}/students` | bitta fakultetning guruh+talabalari — fon rejimida (`202`) |
| `GET /api/admin/hemis/faculties/{id}/groups` | fakultetning HEMIS guruhlari (ro'yxat, **saqlanmaydi**) |
| `POST /api/admin/hemis/faculties/{id}/groups/{groupExternalId}` | bitta guruhni import + talabalarini sinxronlash → `{created, updated}` |
| `POST /api/admin/hemis/groups/{id}/students` | bitta guruh talabalarini qayta sinxronlash |
| `POST /api/admin/hemis/tutors` | tyutorlarni + guruh biriktiruvlarini sinxronlash (sinxron, `{created, updated}`) |

## CLI

```
php bin/console ask:hemis:sync            # fakultetlar + har fakultet guruhlari (" Y" siz)
php bin/console ask:hemis:sync --students # + har guruh talabalari (sekin)
```

CLI barcha faol guruhlarni oladi (yuzlab bo'lishi mumkin) — UI esa admin
tanlaganini. Front `study_groups` / `users` ni fakultet/guruh kesimida
`?itemsPerPage=` bilan bitta so'rovda oladi (`pagination_client_items_per_page`,
maksimum 2000).

## Fon rejimi — navbat, kechki jadval, tezlik cheklovi

Katta sinxron (minglab talaba) HTTP so'rovni bloklamasligi kerak. Shuning uchun
u **Symfony Messenger** orqali navbatga qo'yiladi va worker birma-bir bajaradi.

### Oqim

1. `NightlyHemisSyncMessage` (async transport, `doctrine://` — `messenger_messages`).
2. `NightlyHemisSyncHandler`:
   - `LockFactory` (`hemis-nightly-sync`, 7200s) — ikki marta parallel ishlamaydi.
   - `syncFaculties()` — fakultetlarni yangilaydi.
   - `syncTutors()` — tyutorlar + guruh biriktiruvlarini yangilaydi (sinxron,
     navbatga qo'yilmaydi — xodimlar soni talabalarnikidan ancha kam).
   - HEMIS'ga bog'langan **har fakultet** uchun (`FacultyRepository::findLinkedToHemis()`)
     bitta `SyncFacultyStudentsMessage` dispatch qiladi (14 ta xabar, mingtalab emas).
3. `SyncFacultyStudentsHandler` — `syncFacultyStudents(Faculty)`:
   `student-list?_department=` ni sahifalab oladi, har talaba uchun guruhini
   (`group.id`/`group.name`) upsert qiladi + talabani upsert qiladi. Fakultet
   qulfi (`hemis-faculty-<id>`, 1800s, non-blocking) — tez-tez bosilsa takrorlamaydi.
4. Har HEMIS HTTP chaqiruvi **rate limiter** (`framework.rate_limiter.hemis_api`,
   `token_bucket`, 3 so'rov/soniya, burst 10) bilan cheklanadi — `HemisApiClient`
   `$hemisApiLimiter->reserve(1)->wait()`. 403'da eksponensial backoff.

`SyncGroupStudentsMessage` (bitta guruh) hali bor — hozir hech kim dispatch
qilmaydi, kelajakda bitta guruhni fon rejimida yangilash uchun.

### Kechki jadval

`src/Schedule/HemisSchedule.php` — `#[AsSchedule('hemis')]`,
`RecurringMessage::cron('30 0 * * *', ...)` **Asia/Tashkent** (har kuni 00:30),
`->stateful($cache)` — kompyuter 00:30 da o'chiq bo'lsa, worker keyingi safar
ishga tushganda **o'tkazib yuborilgan ishni bajaradi**.

### Worker (supervisor)

`docker/php/supervisor/messenger-worker.conf` — `php` konteynerida 2 ta jarayon:

```
messenger:consume async scheduler_hemis --time-limit=3600 --memory-limit=192M
```

`run-daemons.sh` konteyner ishga tushganda `supervisorctl start` qiladi.

### Qo'lda ishga tushirish (darhol emas — navbatga)

| Yo'l | Vazifasi |
|---|---|
| `POST /api/admin/hemis/students` (`ROLE_ADMIN`) | **butun universitet** — har fakultet uchun `SyncFacultyStudentsMessage` (`NightlyHemisSyncMessage`) |
| `POST /api/admin/hemis/faculties/{id}/students` (`ROLE_ADMIN`) | bitta fakultetning barcha hozirgi guruh+talabalari (`SyncFacultyStudentsMessage`) |
| `php bin/console ask:hemis:sync --queue` | nightly xabarni navbatga qo'yadi |
| UI: Tashkilot → "Barcha talabalarni yangilash" | `queueHemisStudentsSync()` |
| UI: Tashkilot → HEMIS guruhlari dialogi → "Barcha guruhlarni yuklash" | `queueHemisFacultyStudents(facultyId)` |

Faqat **hozir o'qiyotgan** talabasi bor guruhlar yaratiladi — eski/bo'sh
guruhlar tegilmaydi. Bitta fakultet ≈ 15–30 soniya, butun universitet ≈ 3–5
daqiqa (rate limiter 3 so'rov/soniya).

Navbatni kuzatish: `php bin/console messenger:stats`,
`dbal:run-sql "SELECT COUNT(*) FROM messenger_messages"`.

### Muhim: `cache:clear` dan keyin worker'larni qayta ishga tushiring

Uzoq ishlaydigan worker eski kompilyatsiya qilingan konteynerni ushlab turadi.
`cache:clear` (yoki `composer require`) dan keyin:

```
docker compose exec php php bin/console messenger:stop-workers
```

supervisor ularni yangi kesh bilan qayta ko'taradi. Aks holda worker
"Failed to open stream: .../var/cache/dev/Container.../..." bilan sinadi va
xabar 1 soat "delivered" holatida qotib qoladi.
