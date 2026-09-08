<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Component\Passport\StudentPassportManager;
use App\Component\User\CurrentUser;
use App\Entity\StudentPassport;

/**
 * @implements ProcessorInterface<StudentPassport, StudentPassport>
 */
final class PassportPutProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly StudentPassportManager $passportManager,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): StudentPassport
    {
        $data->setStudent($this->currentUser->getUser());
        $this->passportManager->save($data, true);

        return $data;
    }
}
