<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use App\Component\Core\ParameterGetter;

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

    public function getAuthorizeUrl(): string
    {
        return $this->parameterGetter->getString('hemis.auth_url');
    }

    public function getTokenUrl(): string
    {
        return $this->parameterGetter->getString('hemis.token_url');
    }

    public function getUserinfoUrl(): string
    {
        return $this->parameterGetter->getString('hemis.userinfo_url');
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
}
