# Bildirishnomalar

`Notification` — foydalanuvchiga oddiy xabar (`type`, `title`, `body`, `link`,
`isRead`, `createdAt`).

## Yaratilishi — `NotificationDispatcher`

| Hodisa | Kim oladi | `type` |
|---|---|---|
| Yangi murojaat (`AppealCreator`) | barcha psixolog + admin | `appeal_new` |
| Murojaatga javob (`AppealReplyWriter`) | murojaat egasi (talaba) | `appeal_reply` |

`NotificationFactory::create()` obyektni quradi, `NotificationManager::save()`
saqlaydi (oxirgisida `flush`). `title`/`body` — `NotificationDispatcher` da;
`body` = murojaat matnining 120 belgigacha qismi.

Kelajakda: `assignment_new` (yangi biriktirish), `appointment_reminder`
(qabul eslatmasi) — `NotificationType` da bor, dispetcher qo'shilishi kerak.

## O'qish

- `GET /api/notifications` → `CurrentUserNotificationProvider` — faqat joriy
  foydalanuvchiniki, `id DESC`.
- `POST /api/notifications/mark_read` → `NotificationMarkReadAction` →
  `NotificationReader::markAllAsRead()` — barcha o'qilmaganni belgilaydi, `204`.

Frontend qo'ng'iroqcha ikonasi o'qilmaganlar soni bo'yicha badge ko'rsatadi.
