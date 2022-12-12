<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Notification;
use App\Domain\Enum\LanguageCode;
use App\Domain\Enum\NotificationStatus;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testExceptionIsThrownWhenEmptyStringProvidedAsContent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Notification::create('', LanguageCode::EN_GB);
    }

    public function testStatusChangesWhenProcessDatesAreSet(): void
    {
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);
        $this->assertTrue($notification->isInStatus(NotificationStatus::PROCESS_PENDING));

        $notification->processingStart(new DateTime());
        $this->assertTrue($notification->isInStatus(NotificationStatus::PROCESSING));

        $notification->processingEnd(new DateTime());
        $this->assertTrue($notification->isInStatus(NotificationStatus::PROCESSED));
    }

    public function testFailAndSuccessCountReturnsProperValue(): void
    {
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);

        $this->assertEquals(0, $notification->sendFailCount());
        $this->assertEquals(0, $notification->sendSuccessCount());

        $notification->sendFail();
        $notification->sendSuccess();
        $notification->sendFail();
        $notification->sendSuccess();
        $notification->sendSuccess();

        $this->assertEquals(2, $notification->sendFailCount());
        $this->assertEquals(3, $notification->sendSuccessCount());
    }
}
