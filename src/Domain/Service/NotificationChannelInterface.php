<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Enum\Channel;

interface NotificationChannelInterface
{
    public static function type(): Channel;

    public static function priority(): int;

    public function isAvailable(): bool;

    public function send(string $address, string $content): void;
}
