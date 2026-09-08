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
