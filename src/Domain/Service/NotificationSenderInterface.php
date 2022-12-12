<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Notification;
use App\Domain\Entity\Recipient;

interface NotificationSenderInterface
{
    /**
     * @param Recipient[] $recipientList
     */
    public function send(Notification $notification, array $recipientList): void;
}
