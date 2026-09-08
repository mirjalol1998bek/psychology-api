# psychology-api — Texnik tavsif

> **Bu hujjat koddan muhimroq.** Har qanday o'zgarish avval shu yerda (yoki
> `/docs` dagi mos faylda) yoziladi, keyin kodga tushiriladi. Kod bilan hujjat
> ziddiyatga tushsa — hujjat to'g'ri hisoblanadi.
>
> **Har safar kod yozishdan oldin shu fayl va tegishli `/docs/*.md` o'qiladi.**

---

## 1. Loyiha haqida

`psychology-api` — universitet psixologik xizmati platformasining backend qismi.
Frontend alohida loyiha: `../psychology-front` (Vue 3 + Vuetify).

Platforma quyidagilarni ta'minlaydi:

- Talabalarga psixologik metodikalar (temperament, psixogeometrik va h.k.)
  biriktirish, ularni topshirish va natijalarni saqlash.
- Psixolog/admin uchun fakultet–guruh–talaba kesimida natijalar va hisobotlar.
- Talaba → psixolog **murojaat** kanali (anonim yoki ismli), javob berish.
- **Ijtimoiy-psixologik pasport** — har bir talabadan yig'iladigan so'rovnoma.
- Psixolog **qabul kalendari** (bo'sh/band slotlar).
- Bildirishnomalar (yangi murojaat / javob).

Domen modeli ishlab turgan `dashboard-uzswlu` loyihasidagi `psixologiya`
modulidan meros oladi, lekin API mustaqil qayta yoziladi.

---

## 2. Texnologik stek

| Qatlam | Texnologiya |
|---|---|
| Til | PHP 8.3+ |
| Framework | Symfony 7.x |
| API | API Platform 4.x |
| ORM | Doctrine ORM 3.x |
| MB | PostgreSQL 16 |
| Auth | LexikJWTAuthenticationBundle (JWT) + HEMIS OAuth2 |
| Migratsiya | `doctrine/doctrine-migrations-bundle` |
| Test | PHPUnit 11 + `zenstruck/foundry` (fixture/factory) |
| Statik tahlil | PHPStan (max level), PHP-CS-Fixer (PSR-12) |
| Konteyner | Docker + Docker Compose |

---

## 3. Docker

Barcha ishlar Docker orqali bajariladi. Xost mashinada PHP/Composer
o'rnatilishi shart emas.

```
docker/
  php/            Dockerfile (php-fpm 8.3, kerakli extension'lar)
  nginx/          nginx konfiguratsiyasi
compose.yaml
```

Xizmatlar:

- `php` — php-fpm, ilova kodi
- `nginx` — 80-portda HTTP kirish
- `database` — PostgreSQL, `pgdata` volume
- `mailer` — dev uchun Mailpit

Buyruqlar `Makefile` orqali qisqartiriladi (`make up`, `make sh`, `make migrate`,
`make test`, `make cs`, `make stan`).

---

## 4. Loyiha tuzilmasi

```
src/
  ApiResource/         API Platform DTO-resurslari (Entity'dan tashqari)
  Command/             Konsol buyruqlari
  Component/           BIZNES LOGIKA — barcha logika shu yerda
    User/
      UserFactory.php
      UserManager.php
      UserProvider.php
      ...
    Assessment/
      TemperamentScorer.php
      PsychogeometricScorer.php
      AttemptFactory.php
      AttemptManager.php
      ...
    Appeal/
    Passport/
    Appointment/
    Notification/
    Organization/       Faculty / Group / Student
  Controller/          Kontrollerlar (barchasi AbstractController meros oladi)
  Entity/              Doctrine entity'lari
  Enum/                Backed enum'lar (Role, InstrumentType, ...)
  EventSubscriber/
  Repository/          Doctrine repozitoriylari
  Security/            Voter, autentifikator, token
  Service/             Infratuzilma xizmatlari (ParameterGetter, ...)
  State/               API Platform State Provider / Processor
docs/                  Har bir entity va muhim jarayon uchun .md hujjat
config/
migrations/
tests/
```

**Qoida:** biznes logikaga oid har qanday klass `src/Component/<Domen>/` ichida
bo'ladi. `Controller`, `State`, `EventSubscriber` faqat `Component` dagi
xizmatlarni chaqiradi, o'zida logika saqlamaydi.

---

## 5. Arxitektura qatlamlari

```
HTTP so'rov
   │
   ▼
Controller / API Platform operatsiyasi
   │   (validatsiya, avtorizatsiya, DTO -> Component chaqiruvi)
   ▼
State Processor / Provider  ──►  Component (Manager, Factory, Scorer, ...)
   │                                   │
   │                                   ▼
   │                              Repository (faqat o'qish so'rovlari)
   ▼                                   │
Entity  ◄──────────────────────────────┘
   │
   ▼
Doctrine (EntityNameManager -> AbstractManager -> EntityManager)
```

- **Controller** — HTTP bilan ishlaydi, javob shaklini belgilaydi, logikani
  `Component` ga topshiradi.
- **Component** — biznes qoidalari. `Factory` (yaratish), `Manager` (saqlash),
  `Scorer`/`Provider`/`Calculator` (hisob-kitob).
- **Repository** — faqat `SELECT` so'rovlari. Ichida yozish yo'q.
- **Entity** — anemik emas: o'ziga tegishli invariantlarni ushlab turadi, lekin
  tashqi bog'liqliklarsiz.

---

## 6. Asosiy abstraksiyalar

### 6.1 `App\Controller\AbstractController`

Barcha kontrollerlar bizning `AbstractController` dan meros oladi (Symfony'ning
o'zinikidan emas). Umumiy yordamchi metodlar shu yerda: joriy foydalanuvchini
olish, JSON javob qaytarish, DTO ni deserializatsiya qilish va h.k.

### 6.2 `App\Component\AbstractManager`

Entity saqlashning yagona yo'li. `persist`, `flush`, `remove`, `refresh` kabi
metodlarni beradi.

Har bir entity uchun **bo'sh** `EntityNameManager` klassi yaratiladi. U faqat
`@method` phpDoc bilan tiplashtiriladi:

```php
namespace App\Component\User;

use App\Component\AbstractManager;
use App\Entity\User;

/**
 * @method void save(User $user, bool $flush = true)
 * @method void remove(User $user, bool $flush = true)
 * @method User|null find(int $id)
 */
final class UserManager extends AbstractManager
{
}
```

`AbstractManager` ichida entity klassi konstruktor yoki abstrakt metod orqali
aniqlanadi.

### 6.3 `App\Component\<Domen>\<Entity>Factory`

Entity **hech qachon** `new Entity()` bilan yaratilmaydi. Har doim Factory:

```php
namespace App\Component\User;

use App\Entity\User;
use App\Enum\Role;

final class UserFactory
{
    public function createFromHemis(HemisProfile $profile, Role $role): User
    {
        $user = new User($profile->getHemisId(), $profile->getFullName(), $role);
        $user->setStudyLanguage($profile->getStudyLanguage());

        return $user;
    }
}
```

Testlarda fixture uchun `zenstruck/foundry` factory'lari alohida
(`tests/Factory/`), lekin **domen logikasi** faqat `src/Component` dagi
factory'lardan foydalanadi.

### 6.4 `App\Service\ParameterGetter`

`.env` yoki `services.yaml` dagi parametrlarni **faqat** shu klass orqali olamiz.
Kodda `$_ENV`, `getenv()`, `%kernel.project_dir%` to'g'ridan-to'g'ri ishlatilmaydi.

```php
final class ParameterGetter
{
    public function __construct(private readonly ParameterBagInterface $bag)
    {
    }

    public function getHemisOauthUrl(): string
    {
        return $this->getString('hemis.oauth_url');
    }
}
```

---

## 7. PHP uchun qat'iy qoidalar

Bu qoidalar **majburiy**. PR CI'da PHP-CS-Fixer + PHPStan tekshiruvidan o'tadi.

1. **PSR-12** standartiga qat'iy amal qilamiz.

2. Har bir fayl **bitta bo'sh qator** bilan tugaydi.

3. Entity obyektlari **har doim Factory** orqali yaratiladi (`UserFactory`).

4. Biznes logikaga oid barcha fayllar **`src/Component`** papkasida
   (`src/Component/User/UserFactory.php`, `src/Component/User/UserManager.php`).

5. Entity saqlash uchun **`EntityNameManager`** ishlatiladi. U `AbstractManager`
   dan meros oladi va faqat `@method` phpDoc yozilgan bo'sh klass bo'ladi.

6. Barcha funksiya va klasslar **`use` orqali import** qilinadi. Kodda
   `\SomeClass()` ko'rinishidagi yozuvlar bo'lmaydi.

7. Controller bizning **`App\Controller\AbstractController`** dan meros oladi.

8. `.env` o'zgaruvchilari **`ParameterGetter`** orqali olinadi.

9. **Metod nomlari — har doim fe'l.**
   - Boolean qaytaradigan metod `is` yoki `has` bilan boshlanadi.
   - `has` — obyekt ichida element mavjudligini tekshiradi: `hasChild()`.
   - **Faqat entity obyektlarida** `getIs...` / `getHas...` bilan boshlanadigan
     nomlarga ruxsat (aks holda API Platform bu qiymatlarni frontendga
     uzatmaydi): `getIsActive()`.

10. **Komment o'rniga private metod.** Kod bo'lagini tushuntirish uchun komment
    yozish o'rniga — shu bo'lakni nomi o'zini tushuntiruvchi `private` metodga
    ajratamiz. Kod kommentlarsiz tushunarli bo'lishi kerak.

11. **phpDoc** — faqat kerakli joyda. Masalan, PHP tili metod qanday turdagi
    kolleksiya/massiv qaytarishini to'liq ifodalay olmasa (`list<User>`,
    `array<string, Result>` va h.k.).

12. **Assotsiativ massivlardan foydalanilmaydi.** Massiv o'rniga obyekt (DTO,
    `readonly class`, `ArrayCollection<Entity>`).

