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
  bitirgan eski guruhlarni ham `active:true` bilan qaytaradi (bir fakultetda
  300–600 ta). Shu sababli guruhlar **to'plab import qilinmaydi**.
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
php bin/console ask:hemis:sync            # fakultetlar + har fakultet barcha guruhlari
php bin/console ask:hemis:sync --students # + har guruh talabalari (sekin)
```

CLI barcha faol guruhlarni oladi (ko'p bo'lishi mumkin) — UI esa admin tanlaganini.
