#!/bin/bash
cron -f &

# var/ nomli volume bo'sh va root egaligida ishga tushadi — php-fpm (www-data)
# yoza olishi uchun kataloglarni yaratib, egalikni to'g'rilaymiz.
mkdir -p /var/www/html/var/cache /var/www/html/var/log
chown -R www-data:www-data /var/www/html/var || true

# Messenger worker'lari (HEMIS sinxroni fon rejimida) — supervisor conf
# faqat mount qilingan bo'lsa ishga tushadi.
if [ -f /etc/supervisor/conf.d/messenger-worker.conf ]; then
    service supervisor start
    supervisorctl reread
    supervisorctl update
    supervisorctl start messenger-consume:* || true
fi

docker-php-entrypoint php-fpm
