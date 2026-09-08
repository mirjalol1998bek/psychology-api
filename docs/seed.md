# `ask:seed:assessments` — tayyor metodikalar

```bash
docker compose exec php bin/console ask:seed:assessments
```

Idempotent: kategoriya nomi bo'yicha mavjud bo'lsa o'tkazib yuboradi.

`src/Component/Assessment/Seed/`:

| Klass | Vazifasi |
|---|---|
| `CatalogSeeder` | Orkestratsiya — `AssessmentFactory` orqali entity quradi, `CategoryManager`/`QuizManager` saqlaydi |
| `TemperamentUzData` | 4 blok × 20 bayonot + uz tahlil (manba: `psychology-front/.../temperament-uz.ts`, `descriptions.ts`) |
| `TemperamentRuData` | 14 savol × 4 variant + ru tahlil (`temperament-ru.ts`) |
| `PsychogeometricData` | 5 figura (uz/ru label) + uz/ru tahlil (`psychogeometric.ts`, `descriptions.ts`) |
| `ZungData` | Zung o'z-o'zini baholash depressiya shkalasi: 20 savol (10 tasi teskari), 4 oraliq, uz tahlil |

Yaratiladigan kategoriyalar:

| Category | `instrumentType` | Quiz(lar) | Savol |
|---|---|---|---|
| Temperament testi (uz) | `TEMPERAMENT_STATEMENTS` | uz | 80 (`YES_NO`) |
| Тест на темперамент (ru) | `TEMPERAMENT_CHOICE` | ru | 14 (`SINGLE_CHOICE`) |
| Psixogeometrik test | `FIGURE_CHOICE` | uz + ru | 1 (`FIGURE`, 5 variant) |
| Zung depressiya shkalasi | `SCORE_SCALE` | uz | 20 (`SCALE`, 1–4 ball) |

`AnswerOption.categoryKey`:
- statements: "Ha" variantida blok kaliti (`Xolerik`/...), "Yo'q" da `null`
- choice: har variantda temperament kaliti
- figure: figura kaliti (`Doira`/...), `imageUrl` = mdi ikona nomi
- scale: `null` (faqat `score` 1–4)

## Ibodullayev shkalasi va boshqalar

Seed'ga kiritilmagan (rasmiy savol matni kerak). Qo'shish tartibi —
[`assessment-scoring.md`](assessment-scoring.md) "Yangi metodika qo'shish".
