<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\LanguageCode;
use App\Domain\Enum\NotificationStatus;
use DateTime;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

class Notification
{
    private NotificationStatus $status;

    private ?DateTimeInterface $processingStartedAt = null;

    private ?DateTimeInterface $processedAt = null;

    private int $sendSuccessCount = 0;

    private int $sendFailCount = 0;

    public function __construct(
        private string $id,
        private string $content,
        private LanguageCode $language,
        private DateTimeInterface $createdAt
    ) {
        $this->status = NotificationStatus::PROCESS_PENDING;
    }

    public static function create(string $content, LanguageCode $language): self
    {
        return new self(
            Uuid::v4()->toRfc4122(),
            $content,
            $language,
            new DateTime()
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function language(): LanguageCode
    {
        return $this->language;
    }

    public function processingStart(DateTimeInterface $dateTime): void
    {
        $this->processingStartedAt = $dateTime;
        $this->status = NotificationStatus::PROCESSING;
    }

    public function processingEnd(DateTimeInterface $dateTime): void
    {
        $this->processedAt = $dateTime;
        $this->status = NotificationStatus::PROCESSED;
    }

    public function isInStatus(NotificationStatus $status): bool
    {
        return $this->status === $status;
    }

    public function status(): NotificationStatus
    {
        return $this->status;
    }

    public function sendSuccess(): void
    {
        ++$this->sendSuccessCount;
    }

    public function sendFail(): void
    {
        ++$this->sendFailCount;
    }

    public function sendSuccessCount(): int
    {
        return $this->sendSuccessCount;
    }

    public function sendFailCount(): int
    {
        return $this->sendFailCount;
    }
}
