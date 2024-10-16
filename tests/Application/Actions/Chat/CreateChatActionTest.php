<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Chat;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Chat\ChatAlreadyExistsException;
use App\Domain\Chat\ChatRepository;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class CreateChatActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
        $chatRepositoryProphecy = $this->prophesize(ChatRepository::class);
        $chatRepositoryProphecy
            ->create(1)
            ->shouldBeCalledOnce();
        $container->set(ChatRepository::class, $chatRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/chats/1');
        $response = $app->handle($request);
        $payload = (string)$response->getBody();
        $expectedPayload = new ActionPayload(200, ['message' => 'Chat created successfully.']);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsChatAlreadyExistsException()
    {
        $app = $this->getAppInstance();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->add($errorMiddleware);

        /** @var Container $container */
        $container = $app->getContainer();
        $chatRepositoryProphecy = $this->prophesize(ChatRepository::class);
        $chatRepositoryProphecy
            ->create(1)
            ->willThrow(new ChatAlreadyExistsException())
            ->shouldBeCalledOnce();
        $container->set(ChatRepository::class, $chatRepositoryProphecy->reveal());
        $request = $this->createRequest('POST', '/chats/1');
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedError = new ActionError(
            ActionError::RESOURCE_NOT_FOUND,
            'The chat you requested to create already exists.'
        );
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }
}