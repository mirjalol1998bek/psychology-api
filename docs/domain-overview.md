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
```

## Rollar (`RoleEnum`, Symfony `roles[]`)

| Rol | Nima qila oladi |
|---|---|
| `ROLE_STUDENT` | O'ziga biriktirilgan `Assignment`; o'z `Attempt` / `AssessmentResult` / `Appeal` / `StudentPassport`; bildirishnomalar |
| `ROLE_PSYCHOLOGIST` | Barcha natijalar, murojaatlar, kalendar, metodika (Category/Quiz/Question/...) yaratish |
| `ROLE_ADMIN` | Hammasi + `Faculty` / `StudyGroup` / talaba yaratish |

`config/packages/security.yaml` da `role_hierarchy`:
`ROLE_ADMIN > ROLE_PSYCHOLOGIST > ROLE_STUDENT`.

## Metodika (instrument) — bu **ma'lumot**, kod emas

`Category` bitta metodikani ifodalaydi; `Category.instrumentType`
(`InstrumentType`) — **ballash algoritmi** (4 ta qiymat). Yangi metodika
(Zung, Ibodullayev, nevrasteniya so'rovnomasi, ...) qo'shish = yangi `Category`
+ `Quiz` + `Question`/`AnswerOption` (+ `ScoreRange`/`AssessmentInterpretation`)
qatorlari. Yangi PHP kodi yozilmaydi. Batafsil:
[`assessment-scoring.md`](assessment-scoring.md).

## Til (uz/ru)

`StudyGroup.studyLanguage` talabaning tilini beradi (`User.getStudyLanguage()`
guruhdan meros oladi). `QuizProvider` kategoriya + til bo'yicha `Quiz` topadi,
mos til bo'lmasa uz ga qaytadi. `AssessmentInterpretation` uz/ru alohida.

## Biznes logika joylashuvi

`src/Component/<Domen>/`. `Controller`, `State`, `EventSubscriber` faqat
`Component` xizmatlarini chaqiradi. `Repository` — faqat `SELECT`. Entity saqlash
— `EntityNameManager extends AbstractManager` orqali.
