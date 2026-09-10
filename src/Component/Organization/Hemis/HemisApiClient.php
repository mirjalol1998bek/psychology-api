<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis;

use App\Component\Organization\Hemis\Dto\HemisFaculty;
use App\Component\Organization\Hemis\Dto\HemisGroup;
use App\Component\Organization\Hemis\Dto\HemisStudent;
use App\Component\User\Hemis\HemisAuthException;
use App\Component\User\Hemis\HemisConfig;
use App\Enum\StudyLanguage;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * HEMIS talaba REST API (student.uzswlu.uz/rest/v1) — token bilan.
 * Javob konverti: {"success":bool,"error":?string,"data":{"items":[],"pagination":{}}}.
 * Har so'rov `hemis_api` rate-limiter tokenini kutadi + 403'da sekin qayta urinadi.
 */
final class HemisApiClient
{
    private const PAGE_SIZE = 200;
    private const MAX_PAGES = 60;
    private const RETRY_ON_FORBIDDEN = 3;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly HemisConfig $config,
        private readonly RateLimiterFactoryInterface $hemisApiLimiter,
    ) {
    }

    /**
     * @return list<HemisFaculty>
     */
    public function fetchFaculties(): array
    {
        $faculties = [];

        foreach ($this->collect('/data/department-list', []) as $item) {
            if ($this->stringAt($item, 'structureType', 'code') !== '11') {
                continue;
            }

            $faculties[] = new HemisFaculty(
                (string) ($item['id'] ?? ''),
                (string) ($item['name'] ?? ''),
                (bool) ($item['active'] ?? true),
            );
        }

        return $faculties;
    }

    /**
     * @return list<HemisGroup>
     */
    public function fetchGroups(string $facultyExternalId): array
    {
        $groups = [];

        foreach ($this->collect('/data/group-list', ['_department' => $facultyExternalId]) as $item) {
            $groups[] = new HemisGroup(
                (string) ($item['id'] ?? ''),
                (string) ($item['name'] ?? ''),
                $this->stringAt($item, 'department', 'id'),
                $this->stringAt($item, 'department', 'name'),
                $this->mapLanguage($this->stringAt($item, 'educationLang', 'code')),
                (bool) ($item['active'] ?? true),
            );
        }

        return $groups;
    }

    /**
     * Bitta guruhning talabalari.
     *
     * @return list<HemisStudent>
     */
    public function fetchStudents(string $groupExternalId): array
    {
        return $this->mapStudents($this->collect('/data/student-list', ['_group' => $groupExternalId]));
    }

    /**
     * Fakultetning barcha hozirgi talabalari — har yozuvda guruhi ham bor.
     * Guruhlarni shundan yig'amiz (faqat talabasi bor guruhlar yaratiladi).
     *
     * @return list<HemisStudent>
     */
    public function fetchFacultyStudents(string $facultyExternalId): array
    {
        return $this->mapStudents($this->collect('/data/student-list', ['_department' => $facultyExternalId]));
    }

    /**
     * @param list<array<string, mixed>> $items
     * @return list<HemisStudent>
     */
    private function mapStudents(array $items): array
    {
        $students = [];

        foreach ($items as $item) {
            $students[] = new HemisStudent(
                (string) ($item['student_id_number'] ?? ''),
                (string) ($item['full_name'] ?? ''),
                $this->nullableString($item['image_full'] ?? null),
                $this->stringAt($item, 'group', 'id'),
                $this->stringAt($item, 'group', 'name'),
                $this->mapLanguage($this->stringAt($item, 'group', 'educationLang', 'code')),
                $this->stringAt($item, 'studentStatus', 'code') === '11',
            );
        }

        return $students;
    }

    /**
     * @param array<string, string> $query
     * @return list<array<string, mixed>>
     */
    private function collect(string $path, array $query): array
    {
        $items = [];
        $page = 1;

        do {
            $payload = $this->request($path, $query + ['page' => (string) $page, 'limit' => (string) self::PAGE_SIZE]);
            $pageItems = $payload['data']['items'] ?? [];

            foreach ($pageItems as $item) {
                if (is_array($item)) {
                    $items[] = $item;
                }
            }

            $pageCount = (int) ($payload['data']['pagination']['pageCount'] ?? 1);
            $page++;
        } while ($page <= $pageCount && $page <= self::MAX_PAGES && $pageItems !== []);

        return $items;
    }

    /**
     * @param array<string, string> $query
     * @return array<string, mixed>
     */
    private function request(string $path, array $query): array
    {
        $attempt = 0;
        $limiter = $this->hemisApiLimiter->create('global');

        do {
            $attempt++;
            $limiter->reserve(1)->wait();
            $response = $this->httpClient->request('GET', $this->config->getApiBaseUrl() . $path, $this->options($query));
            $status = $response->getStatusCode();
            $raw = $response->getContent(false);

            if ($status === 403 && $attempt <= self::RETRY_ON_FORBIDDEN) {
                usleep(1_500_000 * $attempt);

                continue;
            }

            return $this->decode($raw, $status);
        } while (true);
    }

    /**
     * @param array<string, string> $query
     * @return array<string, mixed>
     */
    private function options(array $query): array
    {
        $caFile = $this->config->getCaFile();
        $tls = $caFile === null ? [] : ['cafile' => $caFile];

        return $tls + [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getApiToken(),
                'Accept' => 'application/json',
            ],
            'query' => $query,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string $raw, int $status): array
    {
        $data = json_decode($raw, true);

        if (is_array($data) === false) {
            throw new HemisAuthException('HEMIS API javobi JSON emas (HTTP ' . $status . '): ' . mb_substr($raw, 0, 200));
        }

        if (($data['success'] ?? false) === false) {
            $message = is_string($data['error'] ?? null) ? $data['error'] : ('HTTP ' . $status);

            throw new HemisAuthException('HEMIS API xatosi: ' . $message);
        }

        return $data;
    }

    private function mapLanguage(?string $code): StudyLanguage
    {
        return $code === '12' ? StudyLanguage::Russian : StudyLanguage::Uzbek;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function stringAt(array $item, string ...$path): string
    {
        $cursor = $item;

        foreach ($path as $key) {
            if (is_array($cursor) === false || array_key_exists($key, $cursor) === false) {
                return '';
            }

            $cursor = $cursor[$key];
        }

        return is_scalar($cursor) ? (string) $cursor : '';
    }

    private function nullableString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
