<?php

declare(strict_types=1);

namespace App\UserInterface\Api\Controller;

use App\Application\Command\CreateNotificationCommand;
use App\Application\Command\SendNotificationCommand;
use App\Application\Query\NotificationQueryInterface;
use App\Domain\Enum\LanguageCode;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationController
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private NotificationQueryInterface $notificationQuery
    ) {
    }

    public function send(Request $request): JsonResponse
    {
        // I'm doing this for simplicity, in production API one could use ApiPlatform or something else, more flexible
        $parameters = $request->request->all();
        $content = $parameters['content'] ?? null;
        $languageCode = LanguageCode::tryFrom((string) $parameters['language'] ?? null);
        if (empty($content) || empty($languageCode)) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        try {
            $createCommand = new CreateNotificationCommand($content, $languageCode);
            $this->commandBus->dispatch($createCommand);
            $notificationId = $createCommand->getCreatedId();
            $sendCommand = new SendNotificationCommand($notificationId);
            $this->commandBus->dispatch($sendCommand);
        } catch (Exception $exception) {
            return new JsonResponse(['message' => 'Error occurred.', Response::HTTP_INTERNAL_SERVER_ERROR]);
        }

        return new JsonResponse(
            ['notificationId' => $notificationId],
            Response::HTTP_CREATED
        );
    }

    public function status(string $notificationId): JsonResponse
    {
        // https://matthiasnoback.nl/2019/06/you-may-not-need-a-query-bus/
        $notificationView = $this->notificationQuery->notificationStatus($notificationId);
        if ($notificationView === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($notificationView);
    }
}
