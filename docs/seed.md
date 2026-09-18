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
| `Ipm20Data` | IPM-20: 4 subshkala × 5 savol (uz/ru), subshkala bo'yicha 5–25 oraliq + umumiy talqin |
| `Okm20Data` | OKM-20: 4 subshkala × 5 savol (uz/ru), ishorali umumiy indeks (IMI = (A+B) − (C+D)) |
| `Ehs20Data` | EHS-20: 3 subshkala × savol (uz/ru), teng bo'lmagan subshkala o'lchami, ishorali umumiy indeks (ERI = (A+B) − C) |

Yaratiladigan kategoriyalar:

| Category | `instrumentType` | Quiz(lar) | Savol |
|---|---|---|---|
| Temperament testi (uz) | `TEMPERAMENT_STATEMENTS` | uz | 80 (`YES_NO`) |
| Тест на темперамент (ru) | `TEMPERAMENT_CHOICE` | ru | 14 (`SINGLE_CHOICE`) |
| Psixogeometrik test | `FIGURE_CHOICE` | uz + ru | 1 (`FIGURE`, 5 variant) |
| IPM-20 | `SCORE_SCALE_SUBSCALE` | uz + ru | 20 (`SCALE`, 1–5 ball) |
| OKM-20 | `SCORE_SCALE_MOTIVATION` | uz + ru | 20 (`SCALE`, 1–5 ball) |
| EHS-20 | `SCORE_SCALE_EMOTIONAL` | uz + ru | 20 (`SCALE`, 1–5 ball) |

`AnswerOption.categoryKey`:
- statements: "Ha" variantida blok kaliti (`Xolerik`/...), "Yo'q" da `null`
- choice: har variantda temperament kaliti
- figure: figura kaliti (`Doira`/...), `imageUrl` = mdi ikona nomi
- scale: `null` (faqat `score`, odatda 1–5)

## `ask:seed:demo` — demo hisoblar

```bash
docker compose exec php bin/console ask:seed:demo
```

`ask:seed:assessments` ni ham chaqiradi, so'ng (idempotent):

- `Demo fakultet` + `DEMO-01` guruh (uz)
- 3 hisob (parol `demo1234`): `admin@demo.uz` (ADMIN), `psixolog@demo.uz`
  (PSYCHOLOGIST), `talaba@demo.uz` (STUDENT, DEMO-01 da)
- Barcha metodikalar `DEMO-01` ga biriktiriladi

Frontend login sahifasidagi "Login va parol" — shu hisoblar bilan.
`src/Component/Assessment/Seed/DemoSeeder.php`.

## Ibodullayev shkalasi va boshqalar

Seed'ga kiritilmagan (rasmiy savol matni kerak). Qo'shish tartibi —
[`assessment-scoring.md`](assessment-scoring.md) "Yangi metodika qo'shish".
