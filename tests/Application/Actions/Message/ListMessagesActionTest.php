<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Message;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Message\Message;
use App\Domain\Message\MessageRepository;
use App\Domain\Message\UserNotInChatException;
use App\Domain\User\UserNotFoundException;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ListMessagesActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $messages = [
            new Message(1, 1, 1, 'Hello, World!'),
            new Message(2, 1, 1, 'Goodbye, World!')
        ];

        $messageRepositoryProphecy = $this->prophesize(MessageRepository::class);
        $messageRepositoryProphecy
            ->listMessages(1, 1)
            ->willReturn($messages)
            ->shouldBeCalledOnce();

        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());
        $request = $this->createRequest('GET', '/messages/1/users/1');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $messages);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsChatNotFoundException()
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
        $messageRepositoryProphecy = $this->prophesize(MessageRepository::class);
        $messageRepositoryProphecy
            ->listMessages(1, 1)
            ->willThrow(new ChatNotFoundException())
            ->shouldBeCalledOnce();
        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/messages/1/users/1');
        $response = $app->handle($request);
        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The chat you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsUserNotFoundException()
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
        $messageRepositoryProphecy = $this->prophesize(MessageRepository::class);
        $messageRepositoryProphecy
            ->listMessages(1, 1)
            ->willThrow(new UserNotFoundException())
            ->shouldBeCalledOnce();

        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());
        $request = $this->createRequest('GET', '/messages/1/users/1');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsUserNotInChatException()
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
        $messageRepositoryProphecy = $this->prophesize(MessageRepository::class);
        $messageRepositoryProphecy
            ->listMessages(1, 1)
            ->willThrow(new UserNotInChatException())
            ->shouldBeCalledOnce();

        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/messages/1/users/1');
        $response = $app->handle($request);
        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user is not joined in the chat group.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }
}
