<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Repository\FacultyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Fakultetning HEMIS'dagi guruhlari — ro'yxat (saqlanmaydi). Admin kerakligini
 * tanlab `HemisImportGroupAction` bilan import qiladi.
 */
#[IsGranted('ROLE_ADMIN')]
class HemisFacultyGroupsAction extends AbstractController
{
    #[Route('/api/admin/hemis/faculties/{id}/groups', name: 'hemis_faculty_groups', methods: ['GET'])]
    public function __invoke(int $id, FacultyRepository $facultyRepository, HemisOrganizationSync $sync): Response
    {
        $faculty = $facultyRepository->find($id);

        if ($faculty === null) {
            $this->throwNotFoundException('Fakultet topilmadi');
        }

        if ($faculty->getExternalId() === null) {
            throw new BadRequestHttpException('Bu fakultet HEMIS bilan bog\'lanmagan.');
        }

        return $this->response($sync->listGroups($faculty), Response::HTTP_OK, ResponseFormat::JSON);
    }
}
