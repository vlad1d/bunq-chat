<?php

declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Domain\Chat\ChatRepository;
use App\Infrastructure\Persistence\User\SQLiteUserRepository;
use App\Infrastructure\Persistence\Chat\SQLiteChatRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Create PDO instance with SQLite connection
    $pdo = new PDO('sqlite:' . __DIR__ . '/../var/chat.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Here we map our interface to its implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => function () use ($pdo) {
            return new SQLiteUserRepository($pdo);
        },

        ChatRepository::class => function () use ($pdo) {
            return new SQLiteChatRepository($pdo);
        },
    ]);
};
