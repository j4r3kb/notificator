<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Domain\Enum\Channel;
use App\Domain\Service\NotificationChannelInterface;

class AlwaysAvailableSmsChannel implements NotificationChannelInterface
{
    public static function type(): Channel
    {
        return Channel::SMS;
    }

    public static function priority(): int
    {
        return 2;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function send(string $address, string $content): void
    {
    }
}
