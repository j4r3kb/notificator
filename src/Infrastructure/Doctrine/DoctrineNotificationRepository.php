<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Domain\Entity\Notification;
use App\Domain\Repository\NotificationRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineNotificationRepository extends ServiceEntityRepository implements NotificationRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $notification): void
    {
        $em = $this->getEntityManager();
        $em->persist($notification);
        $em->flush();
    }

    public function findOne(string $id): ?Notification
    {
        return $this->find($id);
    }
}
