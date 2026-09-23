# HEMIS OAuth2 — kirish

Universitet SSO orqali kirish. Standart OAuth2 **authorization code** oqimi.

> **Ikki portal, bitta klient:** HEMIS alohida talaba klienti bermaydi — o'sha
> `HEMIS_CLIENT_ID`/`SECRET` va `HEMIS_REDIRECT_URI` ikkala portalda ishlatiladi:
> xodim — `hemis.uzswlu.uz`, talaba — `HEMIS_STUDENT_URL` (standart
> `https://student.uzswlu.uz`). Yo'llar (`/oauth/authorize`, `/oauth/access-token`,
> `/oauth/api/user?fields=...`) bir xil, faqat host almashadi (`HemisConfig::forPortal`).
> `GET /api/auth/hemis?portal=student|employee` (standart `employee`); tanlangan
> portal imzolangan `state` ichida (`nonce.vaqt.portal.imzo`) callback'ga
> qaytadi — soxtalashtirib bo'lmaydi. Talaba portalidan kirgan har doim
> `type=student` deb olinadi.
>
> Callback natijalari (`{FRONTEND_URL}/auth/hemis#...`):
> `#access=&refresh=` — muvaffaqiyat; `#pending=1` — xodim tasdiq kutmoqda;
> `#rejected=1` — admin rad etgan; `#error=<sabab>` — xatolik (blank ekran emas).
> `state` imzo umri — 30 daqiqa (`HemisStateSigner`).

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

- `hemisId = profile.login` (Xodim/Talaba ID — HEMIS sinxroni ham shu
  kalitni ishlatadi: `employee_id_number` / `student_id_number`)
- `email = profile.email ?? "{login}@hemis.uzswlu.uz"` (sintetik, unikal)
- `fullName`, `image`
- **xodim** (`type === 'employee'`) → `status = pending`, `roles = []` —
  tizimga kira olmaydi, admin `POST /api/users/{id}/approve` bilan rol beradi
- **talaba** → `status = active`, `roles = [ROLE_STUDENT]`
- talaba bo'lsa va `groupName` HEMIS'dan kelsa — mavjud `StudyGroup` ga
  `externalId`/`name` bo'yicha ulanadi (topilmasa `null`, admin biriktiradi)

Mavjud foydalanuvchi `hemisId` bo'yicha topiladi — avval `profile.id`
(dastlabki OAuth yozuvlari ichki raqam bilan saqlangan, masalan `2506`),
topilmasa `profile.login` (oldindan sinxronlangan tyutor/talaba). Aks holda
sinxronlangan tyutor HEMIS orqali kirganda ikkinchi `pending` hisob ochilardi
yoki `email` unikalligi bo'yicha xato berardi. `fullName`/`image` yangilanadi,
rol va `status` **o'zgartirilmaydi** (admin bergan rollar saqlanadi).

Yangi `pending` foydalanuvchi yaratilganda `HemisLoginService` barcha adminlarga
`AccessRequest` bildirishnomasini yuboradi. To'liq oqim: [`auth.md`](auth.md).

## Endpointlar (`src/Controller/Hemis*Action`)

| Yo'l | Vazifasi |
|---|---|
| `GET /api/auth/hemis` | HEMIS authorize sahifasiga 302 |
| `GET /api/auth/callback/hemis` | code → token → profil → User → bizning JWT → `FRONTEND_URL` ga 302 |

Ikkovi `security.yaml` da autentifikatsiyasiz (`^/api/auth/hemis`).

## HEMIS REST API (org sinxron)

Fakultet/guruh/talabani HEMIS'dan ko'chirish — alohida hujjat:
[`hemis-sync.md`](hemis-sync.md). Token bilan (`student.uzswlu.uz/rest/v1`),
OAuth'dan mustaqil. Admin `POST /api/students` bilan qo'lda ham kiritishi mumkin.
