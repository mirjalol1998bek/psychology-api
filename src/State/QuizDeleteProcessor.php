<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Quiz;
use App\Repository\AttemptRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * `Attempt.quiz` FK `onDelete` sozlanmagan (attempt har doim quizga ega
 * bo'lishi kerak) — talaba allaqachon boshlagan Quiz'ni o'chirishga
 * urinish xom `500` (FK constraint) berardi. Bu yerda oldindan tekshirib,
 * tushunarli `409` qaytaradi.
 *
 * @implements ProcessorInterface<Quiz, void>
 */
final class QuizDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: RemoveProcessor::class)]
        private readonly ProcessorInterface $removeProcessor,
        private readonly AttemptRepository $attemptRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($this->attemptRepository->count(['quiz' => $data]) > 0) {
            throw new ConflictHttpException('Bu testni talabalar allaqachon topshirgan — avval ularning urinishlarini o\'chiring.');
        }

        return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
    }
}
