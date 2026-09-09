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
- `data/student-list?_group=<groupExtId>` — faqat **hozir o'qiyotgan**
  (`studentStatus.code === "11"`) talabalar. `student_id_number` → `User.hemisId`.
- Ta'lim tili: `educationLang.code` — `"12"` → `ru`, boshqasi → `uz`.

## Moslashtirish (upsert)

| Bizniki | HEMIS kaliti | Yangilanadi |
|---|---|---|
| `Faculty.externalId` | department `id` | `name` |
| `StudyGroup.externalId` (UNIQUE) | group `id` | `name`, `studyLanguage`, `faculty` |
| `User.hemisId` | `student_id_number` | `fullName`, `image`, `studyGroup` |

Yangi talaba: `StudentFactory` (email `{student_id_number}@students.uzswlu.uz`,
tasodifiy parol, `ROLE_STUDENT`, `status=active`). **Mavjud foydalanuvchining
roli/holati o'zgartirilmaydi.**

## Endpointlar (`ROLE_ADMIN`)

| Yo'l | Vazifasi |
|---|---|
| `POST /api/admin/hemis/faculties` | barcha fakultetlarni sinxronlash → `{created, updated}` |
| `GET /api/admin/hemis/faculties/{id}/groups` | fakultetning HEMIS guruhlari (ro'yxat, **saqlanmaydi**) |
| `POST /api/admin/hemis/faculties/{id}/groups/{groupExternalId}` | bitta guruhni import + talabalarini sinxronlash → `{created, updated}` |
| `POST /api/admin/hemis/groups/{id}/students` | guruh talabalarini qayta sinxronlash |

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

1. `NightlyHemisSyncMessage` (async transport, `doctrine://` — `messenger_messages`
   jadvali).
2. `NightlyHemisSyncHandler`:
   - `LockFactory` (`hemis-nightly-sync`, 7200s) — ikki marta parallel ishlamaydi.
   - fakultetlarni sinxronlaydi.
   - HEMIS'ga bog'langan har guruh uchun (`StudyGroupRepository::findLinkedToHemis()`)
     bitta `SyncGroupStudentsMessage` dispatch qiladi.
3. `SyncGroupStudentsHandler` — bitta guruh talabalarini sinxronlaydi.
4. Har HEMIS HTTP chaqiruvi **rate limiter** (`framework.rate_limiter.hemis_api`,
   `token_bucket`, 3 so'rov/soniya, burst 10) bilan cheklanadi — `HemisApiClient`
   `$hemisApiLimiter->reserve(1)->wait()`. 403'da eksponensial backoff.

### Kechki jadval

`src/Schedule/HemisSchedule.php` — `#[AsSchedule('hemis')]`,
`RecurringMessage::cron('30 0 * * *', ...)` **Asia/Tashkent** (har kuni 00:30).
Hech kimga xalaqit bermaydi.

### Worker (supervisor)

`docker/php/supervisor/messenger-worker.conf` — `php` konteynerida 2 ta jarayon:

```
messenger:consume async scheduler_hemis --time-limit=3600 --memory-limit=192M
```

`run-daemons.sh` konteyner ishga tushganda `supervisorctl start` qiladi.

### Qo'lda ishga tushirish (darhol emas — navbatga)

| Yo'l | Vazifasi |
|---|---|
| `POST /api/admin/hemis/students` (`ROLE_ADMIN`) | import qilingan **barcha** guruh talabalarini qayta sinxronlash (`NightlyHemisSyncMessage`) |
| `POST /api/admin/hemis/faculties/{id}/groups` (`ROLE_ADMIN`) | fakultetning **barcha** HEMIS guruhlarini + talabalarini import (`SyncFacultyGroupsMessage`) |
| `php bin/console ask:hemis:sync --queue` | nightly xabarni navbatga qo'yadi |
| UI: Tashkilot → "Barcha talabalarni yangilash" | `queueHemisStudentsSync()` |
| UI: Tashkilot → HEMIS guruhlari dialogi → "Barcha guruhlarni yuklash" | `queueHemisFacultyGroups(facultyId)` |

**Diqqat:** HEMIS `group-list` eski (bitirgan) kurslarni ham `active:true` bilan
qaytaradi — bitta fakultetda **300+ guruh** bo'lishi mumkin. "Barcha guruhlarni
yuklash" hammasini import qiladi (ishonchli "joriy o'quv yili" filtri yo'q).

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
