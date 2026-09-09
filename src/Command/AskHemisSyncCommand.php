<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Entity\Faculty;
use App\Repository\FacultyRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ask:hemis:sync',
    description: 'HEMIS REST API\'dan fakultetlar + guruhlarni (ixtiyoriy: talabalarni) ko\'chirish',
)]
class AskHemisSyncCommand extends Command
{
    public function __construct(
        private readonly HemisOrganizationSync $sync,
        private readonly FacultyRepository $facultyRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('students', null, InputOption::VALUE_NONE, 'Har guruh talabalarini ham ko\'chirish (sekin)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $faculties = $this->sync->syncFaculties();
        $io->success(sprintf('Fakultetlar: %d yangi, %d yangilandi', $faculties->created, $faculties->updated));

        $groupsTotal = 0;

        foreach ($this->facultyRepository->findAll() as $faculty) {
            if ($faculty->getExternalId() === null) {
                continue;
            }

            $groups = $this->sync->syncAllGroups($faculty);
            $groupsTotal += $groups->total();
            $io->writeln(sprintf('  %s — %d guruh', (string) $faculty->getName(), $groups->total()));

            if ($input->getOption('students') === true) {
                $this->syncFacultyStudents($faculty, $io);
            }
        }

        $io->success(sprintf('Guruhlar jami: %d', $groupsTotal));

        return Command::SUCCESS;
    }

    private function syncFacultyStudents(Faculty $faculty, SymfonyStyle $io): void
    {
        foreach ($faculty->getGroups() as $group) {
            if ($group->getExternalId() === null) {
                continue;
            }

            $students = $this->sync->syncGroupStudents($group);
            $io->writeln(sprintf('    %s — %d talaba', (string) $group->getName(), $students->total()));
        }
    }
}
