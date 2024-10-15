<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Chat;

use App\Domain\Chat\Chat;
use App\Domain\Chat\ChatRepository;
use App\Domain\Chat\ChatAlreadyExistsException;
use App\Infrastructure\Persistence\RepositoryHandler;
use PDO;

class SQLiteChatRepository implements ChatRepository
{
    use RepositoryHandler;

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->initialiseChats();
    }

    /**
     * Initialise the chats groups table and the chat members table.
     */
    private function initialiseChats(): void
    {
        $this->connection->exec('CREATE TABLE IF NOT EXISTS chats (id INTEGER PRIMARY KEY)');
        $this->connection->exec('CREATE TABLE IF NOT EXISTS chat_members 
(chat_id INTEGER, user_id INTEGER, PRIMARY KEY (chat_id, user_id))');
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        // Select all chats and return them as an array of Chat objects
        $statements = $this->connection->query('SELECT * FROM chats');
        $chats = [];
        while ($row = $statements->fetch(PDO::FETCH_ASSOC)) {
            $chats[] = $this->findChatOfId($row['id']);
        }
        return $chats;
    }

    /**
     * {@inheritdoc}
     */
    public function findChatOfId(int $id): Chat
    {
        // Check if the chat exists
        $this->checkChatExists($id);

        // Select the chat and its members
        $statements = $this->connection->prepare('SELECT * FROM chats WHERE id = :id');
        $statements->execute(['id' => $id]);
        $row = $statements->fetch(PDO::FETCH_ASSOC);
        $membersStatements = $this->connection->prepare('SELECT user_id FROM chat_members WHERE chat_id = :chat_id');
        $membersStatements->execute(['chat_id' => $id]);
        $members = $membersStatements->fetchAll(PDO::FETCH_COLUMN);
        return new Chat($row['id'], $members);
    }

    /**
     * {@inheritdoc}
     */
    public function create(int $id): void
    {
        // Check if the chat already exists
        $statements = $this->connection->prepare('SELECT * FROM chats WHERE id = (:id)');
        $statements->execute(['id' => $id]);
        if ($statements->fetch(PDO::FETCH_ASSOC)) {
            throw new ChatAlreadyExistsException();
        }

        // Create the chat
        $statements = $this->connection->prepare('INSERT INTO chats (id) VALUES (:id)');
        $statements->execute(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): void
    {
        // Check if the chat exists, if it does not delete it
        $this->checkChatExists($id);

        $statements = $this->connection->prepare('DELETE FROM chats WHERE id = :id');
        $statements->execute(['id' => $id]);

        // Delete all members in the chat
        $statements = $this->connection->prepare('DELETE FROM chat_members WHERE chat_id = :chat_id');
        $statements->execute(['chat_id' => $id]);

        // Delete all messages in the chat
        $statements = $this->connection->prepare('DELETE FROM messages WHERE chat_id = :chat_id');
        $statements->execute(['chat_id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function joinMember(int $chatId, int $userId): void
    {
        // Check if chat exists, if the user exists and if the user is already in the chat
        $this->checkUserExists($userId);
        $this->checkChatExists($chatId);
        $this->checkUserNotExistsChat($chatId, $userId);

        // Add user to chat
        $statements = $this->connection->prepare(
            'INSERT INTO chat_members (chat_id, user_id) VALUES (:chat_id, :user_id)'
        );
        $statements->execute(['chat_id' => $chatId, 'user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function leaveMember(int $chatId, int $userId): void
    {
        // Check if chat exists, if the user exists and if the user is in the chat
        $this->checkUserExists($userId);
        $this->checkChatExists($chatId);
        $this->checkUserExistsChat($chatId, $userId);

        // Remove user from chat
        $statements = $this->connection->prepare(
            'DELETE FROM chat_members WHERE chat_id = :chat_id AND user_id = :user_id'
        );
        $statements->execute(['chat_id' => $chatId, 'user_id' => $userId]);
    }
}
