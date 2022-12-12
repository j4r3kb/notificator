<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Notification;

interface NotificationRepository
{
    public function save(Notification $notification): void;

    public function findOne(string $id): ?Notification;
}
