# AnswerOption

Savol javob varianti. Alohida API resurs emas — `Question` ichida
(`question:write`) yoziladi va o'qiladi.

| Maydon | Tip | Izoh |
|---|---|---|
| `question` | ManyToOne `Question`, not null | |
| `text` | text | |
| `imageUrl` | ?string | figura ikonasi (`mdi-...`) yoki rasm |
| `position` | smallint | |
| `score` | smallint = 0 | `SCORE_SCALE` da 1..N; statements da "Ha" = 1 |
| `categoryKey` | ?string | temperament bloki / figura kaliti; ballashda ishlatiladi |

Jadval: `answer_option`. Tanlangan variantlar `AttemptAnswer` bilan
ManyToMany (`attempt_answer_option`).
