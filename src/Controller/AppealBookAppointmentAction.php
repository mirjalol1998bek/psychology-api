<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Appeal\AppealAppointmentBooker;
use App\Component\Appointment\Dtos\AppointmentBookDto;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Repository\AppealRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Murojaatda so'ralgan "qabulga yozilish"ni tasdiqlab, haqiqiy
 * AppointmentSlot yaratadi (2 soatlik, band). Kirish uchun
 * docs/appeal.md — "Qabulga yozilish oqimi".
 */
#[IsGranted('ROLE_PSYCHOLOGIST')]
class AppealBookAppointmentAction extends AbstractController
{
    #[Route('/api/appeals/{id}/book', name: 'appeal_book_appointment', methods: ['POST'])]
    public function __invoke(int $id, Request $request, AppealRepository $appealRepository, AppealAppointmentBooker $booker): Response
    {
        $appeal = $appealRepository->find($id);

        if ($appeal === null) {
            $this->throwNotFoundException('Murojaat topilmadi');
        }

        /** @var AppointmentBookDto $dto */
        $dto = $this->getDtoFromRequest($request, AppointmentBookDto::class, ResponseFormat::JSON);
        $date = trim((string) $dto->getDate());
        $startTime = trim((string) $dto->getStartTime());

        if ($date === '' || $startTime === '') {
            throw new BadRequestHttpException('Sana va vaqt kerak.');
        }

        $booker->book($appeal, $this->getUser(), $date, $startTime);

        return $this->responseEmpty(Response::HTTP_OK);
    }
}
