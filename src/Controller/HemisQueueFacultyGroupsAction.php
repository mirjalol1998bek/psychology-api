<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Organization\Hemis\Message\SyncFacultyGroupsMessage;
use App\Controller\Base\AbstractController;
use App\Repository\FacultyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Fakultetning HEMIS'dagi barcha faol guruhlarini (va ular talabalarini)
 * import qilishni navbatga qo'yadi. Fon rejimida, birma-bir, rate limiter
 * bilan — web so'rovni bloklamaydi.
 */
#[IsGranted('ROLE_ADMIN')]
class HemisQueueFacultyGroupsAction extends AbstractController
{
    #[Route('/api/admin/hemis/faculties/{id}/groups', name: 'hemis_queue_faculty_groups', methods: ['POST'])]
    public function __invoke(int $id, FacultyRepository $facultyRepository, MessageBusInterface $bus): Response
    {
        $faculty = $facultyRepository->find($id);

        if ($faculty === null) {
            $this->throwNotFoundException('Fakultet topilmadi');
        }

        if ($faculty->getExternalId() === null) {
            throw new BadRequestHttpException('Bu fakultet HEMIS bilan bog\'lanmagan.');
        }

        $bus->dispatch(new SyncFacultyGroupsMessage($id));

        return $this->responseEmpty(Response::HTTP_ACCEPTED);
    }
}
