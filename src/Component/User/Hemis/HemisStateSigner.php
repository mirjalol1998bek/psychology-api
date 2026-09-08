<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use App\Component\Core\ParameterGetter;

final class HemisStateSigner
{
    private const LIFETIME_SECONDS = 1800;

    public function __construct(private readonly ParameterGetter $parameterGetter)
    {
    }

    public function issue(): string
    {
        $payload = bin2hex(random_bytes(8)) . '.' . time();

        return $payload . '.' . $this->sign($payload);
    }

    public function isValid(string $state): bool
    {
        $parts = explode('.', $state);

        if (count($parts) !== 3) {
            return false;
        }

        $payload = $parts[0] . '.' . $parts[1];

        if (hash_equals($this->sign($payload), $parts[2]) === false) {
            return false;
        }

        return $this->isFresh((int) $parts[1]);
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
