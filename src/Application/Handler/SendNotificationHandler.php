<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\SendNotificationCommand;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Repository\NotificationRepository;
use App\Domain\Repository\RecipientRepository;
use App\Domain\Service\NotificationSenderInterface;
use Psr\Log\LoggerInterface;

class SendNotificationHandler implements CommandHandlerInterface
{

    public function __construct(
        private NotificationRepository $notificationRepository,
        private RecipientRepository $recipientRepository,
        private NotificationSenderInterface $notificationSender,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(SendNotificationCommand $sendNotificationCommand): void
    {
        $notificationId = $sendNotificationCommand->notificationId();
        $notification = $this->notificationRepository->findOne($notificationId);
        if ($notification === null) {
            $this->logger->error('Notification {id} not found.', ['id' => $notificationId]);

            return;
        }

        if ($notification->isInStatus(NotificationStatus::PROCESS_PENDING) === false) {
            $this->logger->info('Notification {id} is being/already processed.', ['id' => $notificationId]);

            return;
        }

        $recipientList = $this->recipientRepository->findAll();
        $this->notificationSender->send($notification, $recipientList);
        $this->notificationRepository->save($notification);
    }
}
