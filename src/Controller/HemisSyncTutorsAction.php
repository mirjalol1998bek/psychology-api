<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class HemisSyncTutorsAction extends AbstractController
{
    #[Route('/api/admin/hemis/tutors', name: 'hemis_sync_tutors', methods: ['POST'])]
    public function __invoke(HemisOrganizationSync $sync): Response
    {
        return $this->response($sync->syncTutors(), Response::HTTP_OK, ResponseFormat::JSON);
    }
}
