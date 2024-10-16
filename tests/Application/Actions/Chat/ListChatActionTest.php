<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Chat;

use App\Application\Actions\ActionPayload;
use App\Domain\Chat\ChatRepository;
use App\Domain\Chat\Chat;
use DI\Container;
use Tests\TestCase;

class ListChatActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $chat = new Chat(1);

        $chatRepositoryProphecy = $this->prophesize(ChatRepository::class);
        $chatRepositoryProphecy
            ->findAll()
            ->willReturn([$chat])
            ->shouldBeCalledOnce();

        $container->set(ChatRepository::class, $chatRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/chats');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, [$chat]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}