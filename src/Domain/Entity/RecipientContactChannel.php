<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\Channel;
use Symfony\Component\Uid\Uuid;

class RecipientContactChannel
{
    private ?Recipient $recipient = null;

    public function __construct(
        private string $id,
        private Channel $channel,
        private string $address
    ) {
    }

    public static function create(Channel $channel, string $address): self
    {
        return new self(
            Uuid::v4()->toRfc4122(),
            $channel,
            $address
        );
    }

    public function address(): string
    {
        return $this->address;
    }

    public function channel(): Channel
    {
        return $this->channel;
    }

    public function setRecipient(Recipient $recipient): void
    {
        $this->recipient = $recipient;
    }
}
