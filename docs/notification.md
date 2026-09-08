# Notification

Foydalanuvchiga bildirishnoma. Jarayon: [`notifications.md`](notifications.md).

| Maydon | Tip | Izoh |
|---|---|---|
| `recipient` | ManyToOne `User`, not null | |
| `type` | `NotificationType` | `appeal_new` / `appeal_reply` / `assignment_new` / `appointment_reminder` |
| `title` | string | |
| `body` | text | |
| `link` | ?string | frontend yo'li, masalan `/appeals/12` |
| `isRead` | bool = false | `getIsRead()` |
| `createdAt` | datetime | |

`markAsRead()`.

## API

| Operatsiya | Ruxsat |
|---|---|
| `GET /api/notifications` | `CurrentUserNotificationProvider` — faqat o'ziniki, `id DESC` |
| `POST /api/notifications/mark_read` | `NotificationMarkReadAction` — barcha o'qilmagan → o'qilgan, `204` |
