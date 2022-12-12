<?php

declare(strict_types=1);

namespace App\Tests\Application\UserInterface\Api\Controller;

use App\Domain\Entity\Notification;
use App\Domain\Enum\LanguageCode;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Repository\NotificationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?NotificationRepository $notificationRepository = null;

    public function testReturnsHttpCreatedForValidPostRequest(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/notification',
            [
                'content' => 'Test Content',
                'language' => 'en-gb',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseFormatSame('json');
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertStringContainsString('notificationId', $content);
    }

    public function testReturnsBadRequestForInvalidPostRequest(): void
    {
        $crawler = $this->client->request(
            Request::METHOD_POST,
            '/notification',
            [
                'content' => '',
                'language' => '',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseFormatSame('json');
    }

    public function testReturnsNotFoundForInvalidNotificationIdGetRequest(): void
    {
        $this->client->request(Request::METHOD_GET, '/notification/random-id');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testReturnsOkWithNotificationStatusForGetRequest(): void
    {
        $notification = Notification::create('Test Content', LanguageCode::EN_GB);
        $notification->processingStart($processingStart = (new DateTime())->modify('-10 minutes'));
        $notification->sendSuccess();
        $notification->sendFail();
        $notification->sendSuccess();
        $notification->sendFail();
        $notification->processingEnd($processingEnd = new DateTime());
        $this->notificationRepository->save($notification);

        $this->client->request(Request::METHOD_GET, '/notification/' . $notification->id());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('json');
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertJsonStringEqualsJsonString(
            json_encode(
                [
                    'status' => NotificationStatus::PROCESSED->value,
                    'sendFailCount' => 2,
                    'sendSuccessCount' => 2,
                ]
            ),
            $content
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createClient();
        $this->notificationRepository = $this->getContainer()->get(NotificationRepository::class);
    }
}
