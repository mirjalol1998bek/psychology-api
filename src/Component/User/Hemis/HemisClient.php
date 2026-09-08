<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HemisClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly HemisConfig $config,
    ) {
    }

    public function buildAuthorizationUrl(string $state): string
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

        return $this->config->getAuthorizeUrl() . '?' . http_build_query($query);
    }

    public function fetchAccessToken(string $code): string
    {
        $response = $this->httpClient->request('POST', $this->config->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            'body' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->config->getClientId(),
                'client_secret' => $this->config->getClientSecret(),
                'redirect_uri' => $this->config->getRedirectUri(),
                'code' => $code,
            ],
        ]);

        $data = $response->toArray(false);
        $token = $this->stringOrNull($data, 'access_token');

        if ($token === null) {
            throw new HemisAuthException('HEMIS access_token qaytmadi.');
        }

        return $token;
    }

    public function fetchProfile(string $accessToken): HemisProfile
    {
        $response = $this->httpClient->request('GET', $this->config->getUserinfoUrl(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ],
        ]);

        return $this->mapProfile($this->unwrap($response->toArray(false)));
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
    private function mapProfile(array $data): HemisProfile
    {
        $id = $this->stringOrNull($data, 'id') ?? $this->stringOrNull($data, 'uuid');
        $login = $this->stringOrNull($data, 'login');

        if ($id === null || $login === null) {
            throw new HemisAuthException('HEMIS profili to\'liq emas (id/login yo\'q).');
        }

        return new HemisProfile(
            $id,
            $login,
            $this->stringOrNull($data, 'name') ?? $login,
            $this->stringOrNull($data, 'email'),
            $this->readType($data),
            $this->stringOrNull($data, 'picture'),
            $this->stringOrNull($data, 'phone'),
            $this->readNestedName($data, 'group') ?? $this->stringOrNull($data, 'group_name'),
            $this->readNestedName($data, 'faculty') ?? $this->readNestedName($data, 'department'),
        );
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
