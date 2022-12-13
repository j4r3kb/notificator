<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Command\SendNotificationCommand;
use App\Application\Handler\SendNotificationHandler;
use App\Domain\Entity\Notification;
use App\Domain\Enum\LanguageCode;
use App\Domain\Repository\NotificationRepository;
use App\Domain\Repository\RecipientRepository;
use App\Domain\Service\NotificationSenderInterface;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SendNotificationHandlerTest extends KernelTestCase
{
    private ?NotificationRepository $notificationRepository = null;

    private ?RecipientRepository $recipientRepository = null;

    private ?NotificationSenderInterface $notificationSender = null;

    private ?LoggerInterface $logger = null;

    public function testLogsErrorWhenNotificationIsNotFound(): void
    {
        $command = new SendNotificationCommand('123');

        $this->logger->expects($this->once())->method('error');
        $this->notificationSender->expects($this->never())->method('send');

        $this->handler->__invoke($command);
    }

    public function testLogsInfoWhenNotificationIsAlreadyBeingProcessedOrProcessed(): void
    {
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);
        $notification->processingStart(new DateTime());
        $this->notificationRepository->save($notification);

        $command = new SendNotificationCommand($notification->id());

        $this->logger->expects($this->once())->method('info');
        $this->notificationSender->expects($this->never())->method('send');

        $this->handler->__invoke($command);
    }

    public function testNotificationIsPassedToSenderServiceWhenProcessIsPending(): void
    {
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);
        $this->notificationRepository->save($notification);

        $command = new SendNotificationCommand($notification->id());

        $this->notificationSender->expects($this->once())->method('send');

        $this->handler->__invoke($command);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = $this->getContainer()->get(NotificationRepository::class);
        $this->recipientRepository = $this->getContainer()->get(RecipientRepository::class);
        $this->notificationSender = $this->createMock(NotificationSenderInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->handler = new SendNotificationHandler(
            $this->notificationRepository,
            $this->recipientRepository,
            $this->notificationSender,
            $this->logger
        );
    }
}
