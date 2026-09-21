<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * 10-metodika (Kuzatuv kartasi) jami balliga (0-45) mos xavf darajasi.
 * Bandlar `ObservationCardData::BANDS`da.
 */
enum ObservationRiskLevel: string
{
    case None = 'none';
    case Attention = 'attention';
    case Risk = 'risk';
    case Systemic = 'systemic';
}
