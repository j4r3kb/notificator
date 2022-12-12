<?php

declare(strict_types=1);

namespace App\Application\Query\View;

use JsonSerializable;
use Webmozart\Assert\Assert;

class NotificationStatusView implements JsonSerializable
{
    public function __construct(
        private string $status,
        private int $failCount,
        private int $successCount
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'status' => $this->status,
            'sendFailCount' => $this->failCount,
            'sendSuccessCount' => $this->successCount,
        ];
    }
}
