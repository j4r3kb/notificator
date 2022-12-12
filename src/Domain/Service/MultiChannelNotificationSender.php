<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Notification;
use App\Domain\Entity\Recipient;
use App\Domain\Repository\NotificationRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

class MultiChannelNotificationSender implements NotificationSenderInterface
{
    /**
     * @var NotificationChannelInterface[]
     */
    private array $channelPool = [];

    public function __construct(
        private NotificationRepository $notificationRepository,
        private TranslatorInterface $translator,
        private LoggerInterface $logger
    ) {
    }

    public function addNotificationChannel(NotificationChannelInterface $notificationChannel): void
    {
        $this->channelPool[] = $notificationChannel;
        usort(
            $this->channelPool,
            fn(NotificationChannelInterface $a, NotificationChannelInterface $b) => $b::priority() <=> $a::priority())
        ;
    }

    public function send(Notification $notification, array $recipientList): void
    {
        $notification->processingStart(new DateTime());

        if ($recipientList === []) {
            $this->logger->warning('No recipients found for notification {id}.', ['id' => $notification->id()]);
            $notification->processingEnd(new DateTime());
            $this->notificationRepository->save($notification);

            return;
        }

        $this->notificationRepository->save($notification);

        foreach ($recipientList as $recipient) {
            $channel = $this->pickChannelFor($recipient);
            if ($channel === null) {
                $this->logger->error(
                    'Could not match any channel for recipient {recipientId} and notification {notificationId}.',
                    [
                        'recipientId' => $recipient->id(),
                        'notificationId' => $notification->id(),
                    ]
                );
                $notification->sendFail();
                continue;
            }

            try {
                $translatedContent = $this->translator->translate(
                    $notification->content(),
                    $notification->language(),
                    $recipient->preferredLanguage()
                );
            } catch (Exception $exception) {
                $this->logger->error(
                    'Translation error for notification {id} from language {fromLanguage} to language {toLanguage}.'
                    . ' Exception {code}, {message}.',
                    [
                        'id' => $notification->id(),
                        'fromLanguage' => $notification->language()->value,
                        'toLanguage' => $recipient->preferredLanguage()->value,
                        'code' => $exception->getCode(),
                        'message' => $exception->getMessage(),
                    ]
                );
                $notification->sendFail();
                continue;
            }

            try {
                $channel->send($recipient->addressForChannel($channel::type()), $translatedContent);
                $this->logger->info(
                    'Sent notification {notificationId} to recipient {recipientId} via {channel}.',
                    [
                        'notificationId' => $notification->id(),
                        'recipientId' => $recipient->id(),
                        'channel' => $channel::class,
                    ]
                );
                $notification->sendSuccess();
            } catch (Exception $exception) {
                $this->logger->error(
                    'Failed to send notification {notificationId} to recipient {recipientId} via {channel}.'
                    . ' Exception {code}, {message}.',
                    [
                        'notificationId' => $notification->id(),
                        'recipientId' => $recipient->id(),
                        'channel' => $channel::class,
                        'code' => $exception->getCode(),
                        'message' => $exception->getMessage(),
                    ]
                );
                $notification->sendFail();
            }
        }

        $notification->processingEnd(new DateTime());
        $this->notificationRepository->save($notification);
    }

    private function pickChannelFor(Recipient $recipient): ?NotificationChannelInterface
    {
        foreach ($this->channelPool as $channel) {
            if ($recipient->canReceiveVia($channel::type()) && $channel->isAvailable()) {
                return $channel;
            }
        }

        return null;
    }
}
