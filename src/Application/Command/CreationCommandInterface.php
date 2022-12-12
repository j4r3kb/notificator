<?php

declare(strict_types=1);

namespace App\Application\Command;

interface CreationCommandInterface
{
    public function setCreatedId(string $id): void;

    public function getCreatedId(): ?string;
}
