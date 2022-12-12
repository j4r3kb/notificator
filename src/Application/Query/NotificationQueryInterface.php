<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\Query\View\NotificationStatusView;

interface NotificationQueryInterface
{
    public function notificationStatus(string $notificationId): ?NotificationStatusView;
}
