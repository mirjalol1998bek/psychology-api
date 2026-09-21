<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Assessment\Report\GroupResultReporter;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Repository\CategoryRepository;
use App\Repository\StudyGroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_PSYCHOLOGIST')]
class GroupResultsAction extends AbstractController
{
    #[Route('/api/admin/group_results', name: 'admin_group_results', methods: ['GET'])]
    public function __invoke(
        Request $request,
        GroupResultReporter $reporter,
        StudyGroupRepository $studyGroupRepository,
        CategoryRepository $categoryRepository,
    ): Response {
        $groupId = $this->requireIntParam($request, 'studyGroup');
        $categoryId = $this->requireIntParam($request, 'category');
        $this->assertFound($studyGroupRepository->find($groupId), 'Guruh topilmadi');
        $this->assertFound($categoryRepository->find($categoryId), 'Kategoriya topilmadi');

        return $this->response(
            $reporter->forGroupAndCategory($groupId, $categoryId),
            Response::HTTP_OK,
            ResponseFormat::JSON,
        );
    }

    private function requireIntParam(Request $request, string $name): int
    {
        $value = $request->query->get($name);

        if (is_numeric($value) === false) {
            throw new BadRequestHttpException(sprintf('"%s" parametri (butun son) kerak.', $name));
        }

        return (int) $value;
    }

    private function assertFound(?object $entity, string $message): void
    {
        if ($entity === null) {
            $this->throwNotFoundException($message);
        }
    }
}
