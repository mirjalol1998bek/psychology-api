# AttemptAnswer

Urinish ichidagi bitta savolga javob. Alohida API resurs emas.

| Maydon | Tip | Izoh |
|---|---|---|
| `attempt` | ManyToOne `Attempt`, not null | |
| `question` | ManyToOne `Question`, not null | |
| `selectedOptions` | ManyToMany `AnswerOption` (`attempt_answer_option`) | tanlangan variant(lar) |
| `textValue` | ?text | `WRITING` savoli uchun |

`addSelectedOption()`, `getFirstOption(): ?AnswerOption`.
Jadval: `attempt_answer`.

`AttemptAnswerRecorder::record()` har chaqiruvda barcha javoblarni qayta yozadi
(eski javoblar `orphanRemoval` bilan o'chadi).
