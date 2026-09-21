# psychology-api — Texnik tavsif

> **Bu hujjat koddan muhimroq.** Har qanday o'zgarish avval shu yerda (yoki
> `/docs` dagi mos faylda) yoziladi, keyin kodga tushiriladi. Kod bilan hujjat
> ziddiyatga tushsa — **hujjat to'g'ri** hisoblanadi.
>
> **Har safar kod yozishdan oldin shu fayl va tegishli `/docs/*.md` o'qiladi.**

---

## 1. Loyiha haqida

`psychology-api` — universitet psixologik xizmati platformasining backend qismi.
Frontend alohida loyiha: `../psychology-front` (Vue 3 + Vuetify), hozircha mock
ma'lumot bilan ishlaydi.

Platforma imkoniyatlari:

- Talabalarga psixologik metodikalar (temperament, psixogeometrik, nevrasteniya)
  biriktirish, ularni topshirish va natijalarni saqlash.
- Psixolog/admin uchun fakultet–guruh–talaba kesimida natijalar va hisobotlar
  (Excel/CSV eksport).
- Talaba → psixolog **murojaat** kanali (anonim yoki ismli), javob berish.
- **Ijtimoiy-psixologik pasport** — har bir talabadan yig'iladigan so'rovnoma.
- Psixolog **qabul kalendari** (bo'sh/band/bekor slotlar).
- Bildirishnomalar (yangi murojaat / javob).

Loyiha **`kadirov/api-starter-kit`** skeleti asosida qurilgan. Skeletdan tayyor
kelgan narsalar §4 da.

---

## 2. Texnologik stek

| Qatlam | Texnologiya |
|---|---|
| Til | PHP **8.5** |
| Framework | Symfony **8.0** |
| API | API Platform **4.3** |
| ORM | Doctrine ORM **3.6** |
| MB | **MariaDB 11.7** (MySQL protokoli) |
| Auth | `lexik/jwt-authentication-bundle` (JWT + refresh token) + HEMIS OAuth2 |
| CORS | `nelmio/cors-bundle` |
| Migratsiya | `doctrine/doctrine-migrations-bundle` |
| Kod generatsiya | `symfony/maker-bundle` (dev) |
| Konteyner | Docker + Docker Compose (`php`, `nginx`, `db`) |

Keyin qo'shiladi (skeletda yo'q):

| Statik tahlil | PHPStan (max level) |
| Kod uslubi | PHP-CS-Fixer (PSR-12 + loyiha qoidalari) |
| Test | PHPUnit 11 + `zenstruck/foundry` |

---

## 3. Docker va ishga tushirish

Barcha buyruqlar Docker orqali. Xost mashinada PHP kerak emas.

```
docker/
  php/        Dockerfile (php-fpm 8.5) + php.ini
  nginx/      default.conf
  mysql/db/   MariaDB volume (gitignore)
docker-compose.yml
```

`.env` dagi asosiy o'zgaruvchilar:

| O'zgaruvchi | Qiymat | Izoh |
|---|---|---|
| `DOCKER_PROJECT_NAME` | `psychology_api` | konteyner prefiksi, DB nomi |
| `DOCKER_NGINX_PORT` | `8508` | `http://localhost:8508/api` |
| `DOCKER_DATABASE_PORT` | `3508` | tashqi MariaDB porti |
| `DATABASE_URL` | `mysql://root:...@db:3306/psychology_api` | |

Ishga tushirish:

```bash
docker compose up -d
docker compose exec php composer install
docker compose exec php bin/console ask:install          # bootstrap
docker compose exec php bin/console ask:generate-jwt-keys # JWT kalitlari
docker compose exec php bin/console doctrine:migrations:migrate
```

Kirish: `http://localhost:8508/api` (API Platform UI).

---

## 4. Skeletdan tayyor kelgan narsalar

Bularni **qayta yozmaymiz**, kengaytiramiz.

### `src/Component/Core/`

