# Autentifikatsiya va avtorizatsiya

## Kirish yo'llari

### 1. Login/parol (xodimlar, demo)

Skeletdan: `POST /api/users/auth` → `{accessToken, refreshToken}` (`TokensDto`).
`POST /api/users/auth/refreshToken` — yangilash.
Frontend `Authorization: Bearer <accessToken>` yuboradi.
`TOKEN_ACCESS_EXPIRATION_PERIOD=P1D`, `TOKEN_REFRESH_EXPIRATION_PERIOD=P2M`.

JWT identifikatori — `username` claim (= `email`). Har bir `User` da unikal
`email` bo'lishi shart.

### 2. HEMIS OAuth2 (asosiy yo'l — hali ulanmagan)

`.env`: `HEMIS_OAUTH_URL` (default `student.uzswlu.uz`), `HEMIS_CLIENT_ID`
(hozircha bo'sh). Reja:

- `GET /api/auth/hemis` → HEMIS'ga yo'naltirish.
- `GET /api/auth/hemis/callback` → profil olinadi, `User` topiladi/yaratiladi
  (`UserFactory::createFromHemis` — sintetik email `{hemisId}@hemis.uzswlu.uz`),
  `TokensCreator` JWT beradi. Rol HEMIS profilidan.

Ulanish uchun `HEMIS_CLIENT_ID`/`HEMIS_CLIENT_SECRET` va `src/Security/`
autentifikatori kerak. Hozir talaba `POST /api/students` (admin) orqali
yaratiladi va tasodifiy parol oladi (login uchun emas — impersonatsiya uchun).

## Foydalanuvchi holati (`User.status`)

`UserStatusEnum`: `pending` | `active` | `rejected`. Migratsiyada ustun sukut
bo'yicha `active` (mavjud foydalanuvchilar buzilmaydi).

| Holat | Kim | Tizimga kirish |
|---|---|---|
| `active` | login/parol demo hisoblari, tasdiqlangan xodim, HEMIS talabasi | ha |
| `pending` | HEMIS orqali birinchi marta kirgan **xodim** — hali tasdiqlanmagan | yo'q — "kutilmoqda" ekrani |
| `rejected` | admin rad etgan | yo'q — "rad etilgan" ekrani |

`UserStatusChecker` (`security.yaml` da `main` firewall'ning `user_checker`'i)
har bir so'rovda holatni tekshiradi — `active` bo'lmasa `401`
(`CustomUserMessageAccountStatusException`). Shu sababli `pending`/`rejected`
foydalanuvchining JWT'si hech qa­yerda ishlamaydi.

### Tasdiqlash oqimi

1. HEMIS xodimi `GET /api/auth/callback/hemis` orqali kiradi →
   `HemisLoginService` yangi `User` ni `status=pending`, `roles=[]` bilan
   yaratadi va **barcha adminlarga** `NotificationType::AccessRequest`
   bildirishnomasini yuboradi (`link=/admin/access-requests`).
2. `HemisCallbackAction` `pending` foydalanuvchi uchun token bermaydi —
   `{FRONTEND_URL}/auth/hemis#pending=1` ga qaytaradi.
3. Admin `GET /api/users?status=pending` bilan ro'yxatni ko'radi.
4. Admin `POST /api/users/{id}/approve` (`{"role":"ROLE_PSYCHOLOGIST"}` yoki
   `"ROLE_ADMIN"`) — `status=active`, `roles=[role]`. Foydalanuvchiga
   "kirish ruxsati berildi" bildirishnomasi.
5. Yoki `POST /api/users/{id}/reject` — `status=rejected`.
6. Endi xodim HEMIS orqali qayta kirsa — token oladi.

Talaba (`type != employee`) darhol `status=active`, `roles=[ROLE_STUDENT]` —
HEMIS `type` maydonining o'zi talaba statusining manbai.

Birinchi adminni CLI yaratadi: `php bin/console ask:user:make-admin <email>`
(`status=active`, `roles=[ROLE_ADMIN]`).

## Avtorizatsiya

`config/packages/security.yaml`:

```yaml
role_hierarchy:
    ROLE_PSYCHOLOGIST: [ROLE_STUDENT]
    ROLE_ADMIN: [ROLE_PSYCHOLOGIST]
```

Operatsiya darajasida `security: "is_granted('ROLE_...')"`. Obyekt darajasida
`Attempt`/`AssessmentResult`/`Appeal` da `object.getStudent() == user ||
is_granted('ROLE_PSYCHOLOGIST')`.

Talabaga tegishli ro'yxatlar State Provider'da filtrlanadi
(`StudentAttemptProvider`, `StudentResultProvider`, `AppealCollectionProvider`,
`StudentAssignmentProvider`, `CurrentUserNotificationProvider`,
`CurrentUserPassportProvider`).

## Anonim murojaat

`Appeal.mode = anonymous` → `student` bazada saqlanadi (javobni yetkazish uchun),
lekin API javobida `student` maydoni `appeal:read:staff` guruhida emas va
`getSenderName()` `null` qaytaradi.

## "Talaba sifatida ko'rish" (impersonatsiya)

`POST /api/students/{id}/impersonate` — **`ROLE_ADMIN`**. Berilgan talaba uchun
bizning JWT juftligini (`TokensDto`, `json`) qaytaradi. Admin bu tokenni
ishlatib, talaba ko'radigan barcha endpointlarga kiradi (test topshirish,
natijalar, pasport). Talaba bo'lmagan foydalanuvchiga `400`.

## HEMIS orqali kirish

To'liq: [`hemis-auth.md`](hemis-auth.md). Qisqacha: `GET /api/auth/hemis` →
HEMIS; `GET /api/auth/callback/hemis` → bizning JWT → `FRONTEND_URL` (`:3000`)
ga `#access`/`#refresh` bilan 302. Ikkovi `security.yaml` da `security: false`.
