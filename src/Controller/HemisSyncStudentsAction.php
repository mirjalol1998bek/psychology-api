<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Repository\StudyGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class HemisSyncStudentsAction extends AbstractController
{
    #[Route('/api/admin/hemis/groups/{id}/students', name: 'hemis_sync_students', methods: ['POST'])]
    public function __invoke(int $id, StudyGroupRepository $studyGroupRepository, HemisOrganizationSync $sync): Response
    {
        $group = $studyGroupRepository->find($id);

        if ($group === null) {
            $this->throwNotFoundException('Guruh topilmadi');
        }

        if ($group->getExternalId() === null) {
            throw new BadRequestHttpException('Bu guruh HEMIS bilan bog\'lanmagan (externalId yo\'q).');
        }

        return $this->response($sync->syncGroupStudents($group), Response::HTTP_OK, ResponseFormat::JSON);
    }
}
