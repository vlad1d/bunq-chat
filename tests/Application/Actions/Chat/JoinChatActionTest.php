<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Chat;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Chat\ChatRepository;
use App\Domain\Message\UserAlreadyExistsInChatException;
use App\Domain\User\UserNotFoundException;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class JoinChatActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
        $chatRepositoryProphecy = $this->prophesize(ChatRepository::class);
        $chatRepositoryProphecy
            ->joinMember(1, 1)
            ->shouldBeCalledOnce();
        $container->set(ChatRepository::class, $chatRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/chats/1/users/1');
        $response = $app->handle($request);
        $payload = (string)$response->getBody();
        $expectedPayload = new ActionPayload(200, ['message' => 'User joined chat successfully.']);
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
        $chatRepositoryProphecy = $this->prophesize(ChatRepository::class);
        $chatRepositoryProphecy
            ->joinMember(1, 1)
            ->willThrow(new ChatNotFoundException())
            ->shouldBeCalledOnce();
        $container->set(ChatRepository::class, $chatRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/chats/1/users/1');
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
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
        $chatRepositoryProphecy = $this->prophesize(ChatRepository::class);
        $chatRepositoryProphecy
            ->joinMember(1, 1)
            ->willThrow(new UserNotFoundException())
            ->shouldBeCalledOnce();
        $container->set(ChatRepository::class, $chatRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/chats/1/users/1');
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsUserAlreadyExistsInChatException()
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
            ->joinMember(1, 1)
            ->willThrow(new UserAlreadyExistsInChatException())
            ->shouldBeCalledOnce();
        $container->set(ChatRepository::class, $chatRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/chats/1/users/1');
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedError = new ActionError(
            ActionError::RESOURCE_NOT_FOUND,
            'The user is already in the chat group.'
        );
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }
}
