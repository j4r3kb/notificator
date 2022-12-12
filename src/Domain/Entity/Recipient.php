<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\Channel;
use App\Domain\Enum\LanguageCode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Uid\Uuid;

class Recipient
{
    private array $contactChannelMap = [];

    private Collection $contactChannelList;

    public function __construct(
        private string $id,
        private LanguageCode $preferredLanguage
    ) {
        $this->contactChannelList = new ArrayCollection();
    }

    public static function create(LanguageCode $preferredLanguage): self
    {
        return new self(
            Uuid::v4()->toRfc4122(),
            $preferredLanguage,
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function preferredLanguage(): LanguageCode
    {
        return $this->preferredLanguage;
    }

    public function addContactChannel(RecipientContactChannel $contactChannel): void
    {
        $contactChannel->setRecipient($this);
        $this->contactChannelList->set($contactChannel->channel()->value, $contactChannel);
    }

    public function canReceiveVia(Channel $channel): bool
    {
        return $this->contactChannelList->containsKey($channel->value);
    }

    public function addressForChannel(Channel $channel): ?string
    {
        $contactChannel = $this->contactChannelList->get($channel->value);

        return $contactChannel?->address();
    }
}
