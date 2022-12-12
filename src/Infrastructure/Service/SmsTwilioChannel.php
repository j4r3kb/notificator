<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Enum\Channel;
use App\Domain\Service\NotificationChannelInterface;

class SmsTwilioChannel implements NotificationChannelInterface
{
    public static function type(): Channel
    {
        return Channel::SMS;
    }

    public static function priority(): int
    {
        return 5;
    }

    public function isAvailable(): bool
    {
        // Circuit Breaker here like https://github.com/ackintosh/ganesha
        return false;
    }

    public function send(string $address, string $content): void
    {
        // Twilio API Client here (f.e. Guzzle)
    }
}
