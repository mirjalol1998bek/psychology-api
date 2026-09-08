# HEMIS OAuth2 — kirish

Universitet SSO orqali kirish. Standart OAuth2 **authorization code** oqimi.

> **Muhim:** hozirgi `HEMIS_CLIENT_ID=4` — **xodim (Xodim ID)** OAuth klienti
> (`hemis.uzswlu.uz/dashboard/login`). Talabalar bu klient orqali kira olmaydi —
> ular uchun alohida `studentoauth` klienti kerak (client_id/secret hali yo'q).
> Talabalar hozircha demo parol (`ask:seed:demo`) yoki admin impersonatsiyasi
> (`POST /api/students/{id}/impersonate`) orqali kiradi.
>
> Callback'da xatolik bo'lsa `HemisCallbackAction` foydalanuvchini
> `{FRONTEND_URL}/auth/hemis#error=<sabab>` ga qaytaradi (blank ekran emas),
> `HemisClient` esa HEMIS javobining bir qismini xabarga qo'shadi. `state`
> imzo umri — 30 daqiqa (`HemisStateSigner`).

## Konfiguratsiya

| O'zgaruvchi | Joyi | Izoh |
|---|---|---|
| `HEMIS_CLIENT_ID` | `.env` | ochiq |
| `HEMIS_CLIENT_SECRET` | **`.env.local`** (git'ga tushmaydi) | maxfiy |
| `HEMIS_API_TOKEN` | **`.env.local`** | HEMIS REST API (org sinxron uchun, ixtiyoriy) |
| `HEMIS_AUTH_URL` | `.env` | `https://hemis.uzswlu.uz/oauth/authorize` |
| `HEMIS_TOKEN_URL` | `.env` | `https://hemis.uzswlu.uz/oauth/access-token` |
| `HEMIS_USERINFO_URL` | `.env` | `https://hemis.uzswlu.uz/oauth/api/user?fields=...` |
| `HEMIS_REDIRECT_URI` | `.env` | `http://localhost:3000/api/auth/callback/hemis` — HEMIS'da ro'yxatdan o'tgan bo'lishi shart |
| `FRONTEND_URL` | `.env` | `http://localhost:3000` — callback oxirida shu yerga token bilan qaytariladi |

`HEMIS_REDIRECT_URI` **frontend porti** (3000) bilan. Frontend dev-server
(`Vite`) `/api` so'rovlarini backend'ga (`:8508`) proksilaydi, shu sababli
`localhost:3000/api/auth/callback/hemis` aslida backend'ga tushadi.

## Oqim

```
Brauzer                Frontend(:3000/api → proxy)      Backend(:8508)         HEMIS
  │  "HEMIS orqali kirish"                                    │                   │
  │ ────────────────► GET /api/auth/hemis ──────────────────► │                   │
  │                                          302 authorize?client_id&redirect_uri&state
  │ ◄──────────────────────────────────────────────────────── │                   │
  │ ───────────────────────────────────────────────────────────────────────────► login
  │ ◄──────────── 302 /api/auth/callback/hemis?code=&state= ──────────────────────┤
  │ ────────────► GET /api/auth/callback/hemis ─────────────► │                   │
  │                                     POST access-token (code+secret) ────────► │
  │                                     ◄──────────────────────── {access_token}  │
  │                                     GET userinfo (Bearer) ─────────────────►  │
  │                                     ◄──────────────────────── {profil}        │
  │                            User topiladi/yaratiladi, bizning JWT              │
  │ ◄──── 302 {FRONTEND_URL}/auth/hemis#access=<jwt>&refresh=<jwt> ────────────── │
  │  SPA fragmentdan tokenlarni oladi, saqlaydi                                   │
```

`state` — CSRF himoyasi. Backend `state` ni imzolaydi (HMAC, `APP_SECRET`),
callback'da tekshiradi. Sessiyasiz (stateless) — `state` o'zida `nonce` + vaqt.

## Profil → User

`HemisProfile` (readonly DTO): `id`, `login`, `fullName`, `email`, `type`
(`student` / `employee`), `picture`, `phone`, `groupName?`, `facultyName?`.

`UserFactory::createFromHemis(HemisProfile)`:

- `hemisId = profile.id`
- `email = profile.email ?? "{login}@hemis.uzswlu.uz"` (sintetik, unikal)
- `fullName`, `image`
- rol: `type === 'employee'` → `ROLE_PSYCHOLOGIST`; aks holda `ROLE_STUDENT`
  (admin keyin qo'lda `ROLE_ADMIN` beradi)
- talaba bo'lsa va `groupName` HEMIS'dan kelsa — mavjud `StudyGroup` ga
  `externalId`/`name` bo'yicha ulanadi (topilmasa `null`, admin biriktiradi)

Mavjud foydalanuvchi `hemisId` bo'yicha topiladi; `fullName`/`image` yangilanadi,
rol **o'zgartirilmaydi** (admin bergan rollar saqlanadi).

## Endpointlar (`src/Controller/Hemis*Action`)

| Yo'l | Vazifasi |
|---|---|
| `GET /api/auth/hemis` | HEMIS authorize sahifasiga 302 |
| `GET /api/auth/callback/hemis` | code → token → profil → User → bizning JWT → `FRONTEND_URL` ga 302 |

Ikkovi `security.yaml` da autentifikatsiyasiz (`^/api/auth/hemis`).

## HEMIS REST API (org sinxron — ixtiyoriy, hali ulanmagan)

`HEMIS_API_TOKEN` bilan fakultet/guruh/talaba ro'yxatini olish mumkin
(`ask:hemis:sync`). Hozircha admin `POST /api/students` orqali qo'lda kiritadi.
