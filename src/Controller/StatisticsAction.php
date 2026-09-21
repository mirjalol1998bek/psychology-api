<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Assessment\Report\StatisticsReporter;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PSYCHOLOGIST')]
class StatisticsAction extends AbstractController
{
    #[Route('/api/admin/statistics', name: 'admin_statistics', methods: ['GET'])]
    public function __invoke(StatisticsReporter $statisticsReporter): Response
    {
        return $this->response($statisticsReporter->overview(), Response::HTTP_OK, ResponseFormat::JSON);
    }
}
