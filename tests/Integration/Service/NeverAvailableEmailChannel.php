<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Domain\Enum\Channel;
use App\Domain\Service\NotificationChannelInterface;

class NeverAvailableEmailChannel implements NotificationChannelInterface
{
    public static function type(): Channel
    {
    return Channel::EMAIL;
    }

    public static function priority(): int
    {
        return 1;
    }

    public function isAvailable(): bool
    {
        return false;
    }

    public function send(string $address, string $content): void
    {
    }
}
