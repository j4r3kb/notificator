<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Domain\Entity\Notification;
use App\Domain\Entity\Recipient;
use App\Domain\Entity\RecipientContactChannel;
use App\Domain\Enum\Channel;
use App\Domain\Enum\LanguageCode;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Repository\NotificationRepository;
use App\Domain\Service\MultiChannelNotificationSender;
use App\Infrastructure\Service\FakeTranslator;
use App\Infrastructure\Service\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MultiChannelNotificationSenderTest extends KernelTestCase
{
    private ?NotificationRepository $notificationRepository = null;
    private ?MultiChannelNotificationSender $multiChannelSender = null;

    public function testNothingIsSendWhenEmptyRecipientListGiven(): void
    {
        $recipientList = [];
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);
        $this->multiChannelSender->send($notification, $recipientList);

        $this->assertTrue($notification->isInStatus(NotificationStatus::PROCESSED));
        $this->assertEquals(0, $notification->sendFailCount());
        $this->assertEquals(0, $notification->sendSuccessCount());
    }

    public function testRecipientIsSkippedWhenCanNotReceiveByAnyChannel(): void
    {
        $recipient = Recipient::create(LanguageCode::EN_GB);
        $recipient->addContactChannel(RecipientContactChannel::create(Channel::PUSH, 'push-address'));

        $recipientList = [$recipient];
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);
        $this->multiChannelSender->send($notification, $recipientList);

        $this->assertTrue($notification->isInStatus(NotificationStatus::PROCESSED));
        $this->assertEquals(1, $notification->sendFailCount());
        $this->assertEquals(0, $notification->sendSuccessCount());
    }

    public function testRecipientIsSkippedWhenTranslationErrorOccurs(): void
    {
        $this->multiChannelSender = new MultiChannelNotificationSender(
            $this->notificationRepository,
            new FakeTranslator(true),
            new NullLogger()
        );

        $recipient = Recipient::create(LanguageCode::ES_ES);
        $recipient->addContactChannel(RecipientContactChannel::create(Channel::SMS, 'sms-address'));

        $recipientList = [$recipient];
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);
        $this->multiChannelSender->send($notification, $recipientList);

        $this->assertTrue($notification->isInStatus(NotificationStatus::PROCESSED));
        $this->assertEquals(1, $notification->sendFailCount());
        $this->assertEquals(0, $notification->sendSuccessCount());
    }

    public function testNextChannelIsUsedWhenRecipientCanNotReceiveViaTopPriorityOne(): void
    {
        $this->multiChannelSender->addNotificationChannel(new AlwaysAvailableFacebookChannel());

        $recipientOne = Recipient::create(LanguageCode::EN_GB);
        $recipientOne->addContactChannel(RecipientContactChannel::create(Channel::SMS, 'sms-address'));
        $recipientOne->addContactChannel(RecipientContactChannel::create(Channel::EMAIL, 'email-address'));
        $recipientTwo = Recipient::create(LanguageCode::DE);
        $recipientTwo->addContactChannel(RecipientContactChannel::create(Channel::EMAIL, 'email-address'));
        $recipientTwo->addContactChannel(RecipientContactChannel::create(Channel::PUSH, 'push-address'));

        $recipientList = [$recipientOne, $recipientTwo];
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);
        $this->multiChannelSender->send($notification, $recipientList);

        $this->assertTrue($notification->isInStatus(NotificationStatus::PROCESSED));
        $this->assertEquals(1, $notification->sendFailCount());
        $this->assertEquals(1, $notification->sendSuccessCount());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationRepository = $this->getContainer()->get(NotificationRepository::class);
        $this->multiChannelSender = new MultiChannelNotificationSender(
            $this->notificationRepository,
            new FakeTranslator(),
            new NullLogger()
        );
        $this->multiChannelSender->addNotificationChannel(new NeverAvailableSmsChannel());
        $this->multiChannelSender->addNotificationChannel(new AlwaysAvailableSmsChannel());
        $this->multiChannelSender->addNotificationChannel(new NeverAvailableEmailChannel());
        $this->multiChannelSender->addNotificationChannel(new AlwaysAvailableFailingEmailChannel());
    }
}
