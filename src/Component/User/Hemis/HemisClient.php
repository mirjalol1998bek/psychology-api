<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use App\Enum\HemisPortal;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HemisClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly HemisConfig $config,
    ) {
    }

    public function buildAuthorizationUrl(string $state, HemisPortal $portal): string
    {
        $query = [
            'response_type' => 'code',
            'client_id' => $this->config->getClientId(),
            'redirect_uri' => $this->config->getRedirectUri(),
            'state' => $state,
        ];

        if ($this->config->getScope() !== '') {
            $query['scope'] = $this->config->getScope();
        }

        return $this->config->getAuthorizeUrl($portal) . '?' . http_build_query($query);
    }

    public function fetchAccessToken(string $code, HemisPortal $portal): string
    {
        $response = $this->httpClient->request('POST', $this->config->getTokenUrl($portal), $this->tlsOptions() + [
            'headers' => ['Accept' => 'application/json'],
            'body' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->config->getClientId(),
                'client_secret' => $this->config->getClientSecret(),
                'redirect_uri' => $this->config->getRedirectUri(),
                'code' => $code,
            ],
        ]);

        $raw = $response->getContent(false);
        $data = $this->decode($raw, 'access-token');
        $token = $this->stringOrNull($data, 'access_token') ?? $this->stringOrNull($this->unwrap($data), 'access_token');

        if ($token === null) {
            throw new HemisAuthException(
                'HEMIS access_token qaytmadi (HTTP ' . $response->getStatusCode() . '): ' . mb_substr($raw, 0, 300),
            );
        }

        return $token;
    }

    public function fetchProfile(string $accessToken, HemisPortal $portal): HemisProfile
    {
        $response = $this->httpClient->request('GET', $this->config->getUserinfoUrl($portal), $this->tlsOptions() + [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ],
        ]);

        $raw = $response->getContent(false);

        return $this->mapProfile($this->unwrap($this->decode($raw, 'userinfo')), $portal);
    }

    /**
     * HEMIS ba'zan TLS zanjiridagi oraliq sertifikatni yubormaydi — o'z
     * CA fayl bilan tekshiramiz (config/certs/hemis-ca-chain.pem).
     *
     * @return array<string, mixed>
     */
    private function tlsOptions(): array
    {
        $caFile = $this->config->getCaFile();

        return $caFile === null ? [] : ['cafile' => $caFile];
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string $raw, string $step): array
    {
        $data = json_decode($raw, true);

        if (is_array($data) === false) {
            throw new HemisAuthException('HEMIS ' . $step . ' javobi JSON emas: ' . mb_substr($raw, 0, 300));
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function unwrap(array $payload): array
    {
        $data = $payload['data'] ?? null;

        if (is_array($data)) {
            return $data;
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function mapProfile(array $data, HemisPortal $portal): HemisProfile
    {
        $login = $this->readLogin($data);
        $id = $this->stringOrNull($data, 'id') ?? $this->stringOrNull($data, 'uuid') ?? $login;

        if ($id === null || $login === null) {
            throw new HemisAuthException(sprintf(
                'HEMIS profili to\'liq emas (id/login yo\'q). Kelgan maydonlar: %s',
                implode(', ', array_keys($data)) ?: '—',
            ));
        }

        return new HemisProfile(
            $id,
            $login,
            $this->readFullName($data) ?? $login,
            $this->stringOrNull($data, 'email'),
            // Talaba portalidan kirgan — ta'rifi bo'yicha talaba; `type` bo'lmasa ham.
            $portal === HemisPortal::Student ? 'student' : $this->readType($data),
            $this->stringOrNull($data, 'picture') ?? $this->stringOrNull($data, 'image'),
            $this->stringOrNull($data, 'phone'),
            $this->readNestedName($data, 'group') ?? $this->stringOrNull($data, 'group_name'),
            $this->readNestedName($data, 'faculty') ?? $this->readNestedName($data, 'department'),
        );
    }

    /**
     * Xodim profilida `login`, talabanikida esa Talaba ID `student_id_number`
     * bo'lib keladi — HEMIS sinxroni ham aynan shu qiymatlarni kalit qiladi.
     *
     * @param array<string, mixed> $data
     */
    private function readLogin(array $data): ?string
    {
        return $this->stringOrNull($data, 'login')
            ?? $this->stringOrNull($data, 'student_id_number')
            ?? $this->stringOrNull($data, 'employee_id_number');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function readFullName(array $data): ?string
    {
        return $this->stringOrNull($data, 'name')
            ?? $this->stringOrNull($data, 'full_name')
            ?? $this->stringOrNull($data, 'short_name');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function readType(array $data): string
    {
        $type = $data['type'] ?? null;

        if (is_array($type)) {
            return mb_strtolower((string) ($type['name'] ?? $type['code'] ?? 'student'));
        }

        if (is_string($type) && $type !== '') {
            return mb_strtolower($type);
        }

        return isset($data['employee_list']) ? 'employee' : 'student';
    }

    /**
     * @param array<string, mixed> $data
     */
    private function readNestedName(array $data, string $key): ?string
    {
        $nested = $data[$key] ?? null;

        if (is_array($nested)) {
            return $this->stringOrNull($nested, 'name');
        }

        if (is_string($nested) && $nested !== '') {
            return $nested;
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function stringOrNull(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if (is_string($value) && $value !== '') {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return null;
    }
}
