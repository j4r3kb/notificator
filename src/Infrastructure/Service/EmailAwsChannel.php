<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Enum\Channel;
use App\Domain\Service\NotificationChannelInterface;

class EmailAwsChannel implements NotificationChannelInterface
{
    public static function type(): Channel
    {
        return Channel::EMAIL;
    }

    public static function priority(): int
    {
        return 3;
    }

    public function isAvailable(): bool
    {
        // Circuit Breaker here like https://github.com/ackintosh/ganesha
        return true;
    }

    public function send(string $address, string $content): void
    {
        // AWS Email API Client here (f.e. Guzzle)
    }
}
