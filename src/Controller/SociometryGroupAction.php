<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Assessment\Report\SociometryReporter;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Repository\StudyGroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Sotsiometriya — guruh darajasidagi tahlil, faqat psixolog/admin
 * (`sociometry.md`). Talaba va tyutor bu endpointga kira olmaydi.
 */
#[IsGranted('ROLE_PSYCHOLOGIST')]
class SociometryGroupAction extends AbstractController
{
    #[Route('/api/admin/sociometry', name: 'admin_sociometry', methods: ['GET'])]
    public function __invoke(
        Request $request,
        SociometryReporter $reporter,
        StudyGroupRepository $studyGroupRepository,
    ): Response {
        $groupId = $this->requireIntParam($request, 'studyGroup');
        $group = $studyGroupRepository->find($groupId);

        if ($group === null) {
            $this->throwNotFoundException('Guruh topilmadi');
        }

        return $this->response($reporter->forGroup($group), Response::HTTP_OK, ResponseFormat::JSON);
    }

    private function requireIntParam(Request $request, string $name): int
    {
        $value = $request->query->get($name);

        if (is_numeric($value) === false) {
            throw new BadRequestHttpException(sprintf('"%s" parametri (butun son) kerak.', $name));
        }

        return (int) $value;
    }
}
