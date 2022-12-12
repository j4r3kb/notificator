<?php

declare(strict_types=1);

namespace App\Application\Command;


class SendNotificationCommand
{
    public function __construct(
        private string $notificationId
    ) {
    }

    public function notificationId(): string
    {
        return $this->notificationId;
    }
}
