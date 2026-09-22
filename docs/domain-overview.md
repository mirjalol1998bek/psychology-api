# Domen modeli — umumiy ko'rinish

## Aloqalar

```
Faculty 1───* StudyGroup 1───* User(student)
Category 1───* Quiz 1───* Question 1───* AnswerOption
Category 1───* ScoreRange                (faqat SCORE_SCALE)
Category 1───* AssessmentInterpretation  (natija matni, uz/ru)
Category 1───* Assignment *───1 StudyGroup
Assignment 1───* Attempt *───1 User(student)
Attempt 1───* AttemptAnswer *───1 Question
AttemptAnswer *───* AnswerOption         (tanlangan variantlar)
Attempt 1───1 AssessmentResult
User(psixolog) 1───* AppointmentSlot *───0..1 User(student)
User(student) 1───* Appeal *───0..1 User(psixolog, javob bergan)
User(student) 1───1 StudentPassport
User 1───* Notification
StudyGroup *───1 User(tutor)              (many-to-one, HEMIS'dan sinxron)
User(tutor) 1───* ObservationCard *───1 User(student)  (Category/Quiz/Attempt'dan mustaqil — observation-card.md)
(Sotsiometriya — Category/Quiz/Attempt orqali, lekin natija Attempt emas —
 guruhning BARCHA Attemptlari birlashtirilib hisoblanadi — sociometry.md)
```

## Rollar (`RoleEnum`, Symfony `roles[]`)

| Rol | Nima qila oladi |
|---|---|
| `ROLE_STUDENT` | O'ziga biriktirilgan `Assignment`; o'z `Attempt` / `AssessmentResult` / `Appeal` / `StudentPassport`; bildirishnomalar |
| `ROLE_TUTOR` | Faqat o'ziga (`StudyGroup.tutor`) biriktirilgan guruhlar talabalari ro'yxati (`GET /api/tutor/students`) — test natijalariga kirmaydi. Hierarxiyaga kirmaydi (`isStaff()=false`), staff huquqlaridan mustaqil |
| `ROLE_PSYCHOLOGIST` | Barcha natijalar, murojaatlar, kalendar, metodika (Category/Quiz/Question/...) yaratish |
| `ROLE_ADMIN` | Hammasi + `Faculty` / `StudyGroup` / talaba yaratish |

`config/packages/security.yaml` da `role_hierarchy`:
`ROLE_ADMIN > ROLE_PSYCHOLOGIST > ROLE_STUDENT`. `ROLE_TUTOR` alohida —
hierarxiyaga kirmaydi, faqat o'z guruhiga scoped.

**Muhim:** kontroller darajasidagi `#[IsGranted(...)]` faqat
`Symfony\Component\Security\Http\Attribute\IsGranted` bilan ishlaydi —
`Symfony\Component\HttpKernel\Attribute\IsGranted` degan klass **mavjud
emas** (PHP `use`'ni jim yutib yuboradi, attribute hech qachon
o'qilmaydi — rol tekshiruvi butunlay o'chib qoladi). 2026-09-21'da shu
xato 15 ta kontrollerda topilib tuzatildi.

## Metodika (instrument) — bu **ma'lumot**, kod emas

`Category` bitta metodikani ifodalaydi; `Category.instrumentType`
(`InstrumentType`) — **ballash algoritmi**. `SCORE_SCALE` oilasidagi yangi
metodika (Ibodullayev shkalasi kabi) qo'shish = yangi `Category`
+ `Quiz` + `Question`/`AnswerOption` (+ `ScoreRange`/`AssessmentInterpretation`)
qatorlari, PHP kodi yozilmasdan. Subshkalali/ishorali yangi turdagi metodika
(IPM-20/OKM-20/EHS-20 kabi) esa frontendni ajratish uchun o'ziga xos
`InstrumentType` qiymatini talab qiladi. Batafsil:
[`assessment-scoring.md`](assessment-scoring.md).

## Til (uz/ru)

`StudyGroup.studyLanguage` talabaning tilini beradi (`User.getStudyLanguage()`
guruhdan meros oladi). `QuizProvider` kategoriya + til bo'yicha `Quiz` topadi,
mos til bo'lmasa uz ga qaytadi. `AssessmentInterpretation` uz/ru alohida.

## Biznes logika joylashuvi

`src/Component/<Domen>/`. `Controller`, `State`, `EventSubscriber` faqat
`Component` xizmatlarini chaqiradi. `Repository` — faqat `SELECT`. Entity saqlash
— `EntityNameManager extends AbstractManager` orqali.
