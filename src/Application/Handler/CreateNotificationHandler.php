<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\CreateNotificationCommand;
use App\Domain\Entity\Notification;
use App\Domain\Repository\NotificationRepository;

class CreateNotificationHandler implements CommandHandlerInterface
{

    public function __construct(
        private NotificationRepository $notificationRepository
    ) {
    }

    public function __invoke(CreateNotificationCommand $createNotificationCommand): void
    {
        $notification = Notification::create(
            $createNotificationCommand->getContent(),
            $createNotificationCommand->getLanguage()
        );

        $createNotificationCommand->setCreatedId($notification->id());

        $this->notificationRepository->save($notification);
    }
}