13. **`if` ichida faqat boolean.**
    - `if ($user)` yoki `if (!$company)` — **noto'g'ri**.
    - `null` tekshiruvi: `if ($user === null)`, `if ($company !== null)`.
    - O'zgaruvchi tipi boolean bo'lsa shartsiz: `if ($isActive)`.
    - Metod faqat boolean qaytarsa `=== true`/`=== false` shart emas:
      `if ($company->getIsActive())`.

14. **Har bir entity uchun `/docs/<fayl_nomi>.md`** hujjat fayli yaratiladi.

15. Backend logikasi yoki entity xususiyati o'zgarsa — `/docs` dagi mos `.md`
    fayl **majburiy** yangilanadi (avval hujjat, keyin kod).

16. **Bo'sh qatorlar:**
    - `{`, `for`, `if`, `switch`, `foreach`, metod, class va umuman `{}`
      ishlatadigan blok yoki bir necha qatorli massiv — o'zidan oldin va keyin
      **bitta bo'sh qator** bilan ajratiladi.
    - **Ammo:** `{` dan **keyin** hech qachon bo'sh qator bo'lmaydi; `}` dan
      **oldin** ham bo'sh qator bo'lmaydi.

**Noto'g'ri:**

```php
private function findBestSplitPosition(string $content): int
{

    $maxLength = mb_strlen($content);
    $preferredLength = $maxLength - self::CHUNK_OVERLAP;

    $htmlTagPosition = $this->findLastHtmlTagPosition($content, $preferredLength);
    if ($htmlTagPosition > 0) {
        return $htmlTagPosition;
    }

    return $maxLength;

}
```

