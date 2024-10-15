<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Chat;

use App\Domain\Chat\Chat;
use App\Domain\Chat\ChatRepository;
use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Chat\ChatAlreadyExistsException;
use App\Domain\User\UserAlreadyExistsException;
use App\Domain\User\UserNotFoundException;
use PDO;

class SQLiteChatRepository implements ChatRepository
{
    /**
     * @var PDO
     */
    private PDO $connection;

    /**
     * @param PDO $connection
     * @throws ChatAlreadyExistsException
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->initialiseChats();
    }

    /**
     * @throws ChatAlreadyExistsException
     */
    private function initialiseChats(): void
    {
        $this->connection->exec('CREATE TABLE IF NOT EXISTS chats (id INTEGER PRIMARY KEY)');
        $this->connection->exec('CREATE TABLE IF NOT EXISTS chat_members 
(chat_id INTEGER, user_id INTEGER, PRIMARY KEY (chat_id, user_id))');
        $statements = $this->connection->query('SELECT COUNT(*) FROM chats');
        $cnt = $statements->fetchColumn();

        if ($cnt == 0) {
            $this->create(1);
            $this->create(2);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
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
        $statements = $this->connection->prepare('SELECT * FROM chats WHERE id = :id');
        $statements->execute(['id' => $id]);
        $row = $statements->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new ChatNotFoundException();
        }

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
        $statements = $this->connection->prepare('SELECT * FROM chats WHERE id = (:id)');
        $statements->execute(['id' => $id]);
        if ($statements->fetch(PDO::FETCH_ASSOC)) {
            throw new ChatAlreadyExistsException();
        }

        $statements = $this->connection->prepare('INSERT INTO chats (id) VALUES (:id)');
        $statements->execute(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): void
    {
        $statements = $this->connection->prepare('SELECT * FROM chats WHERE id = :id');
        $statements->execute(['id' => $id]);
        if (!$statements->fetch(PDO::FETCH_ASSOC)) {
            throw new ChatNotFoundException();
        }

        $statements = $this->connection->prepare('DELETE FROM chats WHERE id = :id');
        $statements->execute(['id' => $id]);

        $statements = $this->connection->prepare('DELETE FROM chat_members WHERE chat_id = :chat_id');
        $statements->execute(['chat_id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function joinMember(int $chatId, int $userId): void
    {
        // Check if chat exists
        $memberStatements = $this->checkChat($chatId, $userId);
        if ($memberStatements->fetch(PDO::FETCH_ASSOC)) {
            throw new UserAlreadyExistsException();
        }

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
        // Check if chat exists
        $memberStatements = $this->checkChat($chatId, $userId);
        if (!$memberStatements->fetch(PDO::FETCH_ASSOC)) {
            throw new UserNotFoundException();
        }

        // Remove user from chat
        $statements = $this->connection->prepare(
            'DELETE FROM chat_members WHERE chat_id = :chat_id AND user_id = :user_id'
        );
        $statements->execute(['chat_id' => $chatId, 'user_id' => $userId]);
    }

    /**
     * @param int $chatId
     * @param int $userId
     * @return false|\PDOStatement
     * @throws ChatNotFoundException|UserNotFoundException
     */
    public function checkChat(int $chatId, int $userId)
    {
        $chatStatements = $this->connection->prepare('SELECT * FROM chats WHERE id = :id');
        $chatStatements->execute(['id' => $chatId]);
        if (!$chatStatements->fetch(PDO::FETCH_ASSOC)) {
            throw new ChatNotFoundException();
        }

        // Check if user exists
        $userStatements = $this->connection->prepare('SELECT * FROM users WHERE id = :id');
        $userStatements->execute(['id' => $userId]);
        if (!$userStatements->fetch(PDO::FETCH_ASSOC)) {
            throw new UserNotFoundException();
        }

        // Check if user is already a member
        $memberStatements = $this->connection->prepare(
            'SELECT * FROM chat_members WHERE chat_id = :chat_id AND user_id = :user_id'
        );
        $memberStatements->execute(['chat_id' => $chatId, 'user_id' => $userId]);
        return $memberStatements;
    }
}
