<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Chat\ListChatsAction;
use App\Application\Actions\Chat\ViewChatAction;
use App\Application\Actions\Chat\CreateChatAction;
use App\Application\Actions\Chat\DeleteChatAction;
use App\Application\Actions\User\CreateUserAction;
use App\Application\Actions\User\DeleteUserAction;
use App\Application\Actions\Chat\JoinChatAction;
use App\Application\Actions\Chat\LeaveChatAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
        $group->post('/{id}', CreateUserAction::class);
    });

    $app->group('/chats', function (Group $group) {
        $group->get('', ListChatsAction::class);
        $group->get('/{id}', ViewChatAction::class);
        $group->post('/{id}', CreateChatAction::class);
        $group->delete('/{id}', DeleteChatAction::class);
        $group->post('/{chatId}/users/{userId}', JoinChatAction::class);
        $group->delete('/{chatId}/users/{userId}', LeaveChatAction::class);
    });
};
