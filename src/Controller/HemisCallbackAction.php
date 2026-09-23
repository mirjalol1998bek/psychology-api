<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Hemis\HemisAuthException;
use App\Component\User\Hemis\HemisConfig;
use App\Component\User\Hemis\HemisLoginService;
use App\Component\User\Hemis\HemisStateSigner;
use App\Component\User\TokensCreator;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Enum\HemisPortal;
use App\Enum\UserStatusEnum;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class HemisCallbackAction extends AbstractController
{
    #[Route('/api/auth/callback/hemis', name: 'hemis_callback', methods: ['GET'])]
    public function __invoke(
        Request $request,
        HemisConfig $hemisConfig,
        HemisStateSigner $hemisStateSigner,
        HemisLoginService $hemisLoginService,
        TokensCreator $tokensCreator,
        UserManager $userManager,
        LoggerInterface $logger,
    ): RedirectResponse {
        try {
            $this->assertNoError($request);
            $code = $this->requireCode($request);
            $portal = $this->requirePortal($hemisStateSigner, (string) $request->query->get('state', ''));
            $user = $hemisLoginService->loginByCode($code, $portal);

            if ($user->getStatus() !== UserStatusEnum::Active) {
                return new RedirectResponse($this->buildStatusUrl($hemisConfig, $user->getStatus()));
            }

            $userManager->recordLogin($user);

            return new RedirectResponse($this->buildTokenUrl($hemisConfig, $tokensCreator, $user));
        } catch (Throwable $e) {
            $logger->error('HEMIS callback failed: ' . $e->getMessage(), ['exception' => $e]);

            return new RedirectResponse($this->buildErrorUrl($hemisConfig, $e->getMessage()));
        }
    }

    private function assertNoError(Request $request): void
    {
        $error = $request->query->get('error');

        if ($error !== null) {
            $description = (string) $request->query->get('error_description', '');

            throw new HemisAuthException(trim('HEMIS: ' . (string) $error . ' ' . $description));
        }
    }

    private function requireCode(Request $request): string
    {
        $code = $request->query->get('code');

        if (is_string($code) === false || $code === '') {
            throw new HemisAuthException('HEMIS "code" parametri qaytmadi.');
        }

        return $code;
    }

    private function requirePortal(HemisStateSigner $signer, string $state): HemisPortal
    {
        return $signer->verify($state)
            ?? throw new HemisAuthException('HEMIS "state" yaroqsiz yoki muddati o\'tgan. Qaytadan urinib ko\'ring.');
    }

    private function buildTokenUrl(HemisConfig $config, TokensCreator $tokensCreator, User $user): string
    {
        $tokens = $tokensCreator->create($user);
        $fragment = http_build_query([
            'access' => $tokens->getAccessToken(),
            'refresh' => $tokens->getRefreshToken(),
        ]);

        return $config->getFrontendReturnUrl() . '#' . $fragment;
    }

    private function buildStatusUrl(HemisConfig $config, UserStatusEnum $status): string
    {
        $key = $status === UserStatusEnum::Rejected ? 'rejected' : 'pending';

        return $config->getFrontendReturnUrl() . '#' . http_build_query([$key => '1']);
    }

    private function buildErrorUrl(HemisConfig $config, string $message): string
    {
        return $config->getFrontendReturnUrl() . '#' . http_build_query(['error' => $message]);
    }
}
