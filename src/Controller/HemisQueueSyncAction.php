<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Organization\Hemis\Message\NightlyHemisSyncMessage;
use App\Controller\Base\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Barcha import qilingan guruhlarning talabalarini HEMIS'dan qayta yuklashni
 * navbatga qo'yadi (fon rejimida, birma-bir). Web so'rovni bloklamaydi.
 */
#[IsGranted('ROLE_ADMIN')]
class HemisQueueSyncAction extends AbstractController
{
    #[Route('/api/admin/hemis/students', name: 'hemis_queue_sync', methods: ['POST'])]
    public function __invoke(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new NightlyHemisSyncMessage());

        return $this->responseEmpty(Response::HTTP_ACCEPTED);
    }
}
