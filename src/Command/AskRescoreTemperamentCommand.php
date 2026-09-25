<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Assessment\AttemptManager;
use App\Component\Assessment\AttemptSubmitter;
use App\Enum\InstrumentType;
use App\Repository\AssessmentResultRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Teng ballda birinchi turni tanlagan eski qoida bilan saqlangan temperament
 * natijalarini saqlangan javoblardan qayta hisoblaydi (teng turlar — aralash).
 * Standart holatda faqat ko'rsatadi; bazaga `--apply` bilan yoziladi.
 */
#[AsCommand(
    name: 'ask:results:rescore-temperament',
    description: 'Temperament natijalarini qayta hisoblaydi (teng ballda aralash tur)',
)]
class AskRescoreTemperamentCommand extends Command
{
    public function __construct(
        private readonly AssessmentResultRepository $resultRepository,
        private readonly AttemptSubmitter $attemptSubmitter,
        private readonly AttemptManager $attemptManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('apply', null, InputOption::VALUE_NONE, 'O\'zgarishlarni bazaga yozish');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $apply = (bool) $input->getOption('apply');
        $results = $this->resultRepository->findByInstrumentTypes([
            InstrumentType::TemperamentStatements,
            InstrumentType::TemperamentChoice,
        ]);
        $changes = [];

        foreach ($results as $result) {
            $attempt = $result->getAttempt();

            if ($attempt === null) {
                continue;
            }

            $scored = $this->attemptSubmitter->score($attempt);

            if ($scored->resultKey === $result->getResultKey()) {
                continue;
            }

            $changes[] = [$attempt->getStudent()?->getFullName() ?? '—', $result->getResultKey(), $scored->resultKey];

            if ($apply) {
                $this->attemptSubmitter->applyScore($attempt, $result, $scored);
                $this->attemptManager->save($attempt, true);
            }
        }

        $io->table(['Talaba', 'Oldin', 'Endi'], $changes);
        $io->success(sprintf(
            '%d ta temperament natijasi tekshirildi, %d tasi %s.',
            count($results),
            count($changes),
            $apply ? 'yangilandi' : 'o\'zgaradi (yozish uchun --apply)',
        ));

        return Command::SUCCESS;
    }
}
