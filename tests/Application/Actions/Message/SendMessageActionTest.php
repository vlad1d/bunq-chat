<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Message;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Message\MessageRepository;
use App\Domain\Message\UserNotInChatException;
use App\Domain\User\UserNotFoundException;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class SendMessageActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $messageRepositoryProphecy = $this->prophesize(MessageRepository::class);
        $messageRepositoryProphecy
            ->sendMessage(1, 1, 'hi bunq!')
            ->shouldBeCalledOnce();
        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/messages/1/users/1')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(['content' => 'hi bunq!']);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, ['message' => 'Message sent successfully']);
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
            ->sendMessage(1, 1, 'hi bunq!')
            ->willThrow(new ChatNotFoundException())
            ->shouldBeCalledOnce();
        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/messages/1/users/1')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(['content' => 'hi bunq!']);
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
            ->sendMessage(1, 1, 'hi bunq!')
            ->willThrow(new UserNotFoundException())
            ->shouldBeCalledOnce();
        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/messages/1/users/1')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(['content' => 'hi bunq!']);
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
            ->sendMessage(1, 1, 'hi bunq!')
            ->willThrow(new UserNotInChatException())
            ->shouldBeCalledOnce();
        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/messages/1/users/1')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(['content' => 'hi bunq!']);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user is not joined in the chat group.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsContentRequiredException()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
        $messageRepositoryProphecy = $this->prophesize(MessageRepository::class);
        $messageRepositoryProphecy
            ->sendMessage(1, 1, '')
            ->shouldNotBeCalled();

        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());
        $request = $this->createRequest('POST', '/messages/1/users/1', ['content' => '']);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(400, ['message' => 'Content is required']);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }
}
