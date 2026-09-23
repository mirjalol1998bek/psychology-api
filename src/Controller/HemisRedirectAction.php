<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Hemis\HemisAuthException;
use App\Component\User\Hemis\HemisClient;
use App\Component\User\Hemis\HemisConfig;
use App\Component\User\Hemis\HemisStateSigner;
use App\Controller\Base\AbstractController;
use App\Enum\HemisPortal;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HemisRedirectAction extends AbstractController
{
    #[Route('/api/auth/hemis', name: 'hemis_redirect', methods: ['GET'])]
    public function __invoke(
        Request $request,
        HemisConfig $hemisConfig,
        HemisClient $hemisClient,
        HemisStateSigner $hemisStateSigner,
    ): RedirectResponse {
        if ($hemisConfig->isConfigured() === false) {
            throw new HemisAuthException('HEMIS OAuth sozlanmagan (HEMIS_CLIENT_ID/SECRET).');
        }

        $portal = HemisPortal::tryFrom((string) $request->query->get('portal', '')) ?? HemisPortal::Employee;

        return new RedirectResponse(
            $hemisClient->buildAuthorizationUrl($hemisStateSigner->issue($portal), $portal)
        );
    }
}
