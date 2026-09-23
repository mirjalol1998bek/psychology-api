<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use App\Component\Core\ParameterGetter;
use App\Enum\HemisPortal;

final class HemisConfig
{
    public function __construct(private readonly ParameterGetter $parameterGetter)
    {
    }

    public function getClientId(): string
    {
        return $this->parameterGetter->getString('hemis.client_id');
    }

    public function getClientSecret(): string
    {
        return $this->parameterGetter->getString('hemis.client_secret');
    }

    public function getApiToken(): string
    {
        return $this->parameterGetter->getString('hemis.api_token');
    }

    public function getApiBaseUrl(): string
    {
        return rtrim($this->parameterGetter->getString('hemis.api_base_url'), '/');
    }

    public function getAuthorizeUrl(HemisPortal $portal): string
    {
        return $this->forPortal($this->parameterGetter->getString('hemis.auth_url'), $portal);
    }

    public function getTokenUrl(HemisPortal $portal): string
    {
        return $this->forPortal($this->parameterGetter->getString('hemis.token_url'), $portal);
    }

    public function getUserinfoUrl(HemisPortal $portal): string
    {
        $url = $this->forPortal($this->parameterGetter->getString('hemis.userinfo_url'), $portal);

        return $portal === HemisPortal::Student ? $this->withStudentFields($url) : $url;
    }

    public function getRedirectUri(): string
    {
        return $this->parameterGetter->getString('hemis.redirect_uri');
    }

    public function getScope(): string
    {
        return $this->parameterGetter->getString('hemis.scope');
    }

    public function getCaFile(): ?string
    {
        $path = $this->parameterGetter->getString('hemis.cafile');

        return is_file($path) ? $path : null;
    }

    public function getFrontendReturnUrl(): string
    {
        return rtrim($this->parameterGetter->getString('frontend.url'), '/')
            . $this->parameterGetter->getString('frontend.hemis_return_path');
    }

    public function isConfigured(): bool
    {
        return $this->getClientId() !== '' && $this->getClientSecret() !== '';
    }

    /**
     * `fields` HEMIS javobini faqat so'ralgan maydonlar bilan cheklaydi, talaba
     * profilida esa nomlar boshqa (`student_id_number`, `full_name`, `image`).
     * Ularni qo'shamiz — HEMIS modelda yo'q maydonlarni e'tiborsiz qoldiradi.
     */
    private function withStudentFields(string $url): string
    {
        return preg_replace(
            '/([?&]fields=[^&]*)/',
            '$1,student_id_number,full_name,short_name,image',
            $url,
            1,
        ) ?? $url;
    }

    /**
     * Talaba portali xodimnikidek yo'l va `fields`ni ishlatadi, faqat hosti
     * boshqa (`HEMIS_STUDENT_URL`) — shuning uchun faqat origin almashtiriladi.
     */
    private function forPortal(string $url, HemisPortal $portal): string
    {
        if ($portal === HemisPortal::Employee) {
            return $url;
        }

        $parts = parse_url($url);
        $origin = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '')
            . (isset($parts['port']) ? ':' . $parts['port'] : '');

        return rtrim($this->parameterGetter->getString('hemis.student_url'), '/') . substr($url, strlen($origin));
    }
}
