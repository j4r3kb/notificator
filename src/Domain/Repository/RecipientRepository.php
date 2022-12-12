<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Recipient;

interface RecipientRepository
{
    public function save(Recipient $recipient): void;

    public function findOne(string $id): ?Recipient;

    public function findAll();
}
