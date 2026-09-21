<?php

declare(strict_types=1);

namespace App\Component\ObservationCard;

use App\Component\Notification\NotificationDispatcher;
use App\Entity\ObservationCard;
use App\Entity\User;
use App\Enum\ObservationRiskLevel;
use App\Repository\ObservationCardRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ObservationCardCreator
{
    public function __construct(
        private readonly ObservationCardRepository $observationCardRepository,
        private readonly ObservationCardManager $observationCardManager,
        private readonly NotificationDispatcher $notificationDispatcher,
    ) {
    }

    public function create(ObservationCard $card, User $tutor): ObservationCard
    {
        $this->assertOwnStudent($card, $tutor);
        $this->assertNotFilledYet($card);
        $scores = $this->validatedScores($card->getScores());

        $card->setTutor($tutor);
        $this->applyScoring($card, $scores);
        $this->observationCardManager->save($card, true);

        if ($card->getAlertTriggered()) {
            $this->notificationDispatcher->notifyStaffOnObservationAlert($card);
        }

        return $card;
    }

    private function assertOwnStudent(ObservationCard $card, User $tutor): void
    {
        $group = $card->getStudent()?->getStudyGroup();

        if ($group === null || $group->getTutor()?->getId() !== $tutor->getId()) {
            throw new AccessDeniedHttpException('Bu talaba sizning guruhingizda emas.');
        }
    }

    private function assertNotFilledYet(ObservationCard $card): void
    {
        $existing = $this->observationCardRepository->findOneBy(['student' => $card->getStudent()]);

        if ($existing !== null) {
            throw new BadRequestHttpException('Bu talaba uchun kuzatuv kartasi allaqachon to\'ldirilgan.');
        }
    }

    /**
     * @param array<string, int> $scores
     * @return array<string, int>
     */
    private function validatedScores(array $scores): array
    {
        foreach (array_keys(ObservationCardData::INDICATORS) as $key) {
            $this->assertValidScore($scores, $key);
        }

        return $scores;
    }

    /**
     * @param array<string, int> $scores
     */
    private function assertValidScore(array $scores, string $key): void
    {
        $value = $scores[$key] ?? null;

        if (is_int($value) === false || $value < ObservationCardData::MIN_SCORE || $value > ObservationCardData::MAX_SCORE) {
            throw new BadRequestHttpException('"' . $key . '" ko\'rsatkichi 0-3 oralig\'ida bo\'lishi kerak.');
        }
    }

    /**
     * @param array<string, int> $scores
     */
    private function applyScoring(ObservationCard $card, array $scores): void
    {
        $total = array_sum($scores);

        $card->setScores($scores);
        $card->setTotalScore($total);
        $card->setRiskLevel($this->matchRiskLevel($total));
        $card->setAlertTriggered(($scores[ObservationCardData::ALERT_KEY] ?? 0) === ObservationCardData::ALERT_SCORE);
    }

    private function matchRiskLevel(int $total): ObservationRiskLevel
    {
        foreach (ObservationCardData::BANDS as $band) {
            if ($total >= $band['min'] && $total <= $band['max']) {
                return ObservationRiskLevel::from($band['key']);
            }
        }

        return ObservationRiskLevel::Systemic;
    }
}
