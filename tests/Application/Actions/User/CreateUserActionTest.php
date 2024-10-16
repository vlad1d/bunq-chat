<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\User\UserAlreadyExistsException;
use App\Domain\User\UserRepository;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class CreateUserActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->create(1)
            ->shouldBeCalledOnce();
        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/users/1');
        $response = $app->handle($request);
        $payload = (string)$response->getBody();
        $expectedPayload = new ActionPayload(200, ['message' => 'User created successfully.']);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsUserAlreadyExistsException()
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
        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->create(1)
            ->willThrow(new UserAlreadyExistsException())
            ->shouldBeCalledOnce();
        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());
        $request = $this->createRequest('POST', '/users/1');
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedError = new ActionError(
            ActionError::RESOURCE_NOT_FOUND,
            'The user you requested is already in the scope.'
        );
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);
        $this->assertEquals($serializedPayload, $payload);
    }
}