**To'g'ri:**

```php
private function findBestSplitPosition(string $content): int
{
    $maxLength = mb_strlen($content);
    $preferredLength = $maxLength - self::CHUNK_OVERLAP;
    $htmlTagPosition = $this->findLastHtmlTagPosition($content, $preferredLength);

    if ($htmlTagPosition > 0) {
        return $htmlTagPosition;
    }

    return $maxLength;
}
```

---

## 8. Yumshoq qoidalar

- Metodlar imkon qadar **10 qatordan oshmasin**. Katta metod → bir nechta kichik
  `private` metod.
- **SOLID** tamoyillariga amal qilinadi.
- Imkon bor joyda mashhur loyihalash patternlari (Factory, Strategy, State,
  Value Object, Specification, ...).
- Kolleksiya tiplari: `Doctrine\Common\Collections\Collection<int, Entity>`.
- `readonly` — DTO va Value Object'lar uchun standart.
- `final` — sinf boshqa sinfga meros berish uchun ataylab ochilmagan bo'lsa,
  `final` qilinadi.
- Enum'lar — `enum Role: string` (backed).

---

## 9. Hujjatlashtirish qoidalari (`/docs`)

- Har bir entity: `docs/<entity>.md` — maydonlar, aloqalar, invariantlar,
  API operatsiyalari, ruxsatlar.
- Har bir muhim jarayon: `docs/<jarayon>.md` — masalan `docs/attempt-scoring.md`,
  `docs/hemis-auth.md`, `docs/notifications.md`.
- `docs/README.md` — barcha hujjatlar indeksi.
- O'zgarish tartibi: **`docs` → migratsiya → kod → test**.

---

## 10. Domen modeli

Enum'lar:

- `Role`: `student`, `psychologist`, `admin`
- `StudyLanguage`: `uz`, `ru`
- `InstrumentType`: `FREQUENCY_BASED` (temperament), `RANKING_BASED`
  (psixogeometrik), `SCORE_RANGE_BASED` (nevrasteniya)
