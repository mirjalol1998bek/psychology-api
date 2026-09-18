<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Assessment\Seed\CatalogSeeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ask:seed:assessments',
    description: 'Tayyor metodikalarni (temperament uz/ru, psixogeometrik, IPM-20, OKM-20, EHS-20) bazaga yuklaydi',
)]
class AskSeedAssessmentsCommand extends Command
{
    public function __construct(private readonly CatalogSeeder $catalogSeeder)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $created = $this->catalogSeeder->seed();

        if ($created === []) {
            $io->info('Barcha metodikalar allaqachon mavjud — hech narsa qo\'shilmadi.');

            return Command::SUCCESS;
        }

        $io->success('Qo\'shilgan metodikalar: ' . implode(', ', $created));

        return Command::SUCCESS;
    }
}
