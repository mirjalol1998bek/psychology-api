<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Assessment\Seed\DemoSeeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ask:seed:demo',
    description: 'Demo hisoblar (admin/psixolog/talaba) + guruh + biriktirishlar + metodikalar',
)]
class AskSeedDemoCommand extends Command
{
    public function __construct(private readonly DemoSeeder $demoSeeder)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Demo hisoblar tayyor:');
        $io->listing($this->demoSeeder->seed());
        $io->note('Frontend: "Login va parol" orqali shu hisoblar bilan kiring.');

        return Command::SUCCESS;
    }
}
