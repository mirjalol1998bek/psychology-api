# Question

Quiz ichidagi bitta savol.

| Maydon | Tip | Izoh |
|---|---|---|
| `quiz` | ManyToOne `Quiz`, not null | |
| `type` | `QuestionType` | `YES_NO`, `SINGLE_CHOICE`, `MULTI_SELECT`, `SINGLE_CHOICE_IMAGE`, `FIGURE`, `SCALE`, `WRITING` |
| `text` | text | |
| `imageUrl` | ?string | |
| `position` | smallint | |
| `isReversed` | bool = false | `getIsReversed()` — `SCORE_SCALE` da teskari ballash |
| `subscaleKey` | string = `''` | `SCORE_SCALE` metodikada subshkala nomi (masalan "Akademik moslashuv" — IPM-20). Bo'sh = faqat umumiy ballga kiradi. [`assessment-scoring.md`](assessment-scoring.md) |
| `overallSign` | smallint = `1` | UMUMIY ballga qo'shiladigan ishora (+1/−1) — masalan OKM-20: IMI = (A+B) − (C+D). Subshkalaning o'z ballini o'zgartirmaydi |
| `subscaleRangeKey` | string = `'*'` | Subshkala qaysi ball oralig'i/talqin jadvalidan foydalanadi. `'*'` = barcha subshkalalar umumiy (IPM-20, OKM-20). Teng bo'lmagan subshkalalarda (EHS-20) har guruh o'z kalitiga ega |
| `options` | OneToMany `AnswerOption`, cascade persist+remove, orphanRemoval, `OrderBy position` | |

`addOption()`, `hasOption(AnswerOption): bool`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/questions`, `.../{id}` | auth |
| `POST`, `PATCH` | `ROLE_PSYCHOLOGIST` — variantlar bilan birga (`question:write`) |
| `DELETE` | `ROLE_PSYCHOLOGIST` — testni tahrirlash uchun |

Filtr: `quiz`.

**Test tahriri:** frontend `CreateTestView` tahrir rejimida eski savollarni
`DELETE` qilib yangilarini `POST` qiladi. Talaba allaqachon javob bergan
savol o'chirilsa — `attempt_answer` va `attempt_answer_option` yozuvlari
`ON DELETE CASCADE` bilan tozalanadi (Version20260909…).
