<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Chat\ChatAlreadyExistsException;
use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Message\UserAlreadyExistsInChatException;
use App\Domain\Message\UserNotInChatException;
use App\Domain\User\UserAlreadyExistsException;
use App\Domain\User\UserNotFoundException;
use PDO;

trait RepositoryHandler
{
    private PDO $connection;

    /**
     * @throws ChatNotFoundException
     */
    private function checkChatExists(int $chatId): void
    {
        $statement = $this->connection->prepare('SELECT * FROM chats WHERE id = :id');
        $statement->execute(['id' => $chatId]);
        if (!$statement->fetch(PDO::FETCH_ASSOC)) {
            throw new ChatNotFoundException();
        }
    }

    /**
     * @throws UserNotFoundException
     */
    private function checkUserExists(int $userId): void
    {
        $statement = $this->connection->prepare('SELECT * FROM users WHERE id = :id');
        $statement->execute(['id' => $userId]);
        if (!$statement->fetch(PDO::FETCH_ASSOC)) {
            throw new UserNotFoundException();
        }
    }

    /**
     * @throws UserAlreadyExistsInChatException
     */
    private function checkUserNotExistsChat(int $chatId, int $userId)
    {
        $memberStatements = $this->connection->prepare(
            'SELECT * FROM chat_members WHERE chat_id = :chat_id AND user_id = :user_id'
        );
        $memberStatements->execute(['chat_id' => $chatId, 'user_id' => $userId]);
        $memberExists = $memberStatements->fetch(PDO::FETCH_ASSOC);
        if ($memberExists) {
            throw new UserAlreadyExistsInChatException();
        }
    }

    /**
     * @throws UserNotInChatException
     */
    private function checkUserExistsChat(int $chatId, int $userId)
    {
        $memberStatements = $this->connection->prepare(
            'SELECT * FROM chat_members WHERE chat_id = :chat_id AND user_id = :user_id'
        );
        $memberStatements->execute(['chat_id' => $chatId, 'user_id' => $userId]);
        if (!$memberStatements->fetch(PDO::FETCH_ASSOC)) {
            throw new UserNotInChatException();
        }
    }
}
