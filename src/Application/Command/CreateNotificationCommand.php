<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Enum\LanguageCode;

class CreateNotificationCommand implements CreationCommandInterface
{
    private ?string $id = null;

    public function __construct(
        private string $content,
        private LanguageCode $language
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getLanguage(): LanguageCode
    {
        return $this->language;
    }

    public function setCreatedId(string $id): void
    {
        $this->id = $id;
    }

    public function getCreatedId(): ?string
    {
        return $this->id;
    }
}
