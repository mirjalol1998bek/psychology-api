<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Hemis\HemisAuthException;
use App\Component\User\Hemis\HemisConfig;
use App\Component\User\Hemis\HemisLoginService;
use App\Component\User\Hemis\HemisStateSigner;
use App\Component\User\TokensCreator;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HemisCallbackAction extends AbstractController
{
    #[Route('/api/auth/callback/hemis', name: 'hemis_callback', methods: ['GET'])]
    public function __invoke(
        Request $request,
        HemisConfig $hemisConfig,
        HemisStateSigner $hemisStateSigner,
        HemisLoginService $hemisLoginService,
        TokensCreator $tokensCreator,
    ): RedirectResponse {
        $this->assertNoError($request);
        $code = $this->requireCode($request);
        $this->assertValidState($hemisStateSigner, (string) $request->query->get('state', ''));

        $user = $hemisLoginService->loginByCode($code);

        return new RedirectResponse($this->buildReturnUrl($hemisConfig, $tokensCreator, $user));
    }

    private function assertNoError(Request $request): void
    {
        $error = $request->query->get('error');

        if ($error !== null) {
            throw new HemisAuthException('HEMIS xatosi: ' . (string) $error);
        }
    }

    private function requireCode(Request $request): string
    {
        $code = $request->query->get('code');

        if (is_string($code) === false || $code === '') {
            throw new HemisAuthException('HEMIS "code" parametri yo\'q.');
        }

        return $code;
    }

    private function assertValidState(HemisStateSigner $signer, string $state): void
    {
        if ($signer->isValid($state) === false) {
            throw new HemisAuthException('HEMIS "state" yaroqsiz yoki muddati o\'tgan.');
        }
    }

    /**
     * @throws JWTEncodeFailureException
     */
    private function buildReturnUrl(HemisConfig $config, TokensCreator $tokensCreator, User $user): string
    {
        $tokens = $tokensCreator->create($user);
        $fragment = http_build_query([
            'access' => $tokens->getAccessToken(),
            'refresh' => $tokens->getRefreshToken(),
        ]);

        return $config->getFrontendReturnUrl() . '#' . $fragment;
    }
}
