<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum NotificationStatus: string
{
    case PROCESS_PENDING = 'process-pending';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
}
