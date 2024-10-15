<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Chat;

use App\Domain\Chat\Chat;
use App\Domain\Chat\ChatRepository;
use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Chat\ChatAlreadyExistsException;
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
            $chats[] = new Chat($row['id']);
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
        return new Chat($row['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function create(int $id): Chat
    {
        $statements = $this->connection->prepare('SELECT * FROM chats WHERE id = (:id)');
        $statements->execute(['id' => $id]);
        if ($statements->fetch(PDO::FETCH_ASSOC)) {
            throw new ChatAlreadyExistsException();
        }

        $statements = $this->connection->prepare('INSERT INTO chats (id) VALUES (:id)');
        $statements->execute(['id' => $id]);

        return new Chat($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): Chat
    {
        $statements = $this->connection->prepare('SELECT * FROM chats WHERE id = :id');
        $statements->execute(['id' => $id]);
        if (!$statements->fetch(PDO::FETCH_ASSOC)) {
            throw new ChatNotFoundException();
        }

        $statements = $this->connection->prepare('DELETE FROM chats WHERE id = :id');
        $statements->execute(['id' => $id]);

        return new Chat($id);
    }
}