| Klass | Vazifasi |
|---|---|
| `AbstractManager` | Entity saqlash. `save(object $entity, bool $needToFlush = false)`. Yangi/eski entity'ga qarab `createdAt/By`, `updatedAt/By` maydonlarini avtomatik to'ldiradi (`*SettableInterface` bo'yicha). Yagona persist yo'li. |
| `ParameterGetter` | `.env`/parametr o'qish. `get`, `getString`, `getInt`, `getBool`, `getArray`, `getFloat`. |
| `HashValidator`, `SlugGenerator`, `Requester`, `MarkEntityAsDeleted` | Yordamchi xizmatlar. |
| `Exceptions\ModelNotFoundException` | |

### `src/Component/User/`

| Klass | Vazifasi |
|---|---|
| `CurrentUser` | Joriy foydalanuvchi. `getUser(): User`, `isAuthed(): bool`, `getJwtUser(): JwtUserDto`. |
| `UserFactory` | `User` yaratish (skeletda `create(email, password)` — biz `createFromHemis(...)` qo'shamiz). |
| `UserManager extends AbstractManager` | `hashPassword(User, string)`. |
| `TokensCreator` | JWT access + refresh token juftligi. |
| `Dtos\` | `JwtUserDto`, `TokensDto`, `RefreshTokenDto`, `RefreshTokenRequestDto`. |
| `Exceptions\AuthException` | |

### `src/Controller/Base/`

| Klass | Vazifasi |
|---|---|
| `AbstractController` | **Barcha kontrollerlar shundan meros oladi.** `response()`, `responseNormalized()`, `responseEmpty()`, `getDtoFromRequest()`, `validate()`, `getUser()`, `getJwtUser()`, `findEntityOrError()`, `throwNotFoundException()`. |
| `Constants\ResponseFormat` | `JSONLD`, `JSON` konstantalari. |

### `src/Controller/` (harakat-kontrollerlar)

`UserAuthAction` (`POST users/auth`), `UserAuthByRefreshTokenAction`,
`UserAboutMeAction` (`POST users/about_me`), `UserCreateAction`,
`UserChangePasswordAction`, `UserIsUniqueEmailAction`, `DeleteAction`
(yumshoq o'chirish — `deletedAt` qo'yadi).

### `src/Controller/Subscribers/`

`ReadExtension` (o'qish so'rovlariga umumiy filtr, masalan `deletedAt IS NULL`),
`WriteSubscriber` (yozishda `Manager` chaqirish, audit).

### `src/Entity/`

- `User` — `id`, `email`, `password`, `roles[]`, audit + soft-delete. To'liq
  API Platform CRUD + auth operatsiyalari. **Biz kengaytiramiz** (§10.3).
- `Interfaces/` — `CreatedAtSettableInterface`, `CreatedBySettableInterface`,
  `UpdatedAtSettableInterface`, `UpdatedBySettableInterface`,
  `DeletedAtSettableInterface`, `DeletedBySettableInterface`.
- `Traits/` — mos accessor traitlar
  (`CreatedAtAccessorsTrait`, `UpdatedAtAndByAccessorsTrait`,
  `DeletedAtAndByAccessorsTrait`, `CreatedUpdatedDeletedAtAndByTrait`, ...).

**Yangi entity qoidasi:** audit kerak bo'lsa mos `*SettableInterface` +
`*AccessorsTrait` ulanadi. Yumshoq o'chirish kerak bo'lsa
`DeletedAtSettableInterface` + `DeletedAtAndByAccessorsTrait` + `Delete`
operatsiyasida `controller: DeleteAction::class`.

### `src/Command/` — `ask:*`

`ask:install`, `ask:generate-jwt-keys`, `ask:deploy`,
`ask:roles:add-to-user`, `ask:roles:delete-from-user`, `ask:roles:show-user-roles`.
Yangi buyruqlar shu `ask:` prefiksi + `Ask` klass prefiksi bilan.

---

## 5. Loyiha tuzilmasi (biz to'ldiradigan)

```
src/
  ApiResource/         API Platform DTO-resurslari (Entity'dan tashqari input/output)
  Command/             Ask*Command
  Component/            BIZNES LOGIKA — barcha logika shu yerda
    Core/              (skeletdan)
    User/              (skeletdan) + HEMIS auth, provider
    Organization/       Faculty / StudyGroup / Student
    Assessment/         Quiz, Question, Attempt, Scorer (Strategy)
    Assignment/
    Appeal/
    Passport/
    Appointment/
    Notification/
  Controller/
    Base/              (skeletdan)
    Subscribers/       (skeletdan)
    <Xatti-harakat>Action.php
  Entity/
  Enum/                backed enum'lar
  Repository/          faqat SELECT
  Security/            Voter, HEMIS autentifikator
  State/               API Platform State Provider / Processor
docs/                  har bir entity va muhim jarayon uchun .md
migrations/
tests/                 (keyin)
```

**Qoida:** biznes logika faqat `src/Component/<Domen>/` da. `Controller`,
`State`, `EventSubscriber`, `Voter` faqat `Component` xizmatlarini chaqiradi.

---

## 6. Arxitektura qatlamlari

```
HTTP so'rov
   │
   ▼
API Platform operatsiyasi  yoki  <Action>Controller (AbstractController)
   │   avtorizatsiya (Voter), validatsiya, DTO
   ▼
State Processor/Provider  ──►  Component (Factory, Manager, Scorer, Provider)
   │                                 │
   │                                 ▼
   │                            Repository (faqat o'qish)
   ▼                                 │
Entity  ◄────────────────────────────┘
   │
   ▼
AbstractManager::save()  ──►  Doctrine EntityManager
```

- **Controller / operatsiya** — HTTP bilan ishlaydi, javob shaklini belgilaydi.
- **Component** — biznes qoidalari. `*Factory` (yaratish), `*Manager` (saqlash),
  `*Scorer` / `*Provider` / `*Calculator` (hisob-kitob).
- **Repository** — faqat `SELECT`. Yozish yo'q.
- **Entity** — o'z invariantlarini ushlaydi, tashqi bog'liqliksiz.

---

## 7. PHP uchun qat'iy qoidalar

**Majburiy.** CI'da PHP-CS-Fixer + PHPStan tekshiruvidan o'tadi.

1. **PSR-12** ga qat'iy amal qilamiz.

2. Har bir fayl **bitta bo'sh qator** bilan tugaydi.

3. Entity obyektlari **har doim `*Factory`** orqali yaratiladi
   (`new User()` faqat Factory ichida).

4. Biznes logikaga oid barcha fayllar **`src/Component/<Domen>/`** da
   (`src/Component/User/UserFactory.php`, `.../UserManager.php`).

5. Entity saqlash — **`EntityNameManager extends AbstractManager`**. Ko'p
   hollarda faqat `@method` phpDoc yozilgan bo'sh klass:

   ```php
   namespace App\Component\Assessment;

   use App\Component\Core\AbstractManager;
   use App\Entity\Attempt;

   /**
    * @method void save(Attempt $entity, bool $needToFlush = false)
    */
   class AttemptManager extends AbstractManager
   {
   }
   ```

6. Barcha funksiya/klass **`use` orqali import** qilinadi. Kodda `\SomeClass()`
   yozuvlari bo'lmaydi.

7. Controller bizning **`App\Controller\Base\AbstractController`** dan meros
   oladi (Symfony'nikidan emas).

8. `.env` o'zgaruvchilari **`App\Component\Core\ParameterGetter`** orqali
   olinadi. `$_ENV`, `getenv()`, `%env()%` kontroller/servisda ishlatilmaydi.

9. **Metod nomlari — har doim fe'l.**
   - Boolean qaytaradigan metod `is` yoki `has` bilan boshlanadi.
   - `has` — obyekt ichida element mavjudligini tekshiradi: `hasChild()`.
   - **Faqat entity obyektlarida** `getIs...` / `getHas...` ga ruxsat, aks holda
     API Platform bu qiymatni frontendga uzatmaydi: `getIsActive()`.

10. **Komment o'rniga private metod.** Kod bo'lagini nomi o'zini tushuntiruvchi
    `private` metodga ajratamiz; kod kommentlarsiz tushunarli bo'lishi kerak.

11. **phpDoc** — faqat zarur joyda (masalan `list<Attempt>`,
    `Collection<int, Question>` kabi PHP tili to'liq ifodalay olmaydigan tiplar).

12. **Assotsiativ massivlar ishlatilmaydi.** Massiv o'rniga obyekt: `readonly`
    DTO, Value Object yoki `Collection<Entity>`.

13. **`if` ichida faqat boolean.**
    - `if ($user)` / `if (!$company)` — **taqiqlanadi**.
    - `null` tekshiruvi: `if ($user === null)`, `if ($group !== null)`.
    - O'zgaruvchi tipi boolean bo'lsa shartsiz: `if ($isActive)`.
    - Metod faqat boolean qaytarsa `=== true`/`=== false` shart emas:
      `if ($category->getIsActive())`.

14. **Har bir entity uchun `/docs/<fayl_nomi>.md`** hujjati bo'ladi.

15. Backend logikasi yoki entity xususiyati o'zgarsa — `/docs` dagi mos `.md`
    fayl **majburiy** yangilanadi (**avval hujjat, keyin kod**).

16. **Bo'sh qatorlar.** `{}` ishlatadigan har qanday blok (`if`, `for`,
    `foreach`, `switch`, metod, class) yoki bir necha qatorli massiv — o'zidan
    oldin va keyin **bitta bo'sh qator**. **Ammo:** `{` dan **keyin** hech
    qachon bo'sh qator yo'q; `}` dan **oldin** ham yo'q.

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

- Metodlar imkon qadar **10 qatordan oshmasin** → kichik `private` metodlar.
- **SOLID** tamoyillari.
- Mashhur patternlar (Factory, Strategy, State, Value Object, Specification).
- Kolleksiya tiplari: `Doctrine\Common\Collections\Collection<int, Entity>`.
- `readonly` — DTO/Value Object uchun standart.
- `final` — meros berish ataylab ochilmagan bo'lsa.
- Enum'lar — backed: `enum InstrumentType: string`.

---

## 9. Hujjatlashtirish (`/docs`)

- Har bir entity: `docs/<entity>.md` — maydonlar, aloqalar, invariantlar, API
  operatsiyalari, ruxsatlar.
- Har bir muhim jarayon: `docs/<jarayon>.md` (masalan `docs/attempt-scoring.md`,
  `docs/hemis-auth.md`, `docs/notifications.md`).
- `docs/README.md` — indeks (checklist).
- O'zgarish tartibi: **`docs` → migratsiya → kod → test**.

---

## 10. Domen modeli

### 10.1 Enum'lar (`src/Enum/`)

- `RoleEnum: string` — `ROLE_STUDENT`, `ROLE_PSYCHOLOGIST`, `ROLE_ADMIN`
  (Symfony `roles[]` bilan mos).
- `StudyLanguage: string` — `uz`, `ru`
- **`InstrumentType: string`** — **ballash algoritmi** (metodika turi emas).
  Oddiy (subshkalasiz) yangi metodika (Ibodullayev shkalasi kabi) qo'shish —
  bu **`Category` qatori + savol/ball ma'lumoti**, yangi kod emas. Qiymatlari:
  - `TEMPERAMENT_STATEMENTS` — har bayonotga "Ha/Yo'q"; `AnswerOption.categoryKey`
    bo'yicha "Ha"lar sanaladi, eng ko'p ballli kategoriya natija (uz temperament).
  - `TEMPERAMENT_CHOICE` — har savolga bitta javob; `option.categoryKey` bo'yicha
    eng ko'p tanlangan kategoriya natija (ru temperament).
  - `FIGURE_CHOICE` — bitta figura tanlanadi; o'sha figura natija (psixogeometrik).
  - `SCORE_SCALE` — variant ballari yig'iladi → `ScoreRange` oralig'i → xulosa
    (masalan Ibodullayev shkalasi). Teskari (reverse) savollar
    `Question.getIsReversed()` orqali.
  - `SCORE_SCALE_SUBSCALE` / `SCORE_SCALE_MOTIVATION` / `SCORE_SCALE_EMOTIONAL` /
    `SCORE_SCALE_COMMUNICATION` / `SCORE_SCALE_RISK` / `SCORE_SCALE_VALUES` —
    xuddi `SCORE_SCALE` bilan bir xil ballash (`ScoreScaleScorer`), lekin
    savollar `Question.subscaleKey` bo'yicha nomlangan subshkalalarga
    guruhlanadi (IPM-20, OKM-20, EHS-20, XO-20, QY-16). `SCORE_SCALE_
    COMMUNICATION` (KSM-20) va `SCORE_SCALE_VALUES` (QY-16) — umumiy ball
    yo'q, faqat subshkalalar. Batafsil:
    [`docs/assessment-scoring.md`](docs/assessment-scoring.md).
- `QuestionType: string` — `YES_NO`, `SINGLE_CHOICE`, `MULTI_SELECT`,
  `SINGLE_CHOICE_IMAGE`, `FIGURE`, `WRITING`, `SCALE`
- `AttemptStatus: string` — `not_started`, `in_progress`, `submitted`, `reviewed`
- `AppointmentStatus: string` — `free`, `booked`, `cancelled`
- `AppealMode: string` — `named`, `anonymous`
- `AppealStatus: string` — `open`, `answered`
- `AppealTopic: string` — `question`, `appointment`, `stress`, `other`
- `FamilyStatus: string` — `married`, `single`
- `LivingEnvironment: string` — `calm`, `problematic`
- `NotificationType: string` — `appeal_new`, `appeal_reply`, `assignment_new`,
  `appointment_reminder`

### 10.2 Entity'lar (har biri uchun `docs/*.md`)

| Entity | Vazifasi | Hujjat |
|---|---|---|
| `User` | Talaba / psixolog / admin. HEMIS profili + rollar. | `docs/user.md` |
| `Faculty` | Fakultet (HEMIS'dan). | `docs/faculty.md` |
| `StudyGroup` | Guruh, fakultetga tegishli, ta'lim tili. | `docs/study-group.md` |
| `Category` | Metodika (Temperament, Psixogeometrik, IPM-20, ...) + `InstrumentType` (ballash algoritmi). | `docs/category.md` |
| `Quiz` | Kategoriya ichidagi test. `studyLanguage` (uz/ru variantlar). | `docs/quiz.md` |
| `Question` | Savol + `QuestionType` + tartib + `getIsReversed()`. | `docs/question.md` |
| `AnswerOption` | Javob varianti (matn/rasm, ball, `categoryKey`). | `docs/answer-option.md` |
| `ScoreRange` | `SCORE_SCALE` oilasi uchun ball oralig'i → natija kaliti. | `docs/score-range.md` |
| `AssessmentInterpretation` | Natija matni (temperament tipi / figura / ball oralig'i tavsifi), uz/ru. | `docs/assessment-interpretation.md` |
| `Assignment` | Kategoriyani fakultet/guruhga biriktirish + muddat. | `docs/assignment.md` |
| `Attempt` | Talabaning bitta metodika bo'yicha urinishi. | `docs/attempt.md` |
| `AttemptAnswer` | Urinish ichidagi bitta javob. | `docs/attempt-answer.md` |
| `AssessmentResult` | Hisoblangan natija (`label`, `description`, `breakdown`). | `docs/assessment-result.md` |
| `AppointmentSlot` | Psixolog qabul slot'i. | `docs/appointment-slot.md` |
| `Appeal` | Talaba → psixolog murojaati + javob. | `docs/appeal.md` |
| `StudentPassport` | Ijtimoiy-psixologik pasport so'rovnomasi. | `docs/student-passport.md` |
| `Notification` | Foydalanuvchiga bildirishnoma. | `docs/notification.md` |

### 10.3 `User` kengaytmasi

Skeletdagi `User` ga qo'shiladi:

- `hemisId: ?string` (unique) — HEMIS identifikatori.
- `fullName: string`
- `studyLanguage: StudyLanguage` (talaba uchun)
- `image: ?string`
- `getIsActive(): bool`
- `studyGroup: ?StudyGroup` (talaba)
- `getUserIdentifier()` — `hemisId ?? email ?? id` tartibida.
- `roles[]` da `RoleEnum` qiymatlari.
- Yordamchi: `getPrimaryRole(): RoleEnum`.

### 10.4 Aloqalar

```
Faculty 1───* StudyGroup 1───* User(student)
Category 1───* Quiz 1───* Question 1───* AnswerOption
Category 1───* ScoreRange
Category 1───* AssessmentInterpretation
Category 1───* Assignment *───1 StudyGroup
Assignment 1───* Attempt *───1 User(student)
Attempt 1───* AttemptAnswer *───1 Question
Attempt 1───1 AssessmentResult
User(psychologist) 1───* AppointmentSlot *───0..1 User(student)
User(student) 1───* Appeal *───0..1 User(psychologist, javob bergan)
User(student) 1───1 StudentPassport
User 1───* Notification
```

### 10.5 Ballash — `src/Component/Assessment/Scoring/`

`InstrumentType` bo'yicha **Strategy**: `ScorerInterface`
(`supports(InstrumentType): bool`, `score(Attempt): ScoredResult`) +
`ScorerResolver` (barcha scorer'larni `#[AutowireIterator]` orqali oladi,
mosini qaytaradi).

| Scorer | `InstrumentType` | Algoritm |
|---|---|---|
| `TemperamentStatementScorer` | `TEMPERAMENT_STATEMENTS` | "Ha" javoblarni `option.categoryKey` bo'yicha sanaydi; argmax kategoriya. `breakdown` = kategoriya → ball. |
| `TemperamentChoiceScorer` | `TEMPERAMENT_CHOICE` | Tanlangan `option.categoryKey` bo'yicha sanaydi; argmax kategoriya. |
| `FigureChoiceScorer` | `FIGURE_CHOICE` | Tanlangan figura `option.categoryKey` = natija. `breakdown` = bo'sh. |
| `ScoreScaleScorer` | `SCORE_SCALE` | Barcha javob variantlari ballini yig'adi (reverse savolda `max - score`); `ScoreRange` orasidan mosini topadi → natija kaliti. `breakdown` = `[{label: 'Ball', value: total}]`. |

`ScoredResult` (`readonly`): `resultKey`, `score?`, `breakdown` (VO massivi).
`AttemptManager::submit()` → `ScorerResolver` → `ScoredResult` →
`AssessmentInterpretationRepository` dan `resultKey` + `attempt.studyLanguage`
bo'yicha matn → `AssessmentResult` saqlanadi.

**Yangi metodika qo'shish** (masalan Ibodullayev shkalasi): `Category(instrumentType: SCORE_SCALE)`
+ `Quiz` + `Question`lar + `AnswerOption`lar (ball bilan) + `ScoreRange`lar +
`AssessmentInterpretation`lar. **Kod yozilmaydi** — faqat fixture/seed yoki
admin API orqali ma'lumot.

Manba ma'lumot: `../psychology-front/src/data/assessments/*`,
`../psychology-front/src/utils/scoring.ts`.

Ballash **`AttemptManager::submit()`** ichida `ScorerResolver` orqali chaqiriladi
(State Processor emas — migratsiyadan qayta hisoblash ham ishlashi uchun).

Manba ma'lumot: `../psychology-front/src/data/assessments/*` va
`src/utils/scoring.ts`.

---

## 11. Autentifikatsiya va avtorizatsiya

### 11.1 Kirish yo'llari

1. **HEMIS OAuth2** (`HEMIS_OAUTH_URL`, default `student.uzswlu.uz`) — asosiy
   yo'l. `GET /api/auth/hemis` → HEMIS'ga yo'naltirish;
   `GET /api/auth/hemis/callback` → profil olinadi, `User` topiladi/yaratiladi
   (`UserFactory::createFromHemis`), `TokensCreator` orqali JWT beriladi. Rol
   HEMIS profilidan. Batafsil: `docs/hemis-auth.md`.
2. **Login/parol** — xodimlar (psixolog/admin) va demo. Skeletdagi
   `POST users/auth` + `users/auth/refreshToken`.

Frontend `Authorization: Bearer <access>` yuboradi; muddati tugaganda
`refreshToken` bilan yangilaydi (`TOKEN_ACCESS_EXPIRATION_PERIOD=P1D`,
`TOKEN_REFRESH_EXPIRATION_PERIOD=P2M`).

### 11.2 Ruxsatlar (Voter'lar, `src/Security/`)

- **student** — faqat o'ziga biriktirilgan `Assignment`, o'z `Attempt` /
  `AssessmentResult` / `Appeal` / `StudentPassport`.
- **psychologist** — barcha natijalar, murojaatlar, kalendar; talaba/guruh
  yaratmaydi.
- **admin** — hammasi; `Faculty` / `StudyGroup` / `User(student)` yaratish (test
  uchun) va **"talaba sifatida ko'rish"** (impersonatsiya token'i, cheklangan
  muddatli).

Anonim murojaat (`Appeal.getIsAnonymous() === true`) — API javobida talaba ismi
va guruhi berilmaydi (serializatsiya guruhi cheklanadi), lekin `student`
bog'lanishi bazada saqlanadi (javobni yetkazish uchun).

---

## 12. API dizayni

- Asosan `#[ApiResource]` Entity'lar; murakkab shakllar uchun `src/ApiResource/`
  DTO + State Provider/Processor.
- Serializatsiya guruhlari: `<entity>:read`, `<entity>:write`,
  `<entity>:read:staff` (xodimga ko'proq maydon).
- Barcha ro'yxatlar sahifalanadi.
- Filtrlar: `SearchFilter`, `OrderFilter` + kerak bo'lsa maxsus.
- Yumshoq o'chirish: `Delete` operatsiyasi `DeleteAction` bilan; `ReadExtension`
  `deletedAt IS NULL` filtrini qo'shadi.

Frontend kutayotgan asosiy oqimlar (aniq shakli — mos `docs/*.md`):

- `GET /api/assignments` (talabaga biriktirilganlar)
- `GET /api/quizzes/{id}` (savollari bilan)
- `POST /api/attempts` (boshlash) · `PATCH /api/attempts/{id}` (javob / yakunlash)
- `GET /api/attempts?student=me` · `GET /api/assessment_results?student=me`
- `GET /api/faculties` · `GET /api/study_groups?faculty=` · `GET /api/students?group=`
- `GET /api/admin/group_results?group=&instrument=` (jadval + eksport)
- `GET /api/admin/group_passports?group=` (ijtimoiy-psixologik pasport eksporti)
- `GET/POST /api/appointment_slots`
- `GET/POST /api/appeals` · `POST /api/appeals/{id}/reply`
- `GET/PUT /api/student_passport`
- `GET /api/notifications` · `POST /api/notifications/mark_read`

---

## 13. Sifat va test (keyin qo'shiladi)

- `make cs` / `composer cs` — PHP-CS-Fixer, CI'da `--dry-run`.
- `make stan` — PHPStan max level, baseline yo'q.
- `make test` — PHPUnit; har bir `Component` xizmati uchun unit test, muhim
  oqimlar uchun API (functional) test. Fixture — `zenstruck/foundry`.
- Har yangi entity/endpoint bilan test **va** `docs/*.md` keladi.

---

## 14. Frontend bilan integratsiya

`../psychology-front/src/services/*` hozircha `localStorage` mock. Backend tayyor
bo'lgach o'sha service qatlami `axios` bilan shu API'ga ulanadi — Vue
komponentlari o'zgarmaydi.

Domen shakllari bo'yicha manba: `psychology-front/src/types/domain.ts`,
`psychology-front/src/data/assessments/*`, `psychology-front/src/utils/scoring.ts`.

---

## 15. Holat va keyingi qadamlar

**Bajarilgan (2026-09-08):**

- [x] Docker stack, `ask:install`, JWT kalitlari
- [x] `src/Enum/` — barcha enum'lar
- [x] `User` kengaytmasi + `Faculty` + `StudyGroup` + migratsiya
- [x] Assessment domeni: `Category` → `Quiz` → `Question` → `AnswerOption` →
  `Assignment` → `Attempt` → ballash (Strategy) → `AssessmentResult`
- [x] `Appeal` (anonim/ismli) + javob, `Notification`, `StudentPassport`,
  `AppointmentSlot`
- [x] `role_hierarchy`, State Provider/Processor'lar (talaba izolyatsiyasi)
- [x] `ask:seed:assessments` — temperament uz/ru, psixogeometrik, IPM-20, OKM-20, EHS-20, KSM-20, XO-20, QY-16
- [x] `docs/` — barcha entity + jarayon hujjatlari

- [x] HEMIS OAuth2 (`docs/hemis-auth.md` — authorization code oqimi,
  `src/Component/User/Hemis/`, `GET /api/auth/hemis` + `/callback/hemis`)
- [x] Guruh eksporti: `GET /api/admin/group_results?studyGroup=&category=`,
  `GET /api/student_passports?student.studyGroup=` ([`docs/reports.md`](docs/reports.md))
- [x] "Talaba sifatida ko'rish": `POST /api/students/{id}/impersonate` (admin)
- [x] Frontend: 3000-port + `/api` proxy + haqiqiy HEMIS kirish

**Qolgan:**

- [ ] PHPStan + PHP-CS-Fixer + PHPUnit + CI, `Makefile` to'ldirish
- [ ] Ibodullayev shkalasi (rasmiy savol matni bilan, seed yoki admin API)
- [ ] `psychology-front` `src/services/*` (testlar/natijalar/murojaat/pasport)
  qatlamini API'ga ulash — hozircha localStorage mock, faqat kirish real
