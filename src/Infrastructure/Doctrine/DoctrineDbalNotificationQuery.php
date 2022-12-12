<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Application\Query\NotificationQueryInterface;
use App\Application\Query\View\NotificationStatusView;
use Doctrine\DBAL\Connection;

class DoctrineDbalNotificationQuery implements NotificationQueryInterface
{

    public function __construct(
        private Connection $connection
    ) {
    }

    public function notificationStatus(string $notificationId): ?NotificationStatusView
    {
        $result = $this->connection->createQueryBuilder()
            ->select('n.status, n.send_fail_count, n.send_success_count')
            ->from('notification', 'n')
            ->where('n.id = :notificationId')
            ->setMaxResults(1)
            ->setParameter('notificationId', $notificationId)
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        return $result ? NotificationStatusView::fromArray(current($result)) : null;
    }
}
