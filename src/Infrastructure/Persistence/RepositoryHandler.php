<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Message\UserAlreadyExistsInChatException;
use App\Domain\Message\UserNotInChatException;
use App\Domain\User\UserNotFoundException;
use PDO;

/**
 * Trait RepositoryHandler, used to handle common repository error detection.
 */
trait RepositoryHandler
{
    /**
     * @var PDO
     */
    private PDO $connection;

    /**
     * @throws ChatNotFoundException
     */
    private function checkChatExists(int $chatId): void
    {
        // Check if a chat exists by selecting all columns from the chats table where the id is equal to the chatId
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
        // Check if a user exists by selecting all columns from the users table where the id is equal to the userId
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
        // Check if a user already exists in a chat, if so, throw an exception
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
        // Check if a user exists in a chat, if not, throw an exception
        $memberStatements = $this->connection->prepare(
            'SELECT * FROM chat_members WHERE chat_id = :chat_id AND user_id = :user_id'
        );
        $memberStatements->execute(['chat_id' => $chatId, 'user_id' => $userId]);
        if (!$memberStatements->fetch(PDO::FETCH_ASSOC)) {
            throw new UserNotInChatException();
        }
    }
}
