<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Hemis\HemisAuthException;
use App\Component\User\Hemis\HemisClient;
use App\Component\User\Hemis\HemisConfig;
use App\Component\User\Hemis\HemisStateSigner;
use App\Controller\Base\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class HemisRedirectAction extends AbstractController
{
    #[Route('/api/auth/hemis', name: 'hemis_redirect', methods: ['GET'])]
    public function __invoke(
        HemisConfig $hemisConfig,
        HemisClient $hemisClient,
        HemisStateSigner $hemisStateSigner,
    ): RedirectResponse {
        if ($hemisConfig->isConfigured() === false) {
            throw new HemisAuthException('HEMIS OAuth sozlanmagan (HEMIS_CLIENT_ID/SECRET).');
        }

        return new RedirectResponse(
            $hemisClient->buildAuthorizationUrl($hemisStateSigner->issue())
        );
    }
}
