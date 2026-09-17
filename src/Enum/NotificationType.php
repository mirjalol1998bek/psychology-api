<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationType: string
{
    case AppealNew = 'appeal_new';
    case AppealReply = 'appeal_reply';
    case AssignmentNew = 'assignment_new';
    case AppointmentReminder = 'appointment_reminder';
    case AppealAppointmentBooked = 'appeal_appointment_booked';
    case AccessRequest = 'access_request';
    case AccessApproved = 'access_approved';
}
