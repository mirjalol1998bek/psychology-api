<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use App\Component\Core\ParameterGetter;
use App\Enum\HemisPortal;

/**
 * `state` = `nonce.issuedAt.portal.signature` — callback qaysi portal
 * (xodim/talaba) orqali kelganini shundan biladi; redirect_uri ikkalasida bir.
 */
final class HemisStateSigner
{
    private const LIFETIME_SECONDS = 1800;

    public function __construct(private readonly ParameterGetter $parameterGetter)
    {
    }

    public function issue(HemisPortal $portal): string
    {
        $payload = bin2hex(random_bytes(8)) . '.' . time() . '.' . $portal->value;

        return $payload . '.' . $this->sign($payload);
    }

    /**
     * Imzo va muddat to'g'ri bo'lsa — kirish boshlangan portal, aks holda null.
     */
    public function verify(string $state): ?HemisPortal
    {
        $parts = explode('.', $state);

        if (count($parts) !== 4) {
            return null;
        }

        [$nonce, $issuedAt, $portal, $signature] = $parts;

        if (hash_equals($this->sign($nonce . '.' . $issuedAt . '.' . $portal), $signature) === false) {
            return null;
        }

        return $this->isFresh((int) $issuedAt) ? HemisPortal::tryFrom($portal) : null;
    }

    private function isFresh(int $issuedAt): bool
    {
        return time() - $issuedAt <= self::LIFETIME_SECONDS;
    }

    private function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->parameterGetter->getString('kernel.secret'));
    }
}
