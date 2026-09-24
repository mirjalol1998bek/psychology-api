# StudentPassport

Ijtimoiy-psixologik anketa — har talabada bitta, o'zi to'ldiradi (bir marta
yozib, keyin tahrirlashi mumkin — `PUT` upsert).

FISH, fakultet va kurs/guruh **bu yerda saqlanmaydi** — talaba frontendda
HEMIS profilidan (`auth.user.hemis.fullName`/`.faculty`/`.group`) o'qib,
faqat ko'rsatiladi (readonly). To'liq yosh ham saqlanmaydi — `birthDate`dan
frontendda hisoblanadi.

| Maydon | Tip | Izoh |
|---|---|---|
| `student` | OneToOne `User`, not null | |
| `personalCode` | ?string(32) | **Psixolog beradi** — talaba formasida yo'q, `passport:write` guruhida emas |
| `birthDate` | ?date | |
| `gender` | ?`Gender` | `male` / `female` |
| `permanentAddress` | ?string(512) | doimiy yashash manzili (viloyat, tuman) |
| `livingArrangement` | ?`LivingArrangement` | `with_family` / `dormitory` / `rented` / `with_relatives` — hozir qayerda yashaydi |
| `commuteMinutes` | ?int | universitetgacha yo'lda ketadigan vaqt (daqiqa) |
| `familyStatus` | ?`FamilyStatus` | `married` / `single` |
| `familyType` | ?`FamilyType` | `full` / `incomplete` / `under_guardianship` / `lost_breadwinner` |
| `siblingsCount` | ?int | oiladagi farzandlar soni |
| `birthOrder` | ?int | o'zi nechanchi farzand |
| `fatherInfo` | ?string(255) | otasining ma'lumoti va kasbi |
| `motherInfo` | ?string(255) | onasining ma'lumoti va kasbi |
| `financialStatus` | ?`FinancialStatus` | `good` / `average` / `difficult` |
| `educationForm` | ?`EducationForm` | `budget` / `contract` / `grant` |
| `workStatus` | ?`WorkStatus` | `no` / `partial` / `full_time` — o'qish bilan birga ishlaydimi |
| `priorEducation` | ?string(255) | universitetgacha tugatgan ta'lim muassasasi |
| `gpaScore` | ?string(16) | o'tgan semestr o'rtacha bahosi (reyting balli) |
| `languageLevel` | ?string(128) | chet tili darajasi (sertifikat turi bo'lsa) |
| `extracurricular` | ?text | to'garak / sport / ijtimoiy faoliyat |
| `leisureActivity` | ?text | bo'sh vaqtni qanday o'tkazadi |
| `healthLimitations` | ?text | sog'liq cheklovlari — **anketada aniq ixtiyoriy**, completeness'ga kirmaydi |
| `priorPsychologistVisit` | ?bool | ilgari psixologga murojaat qilganmi |
| `currentConcern` | ?text | hozir ko'proq tashvishga solayotgan narsa (erkin javob) |
| `updatedAt` | ?datetime | |

`getCompleteness(): int` — yuqoridagi ro'yxatdan `healthLimitations`
(va `personalCode`, `student`) tashqari **hammasi** bo'yicha foiz. Hech
qaysi maydon `PUT`da majburiy emas — talaba istagan qismini to'ldirib
saqlashi mumkin, foiz shunchaki qancha to'ldirilganini ko'rsatadi.

Jadval: `student_passport`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/student_passport` | `CurrentUserPassportProvider` — mavjud bo'lmasa bo'sh (saqlanmagan) obyekt |
| `PUT /api/student_passport` | `PassportPutProcessor` — `student` = joriy foydalanuvchi; `personalCode` bu yo'l bilan yozilmaydi |
| `GET /api/student_passports` | `ROLE_PSYCHOLOGIST` (ro'yxat, eksport uchun — `personalCode` ham shu yerda ko'rinadi) |

Filtr: `student`, `student.studyGroup`, `student.studyGroup.faculty` (fakultet arxivi —
front har bir talabaga PDF yaratib zip qiladi). Javobda `student.fullName`,
`student.hemisId`, `student.studyGroup{id,name}` bor. Sahifalash: `itemsPerPage`
(maks. 2000) — front 1000 tadan sahifalab oladi.
