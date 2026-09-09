# Tezlik (performance)

Maqsad: backend so'rovlari tez javob bersin, og'ir ishlar (HEMIS sinxroni)
foydalanuvchini kutdirmasin.

## O'lchovlar (dev, Windows + Docker Desktop, `/api/faculties?itemsPerPage=2000`)

| Holat | Iliган (warm) so'rov |
|---|---|
| Oldin (var/ bind-mount, OPcache sozlanmagan) | ~1.9–2.7s |
| Hozir (var/ nomli volume + OPcache/JIT + revalidate_freq=60) | ~1.2–1.5s |
| `APP_ENV=prod` (shu mashinada) | ~0.7–0.9s |

Qolgan ~1s — Windows Docker Desktop'ning bind-mount + FPM/nginx ustama xarajati.
Buni butunlay yo'qotishning yagona yo'li — **WSL2 fayl tizimida** ishlash
(loyihani `\\wsl$\...` ichida saqlash) yoki Linux server.

## Nima qilindi

### 1. `var/` — nomli Docker volume (`docker-compose.yml`)

```yaml
volumes:
  - symfony_var:/var/www/html/var
```

Symfony keshi (`var/cache`), loglar, kesh pool'lari endi sekin Windows
bind-mount'da emas. Bu eng katta yutuq. Loglarni ko'rish:

```
docker compose exec php tail -f var/log/dev.log
```

### 2. OPcache + JIT (`docker/php/php.ini`)

- `opcache.enable=1`, `opcache.jit=tracing`, `jit_buffer_size=128M`
- `opcache.max_accelerated_files=30000` (Symfony + vendor katta)
- `opcache.validate_timestamps=1`, `revalidate_freq=60` — dev'da fayl
  o'zgarishini 60s ichida ko'radi. Tez kerak bo'lsa:
  `docker compose exec php php bin/console cache:clear`.
- `realpath_cache_size=4096K` — Symfony ko'p fayl `stat` qiladi.

### 3. APCu (`docker/php/Dockerfile`)

`pecl install apcu` — Doctrine metadata / API Platform metadata keshi uchun
tez umumiy xotira (fayl tizimidan tez).

### 4. HEMIS sinxroni — fon rejimi

Messenger navbat + kechki cron + rate limiter. Batafsil: [hemis-sync.md](hemis-sync.md).
HTTP so'rov endi sinxronni kutmaydi (`202 Accepted`).

### 5. `memory_limit = 512M` (2G edi — keraksiz)

## PROD deploy cheklisti

1. `APP_ENV=prod`, `APP_DEBUG=0`.
2. `composer install --no-dev --optimize-autoloader --classmap-authoritative`.
3. `php bin/console cache:clear && cache:warmup` (deploy paytida).
4. `php.ini` PROD:
   - `opcache.validate_timestamps=0` — deploy'da `php-fpm` restart yoki
     `opcache_reset()`.
   - `opcache.preload=/var/www/html/config/preload.php`,
     `opcache.preload_user=www-data`.
5. Doctrine: `query_cache` / `result_cache` pool (allaqachon `when@prod`da,
   `config/packages/doctrine.yaml`).
6. `messenger:consume` worker'lari supervisor ostida (allaqachon).
7. DB: tez-tez so'raladigan ustunlarga indeks (`user.hemis_id`,
   `study_group.external_id`, `attempt.user_id` — migratsiyalarda bor).
8. Nginx: statik `public/` ni to'g'ridan-to'g'ri bersin, `gzip on`.

## Dev'da "tez rejim" (ixtiyoriy)

Xatolik sahifalari kerak bo'lmasa, `.env.local`:

```
APP_ENV=prod
APP_DEBUG=0
```

va `docker compose exec php php bin/console cache:warmup`. So'rovlar ~2×
tezlashadi. Kod o'zgartirsangiz `cache:clear` kerak.
