<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Repository\FacultyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Bitta HEMIS guruhini bazaga import qiladi va o'sha zahoti hozirgi
 * talabalarini ham ko'chiradi.
 */
#[IsGranted('ROLE_ADMIN')]
class HemisImportGroupAction extends AbstractController
{
    #[Route(
        '/api/admin/hemis/faculties/{id}/groups/{groupExternalId}',
        name: 'hemis_import_group',
        requirements: ['groupExternalId' => '\d+'],
        methods: ['POST'],
    )]
    public function __invoke(
        int $id,
        string $groupExternalId,
        FacultyRepository $facultyRepository,
        HemisOrganizationSync $sync,
    ): Response {
        $faculty = $facultyRepository->find($id);

        if ($faculty === null) {
            $this->throwNotFoundException('Fakultet topilmadi');
        }

        if ($faculty->getExternalId() === null) {
            throw new BadRequestHttpException('Bu fakultet HEMIS bilan bog\'lanmagan.');
        }

        $group = $sync->importGroup($faculty, $groupExternalId);
        $students = $sync->syncGroupStudents($group);

        return $this->response(
            $students,
            Response::HTTP_OK,
            ResponseFormat::JSON,
        );
    }
}
