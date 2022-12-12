<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Domain\Entity\Recipient;
use App\Domain\Repository\RecipientRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineRecipientRepository extends ServiceEntityRepository implements RecipientRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipient::class);
    }

    public function save(Recipient $recipient): void
    {
        $em = $this->getEntityManager();
        $em->persist($recipient);
        $em->flush();
    }

    public function findOne(string $id): ?Recipient
    {
        return $this->find($id);
    }
}
