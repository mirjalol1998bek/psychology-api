<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Assessment\AttemptStarter;
use App\Component\Assessment\Dto\StartAttemptInput;
use App\Controller\Base\AbstractController;
use App\Entity\Attempt;
use App\Repository\CategoryRepository;

class AttemptStartAction extends AbstractController
{
    public function __invoke(
        StartAttemptInput $data,
        CategoryRepository $categoryRepository,
        AttemptStarter $attemptStarter,
    ): Attempt {
        $this->validate($data);
        $category = $categoryRepository->find($data->categoryId);

        if ($category === null) {
            $this->throwNotFoundException('Category is not found');
        }

        return $attemptStarter->start($this->getUser(), $category);
    }
}