- `QuestionType`: `YES_NO`, `SINGLE_CHOICE`, `MULTI_SELECT`,
  `SINGLE_CHOICE_IMAGE`, `WRITING`
- `AttemptStatus`: `not_started`, `in_progress`, `submitted`, `reviewed`
- `AppointmentStatus`: `free`, `booked`, `cancelled`
- `AppealMode`: `named`, `anonymous`
- `AppealStatus`: `open`, `answered`
- `AppealTopic`: `question`, `appointment`, `stress`, `other`
- `FamilyStatus`: `married`, `single`
- `LivingEnvironment`: `calm`, `problematic`

### 10.1 Entity'lar ro'yxati (har biri uchun `docs/*.md`)

| Entity | Vazifasi | Hujjat |
|---|---|---|
| `User` | Talaba / psixolog / admin. HEMIS profili + rol. | `docs/user.md` |
| `Faculty` | Fakultet (HEMIS'dan). | `docs/faculty.md` |
| `StudyGroup` | Guruh, fakultetga tegishli, ta'lim tili. | `docs/study-group.md` |
| `Category` | Metodika kategoriyasi + `InstrumentType`. | `docs/category.md` |
| `Quiz` | Kategoriya ichidagi test (savollar to'plami). | `docs/quiz.md` |
| `Question` | Savol + `QuestionType` + tartib. | `docs/question.md` |
| `AnswerOption` | Javob varianti (matn/rasm, ball, kategoriya kaliti). | `docs/answer-option.md` |
| `Assignment` | Kategoriyani fakultet/guruhga biriktirish + muddat. | `docs/assignment.md` |
| `Attempt` | Talabaning bitta metodika bo'yicha urinishi. | `docs/attempt.md` |
| `AttemptAnswer` | Urinish ichidagi bitta javob. | `docs/attempt-answer.md` |
| `AssessmentResult` | Hisoblab chiqilgan natija (label, tavsif, breakdown). | `docs/assessment-result.md` |
| `AppointmentSlot` | Psixolog qabul slot'i (bo'sh/band/bekor). | `docs/appointment-slot.md` |
| `Appeal` | Talaba → psixolog murojaati + javob. | `docs/appeal.md` |
| `StudentPassport` | Ijtimoiy-psixologik pasport so'rovnomasi. | `docs/student-passport.md` |
| `Notification` | Foydalanuvchiga bildirishnoma. | `docs/notification.md` |

### 10.2 Aloqalar (qisqacha)

```
Faculty 1───* StudyGroup 1───* User(student)
Category 1───* Quiz 1───* Question 1───* AnswerOption
Category 1───* Assignment *───1 StudyGroup
Assignment 1───* Attempt *───1 User(student)
Attempt 1───* AttemptAnswer *───1 Question
Attempt 1───1 AssessmentResult
User(psychologist) 1───* AppointmentSlot *───0..1 User(student)
User(student) 1───* Appeal *───0..1 User(psychologist)  (javob bergan)
User(student) 1───1 StudentPassport
User 1───* Notification
```

### 10.3 Ballash (scoring)

`src/Component/Assessment/` ichida `InstrumentType` bo'yicha strategiya:

- **`FREQUENCY_BASED` (temperament)** — `AnswerOption.categoryKey` bo'yicha
  "Ha" javoblar sanaladi; eng ko'p ball to'plagan kategoriya natija bo'ladi.
  uz — 80 ta bayonot ("Ha/Yo'q"); ru guruhlar uchun 14 ta tanlovli savol
  (alohida `Quiz`, `studyLanguage=ru`).
- **`RANKING_BASED` (psixogeometrik)** — talaba bitta figurani tanlaydi; o'sha
  figura natija.
- **`SCORE_RANGE_BASED` (nevrasteniya)** — ball yig'indisi → oraliq → xulosa
  matni (`ScoreRange` konfiguratsiyasi).

Natija matnlari (temperament tiplari, geometrik figuralar tavsifi) — bazada
`AssessmentInterpretation` (yoki `Category` bilan bog'liq konfiguratsiya),
`studyLanguage` bo'yicha uz/ru.

Ballash **State Processor** emas, `AttemptManager::submit()` ichida `Scorer`
chaqiriladi — API'siz (masalan, migratsiyadan qayta hisoblash) ham ishlashi
uchun.

---

## 11. Autentifikatsiya va avtorizatsiya

### 11.1 Kirish yo'llari

1. **HEMIS OAuth2** (`student.uzswlu.uz`) — asosiy yo'l. Rol HEMIS profilidan
   aniqlanadi. Callback → `User` topiladi/yaratiladi (`UserFactory`) → JWT
   beriladi. Batafsil: `docs/hemis-auth.md`.
2. **Login/parol** — xodimlar (psixolog/admin) va demo hisoblar uchun.
   `password_hash` bazada saqlanadi.

Chiqishda JWT + refresh token. Frontend `Authorization: Bearer` yuboradi.

### 11.2 Ruxsatlar (Voter'lar)

- `student` — faqat o'ziga biriktirilgan `Assignment`, o'z `Attempt`,
  o'z `AssessmentResult`, o'z `Appeal` va `StudentPassport`.
- `psychologist` — barcha natijalar, murojaatlar, kalendar; talaba/guruh
  yaratmaydi.
- `admin` — hammasi, shu jumladan `Faculty` / `StudyGroup` / `User(student)`
  yaratish (test qilish uchun) va "talaba sifatida ko'rish" (impersonatsiya
  token'i).

Anonim murojaatda `Appeal.mode = anonymous` bo'lsa — API javobida talaba ismi
va guruhi **berilmaydi** (`getIsAnonymous() === true` da serializatsiya
guruhi cheklanadi), lekin `student` bog'lanishi bazada saqlanadi (javobni
yetkazish uchun).

---

## 12. API dizayni

- API Platform resurslari — asosan `#[ApiResource]` Entity'lar; murakkab
  kirish/chiqish shakllari uchun `src/ApiResource/` dagi DTO + State
  Provider/Processor.
- Serializatsiya guruhlari: `<entity>:read`, `<entity>:write`,
  `<entity>:read:staff` (xodimga ko'proq maydon).
- Barcha ro'yxatlar sahifalanadi (`GET` — `page`, `itemsPerPage`).
- Filtr: `SearchFilter`, `OrderFilter`, kerak bo'lsa maxsus filtrlar.
- Frontend kutayotgan asosiy oqimlar:
  - `GET /api/assignments` (talabaga biriktirilganlar)
  - `GET /api/quizzes/{id}` (savollari bilan)
  - `POST /api/attempts` (boshlash) / `PATCH /api/attempts/{id}` (javob/yakunlash)
  - `GET /api/attempts?student=me` , `GET /api/assessment_results?student=me`
  - `GET /api/faculties` , `GET /api/study_groups?faculty=` , `GET /api/students?group=`
  - `GET /api/admin/group_results?group=&instrument=` (jadval + eksport uchun)
  - `GET /api/admin/group_passports?group=` (ijtimoiy-psixologik pasport eksporti)
  - `GET/POST /api/appointment_slots`
  - `GET/POST /api/appeals` , `POST /api/appeals/{id}/reply`
  - `GET/PUT /api/student_passport`
  - `GET /api/notifications` , `POST /api/notifications/mark_read`

Har bir endpoint aniq shakli — tegishli `docs/*.md` da.

---

## 13. Sifat va test

- `make cs` — PHP-CS-Fixer (PSR-12 + loyiha qoidalari), CI'da `--dry-run`.
- `make stan` — PHPStan max level, baseline yo'q (yangi kod toza).
- `make test` — PHPUnit; har bir `Component` xizmati uchun unit test, muhim
  oqimlar uchun API (functional) test.
- Fixture — `zenstruck/foundry`, `tests/Factory/`.
- Har bir yangi entity/endpoint bilan birga test **va** `docs/*.md` keladi.

---

## 14. Frontend bilan integratsiya

`../psychology-front` hozircha `localStorage`'dagi mock ma'lumot bilan ishlaydi
(`src/services/*`, `src/stores/*`). Backend tayyor bo'lgach, o'sha service
qatlami `axios` bilan shu API'ga ulanadi — komponentlar o'zgarmaydi.

`psychology-front/src/types/domain.ts` va shu loyihaning
`src/data/assessments/*` fayllari — domen shakllari bo'yicha manba sifatida
qaraladi (ballash qoidalari, temperament bayonotlari, figura tavsiflari).

---

## 15. Keyingi qadamlar

1. Docker skeleti (`compose.yaml`, `docker/`, `Makefile`).
2. Symfony + API Platform o'rnatish, `AbstractController`, `AbstractManager`,
   `ParameterGetter` skeletlari.
3. `docs/` — birinchi navbatda `user.md`, `faculty.md`, `study-group.md`.
4. Migratsiya + entity'lar (10.1 tartibida).
5. HEMIS auth (`docs/hemis-auth.md` → kod).
6. Assessment oqimi (assignment → attempt → scoring → result).
7. Appeal, notification, passport, appointment.
