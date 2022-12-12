<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Domain\Enum\Channel;
use App\Domain\Service\NotificationChannelInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AlwaysAvailableFailingEmailChannel implements NotificationChannelInterface
{
    public static function type(): Channel
    {
    return Channel::EMAIL;
    }

    public static function priority(): int
    {
        return 0;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function send(string $address, string $content): void
    {
        throw new Exception('Send error occurred.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
