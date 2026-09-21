# ObservationCard — 10-metodika (kuzatuv kartasi)

Kurator (tyutor) o'z guruhi talabasi haqida to'ldiradigan **ekspert bahosi**.
Boshqa 9 ta metodikadan tubdan farqli: talaba o'zi haqida emas — tyutor
talaba haqida to'ldiradi. Shu sabab **Category/Quiz/Question/Attempt
tizimidan butunlay mustaqil**, alohida entity (`assessment-scoring.md`dagi
"Mustaqil Scorer" naqshidan ham ko'ra chuqurroq ajralish — bu yerda hatto
`Category` ham yo'q, chunki Kategoriya→Assignment→StudyGroup oqimi "guruhga
test biriktirish" degani, Kuzatuv kartasi esa guruhga emas, **bitta
talabaga** tyutor tomonidan to'ldiriladi).

Manba: rasmiy hujjat (`10-қадам.docx`, 2026-09-21 taqdim etilgan) — matn
o'zbek tilida, shu sabab butun metodika (ko'rsatkichlar, band matni) faqat
o'zbek tilida (tarjima qilinmagan — rasmiy klinik matnni o'zboshimchalik
bilan tarjima qilish xato bo'lardi).

## Model

| Maydon | Tip | Izoh |
|---|---|---|
| `student` | ManyToOne `User`, not null, **UNIQUE** | kim haqida — har talaba uchun **bir marta** (hozirgi qaror) |
| `tutor` | ManyToOne `User`, not null | kim to'ldirgan — server tomonidan joriy foydalanuvchidan o'rnatiladi, client yubormaydi |
| `scores` | json (`array<string, int>`) | 15 ko'rsatkich kaliti → ball (0-3), `ObservationCardData::INDICATORS` |
| `totalScore` | smallint | server hisoblaydi (`array_sum`) |
| `riskLevel` | `ObservationRiskLevel` enum | server hisoblaydi, `ObservationCardData::BANDS`dan |
| `alertTriggered` | bool | `behavior_change` (9-ko'rsatkich) === 3 bo'lsa |
| `createdAt` | datetime | |

15 ko'rsatkich va 4 band (`0–10`/`11–22`/`23–34`/`35–45`) matni
`src/Component/ObservationCard/ObservationCardData.php`da qattiq
kodlangan (rasmiy, o'zgarmas matn — admin orqali tahrirlanmaydi, farqli
o'laroq `ScoreRange`/`AssessmentInterpretation`dan). Front tomonda xuddi shu
matn `src/data/assessments/observationCard.ts`da takrorlangan (front/back
mustaqil, sinxronizatsiya qo'lda).

## Hisoblash — `ObservationCardCreator`

`AttemptSubmitter`ga o'xshamaydi (Attempt yo'q). `create(ObservationCard
$card, User $tutor)`:

1. **Guruh tekshiruvi**: `card.student.studyGroup.tutor === $tutor`,
   aks holda `403` (`Bu talaba sizning guruhingizda emas`).
2. **Bir martalik tekshiruvi**: shu talaba uchun mavjud karta bo'lsa `400`.
3. **Ballar tekshiruvi**: barcha 15 kalit mavjud, har biri `0..3` oralig'ida
   (aks holda `400`).
4. `totalScore` = yig'indi, `riskLevel` = band bo'yicha, `alertTriggered` =
   `behavior_change === 3`.
5. Saqlanadi. `alertTriggered` bo'lsa —
   `NotificationDispatcher::notifyStaffOnObservationAlert()` (barcha
   psixolog+admin, `NotificationType::ObservationAlert`) — rasmiy qoida:
   "9-ko'rsatkich 3 ball bilan baholansa — jami balldan qat'i nazar, darhol
   individual suhbat o'tkaziladi".

## API

| Operatsiya | Ruxsat | Izoh |
|---|---|---|
| `POST /api/observation_cards` | `ROLE_TUTOR` | `{student: IRI, scores: {...15 kalit}}`; `tutor`/`totalScore`/`riskLevel`/`alertTriggered` server hisoblaydi |
| `GET /api/observation_cards/{id}` | to'ldirgan tyutor yoki `ROLE_PSYCHOLOGIST`+ | `object.getTutor() == user \|\| is_granted('ROLE_PSYCHOLOGIST')` — **talaba ko'rmaydi** |
| `GET /api/observation_cards` | auth | Tyutor — faqat o'zi to'ldirganlari; psixolog/admin (`isStaff()`) — hammasi; talaba — bo'sh ro'yxat (`ObservationCardCollectionProvider`) |
| `DELETE /api/observation_cards/{id}` | `ROLE_ADMIN` | xato to'ldirilgan kartani o'chirish uchun |

Filtr: `student`, `student.studyGroup`, `riskLevel` (`ApiFilter`da e'lon
qilingan — lekin `GetCollection` custom provider ishlatgani uchun
haqiqatda faqat OpenAPI hujjat uchun, `Appeal`dagi kabi — amaliy filtr
kerak bo'lsa provider ichida qo'lda qo'shiladi).

`User.fullName`/`studyGroup` va `StudyGroup.name`ga `observation-card:read`
guruhi qo'shilgan — shu orqali `student`/`tutor` javobda **to'liq ichma-ich
obyekt** (`{fullName, studyGroup:{name}}`) sifatida keladi, sof IRI emas
(`user.md`dagi bir xil naqsh).

## `GET /api/tutor/students` bilan bog'liqligi

Tyutor ro'yxatida (`study-group.md`) har talaba qatorida
`hasObservationCard: bool` bor — `TutorStudentsAction` joriy tyutor
to'ldirgan kartalar bo'yicha talaba ID to'plamini oldindan yig'ib beradi.
Frontend shu bilan "to'ldirish" tugmasi o'rniga "to'ldirilgan" belgisini
ko'rsatadi.

## Frontend

- `src/data/assessments/observationCard.ts` — 15 ko'rsatkich, baholash
  mezoni (legend), 4 band matni — backend `ObservationCardData.php` bilan
  qo'lda sinxron.
- `src/views/tutor/TutorStudentsView.vue` — guruh bo'yicha talabalar
  ro'yxati (`/tutor/students`, `ROLE_TUTOR`).
- `src/views/tutor/ObservationCardFormView.vue` — bitta talaba uchun forma
  (`/tutor/students/:id/observation-card`) — har ko'rsatkich uchun
  `v-btn-toggle` (0-3), barchasi to'ldirilmaguncha "Saqlash" faolsiz.
  Muvaffaqiyatli saqlangach — jami ball + band sarlavha/chora matnini
  ko'rsatadi (local `observationCard.ts`dan, server faqat
  `totalScore`/`riskLevel` qaytaradi).
- `src/views/observation-cards/ObservationCardsView.vue` — psixolog/admin
  uchun barcha kartalar ro'yxati (`/observation-cards`, `STAFF`), xavf
  darajasi rangli chip bilan, alert belgisi (⚠) `alertTriggered` bo'lsa.

Tyutor uchun asosiy menyu boshqa rollardan farqli — juda tor (faqat "Bosh
sahifa" + "Mening guruhim"): test topshirmaydi, natijalarga kirmaydi
(`useNavItems.ts`).
