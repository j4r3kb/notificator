<?php

declare(strict_types=1);

namespace App\Application\Query\View;

use App\Domain\Entity\Notification;
use JsonSerializable;

class NotificationStatusView implements JsonSerializable
{
    public function __construct(
        private string $status,
        private int $failCount,
        private int $successCount
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['status'],
            $data['send_fail_count'],
            $data['send_success_count']
        );
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
