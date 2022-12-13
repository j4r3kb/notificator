<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Command\CreateNotificationCommand;
use App\Application\Handler\CreateNotificationHandler;
use App\Domain\Enum\LanguageCode;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateNotificationHandlerTest extends KernelTestCase
{
    private ?NotificationRepository $notificationRepository = null;

    private ?CreateNotificationHandler $handler = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationRepository = $this->getContainer()->get(NotificationRepository::class);
        $this->handler = new CreateNotificationHandler($this->notificationRepository);
    }

    public function testCreatesNotificationAndSavesInRepository(): void
    {
        $command = new CreateNotificationCommand('Test Content', LanguageCode::EN_GB);
        $this->handler->__invoke($command);
        $notification = $this->notificationRepository->findOne($command->getCreatedId());

        $this->assertTrue($notification->isInStatus(NotificationStatus::PROCESS_PENDING));
        $this->assertEquals('Test Content', $notification->content());
        $this->assertEquals(LanguageCode::EN_GB, $notification->language());
    }
}
