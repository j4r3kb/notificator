<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Domain\Enum\Channel;
use App\Domain\Service\NotificationChannelInterface;

class NeverAvailableSmsChannel implements NotificationChannelInterface
{
    public static function type(): Channel
    {
        return Channel::SMS;
    }

    public static function priority(): int
    {
        return 3;
    }

    public function isAvailable(): bool
    {
        return false;
    }

    public function send(string $address, string $content): void
    {
    }
}
