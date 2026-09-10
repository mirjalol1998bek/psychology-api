<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Component\Organization\Hemis\Message\NightlyHemisSyncMessage;
use App\Repository\FacultyRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'ask:hemis:sync',
    description: 'HEMIS REST API\'dan fakultet + hozirgi guruh + talabalarni ko\'chirish',
)]
class AskHemisSyncCommand extends Command
{
    public function __construct(
        private readonly HemisOrganizationSync $sync,
        private readonly FacultyRepository $facultyRepository,
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('queue', null, InputOption::VALUE_NONE, 'Darhol bajarmasdan navbatga qo\'yish (tunlik sinxron kabi)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('queue') === true) {
            $this->bus->dispatch(new NightlyHemisSyncMessage());
            $io->success('Navbatga qo\'yildi. Worker birma-bir bajaradi (php bin/console messenger:consume async).');

            return Command::SUCCESS;
        }

        $faculties = $this->sync->syncFaculties();
        $io->success(sprintf('Fakultetlar: %d yangi, %d yangilandi', $faculties->created, $faculties->updated));

        $total = 0;

        foreach ($this->facultyRepository->findLinkedToHemis() as $faculty) {
            $counts = $this->sync->syncFacultyStudents($faculty);
            $total += $counts->total();
            $io->writeln(sprintf('  %s — %d talaba', (string) $faculty->getName(), $counts->total()));
        }

        $io->success(sprintf('Talabalar jami: %d', $total));

        return Command::SUCCESS;
    }
}
