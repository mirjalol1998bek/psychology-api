<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\User\UserManager;
use App\Enum\RoleEnum;
use App\Enum\UserStatusEnum;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ask:user:make-admin',
    description: 'Foydalanuvchiga (email yoki hemisId bo\'yicha) ROLE_ADMIN va active holat beradi',
)]
class AskUserMakeAdminCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserManager $userManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'Foydalanuvchi email yoki hemisId');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $identifier = (string) $input->getArgument('identifier');
        $user = $this->userRepository->findOneBy(['email' => $identifier])
            ?? $this->userRepository->findOneBy(['hemisId' => $identifier]);

        if ($user === null) {
            $io->error('Foydalanuvchi topilmadi: ' . $identifier);

            return Command::FAILURE;
        }

        $user->setRoles([RoleEnum::Admin->value]);
        $user->setStatus(UserStatusEnum::Active);
        $this->userManager->save($user, true);

        $io->success(($user->getFullName() ?? $user->getEmail()) . ' endi administrator.');

        return Command::SUCCESS;
    }
}
