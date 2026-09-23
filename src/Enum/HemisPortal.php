<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * HEMIS'da xodim va talaba alohida portaldan kiradi (`hemis.*` / `student.*`),
 * lekin OAuth klienti (client_id/secret) va redirect_uri bitta.
 */
enum HemisPortal: string
{
    case Employee = 'employee';
    case Student = 'student';
}
