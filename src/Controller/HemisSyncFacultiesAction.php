<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')]
class HemisSyncFacultiesAction extends AbstractController
{
    #[Route('/api/admin/hemis/faculties', name: 'hemis_sync_faculties', methods: ['POST'])]
    public function __invoke(HemisOrganizationSync $sync): Response
    {
        return $this->response($sync->syncFaculties(), Response::HTTP_OK, ResponseFormat::JSON);
    }
}
