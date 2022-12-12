<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Recipient;
use App\Domain\Entity\RecipientContactChannel;
use App\Domain\Enum\Channel;
use App\Domain\Enum\LanguageCode;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{
    public function testRecipientCanReceiveViaChannelItHasAddressFor(): void
    {
        $recipient = Recipient::create(LanguageCode::EN_GB);

        $this->assertFalse($recipient->canReceiveVia(Channel::EMAIL));

        $recipient->addContactChannel(RecipientContactChannel::create(Channel::EMAIL, 'test@example.com'));
        $recipient->addContactChannel(RecipientContactChannel::create(Channel::SMS, '555666777'));

        $this->assertTrue($recipient->canReceiveVia(Channel::EMAIL));
        $this->assertEquals('test@example.com', $recipient->addressForChannel(Channel::EMAIL));
        $this->assertTrue($recipient->canReceiveVia(Channel::SMS));
        $this->assertEquals('555666777', $recipient->addressForChannel(Channel::SMS));
    }
}
