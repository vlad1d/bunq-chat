<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Message;

use App\Domain\Message\Message;
use App\Domain\Message\MessageRepository;
use App\Infrastructure\Persistence\RepositoryHandler;
use PDO;

class SQLiteMessageRepository implements MessageRepository
{
    use RepositoryHandler;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->initialiseMessages();
    }

    private function initialiseMessages(): void
    {
        $this->connection->exec(
            'CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                chat_id INTEGER,
                user_id INTEGER,
                content TEXT
            )'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage(int $chatId, int $userId, string $content): void
    {
        $this->checkChatExists($chatId);
        $this->checkUserExists($userId);
        $this->checkUserExistsChat($chatId, $userId);

        $statement = $this->connection->prepare(
            'INSERT INTO messages (chat_id, user_id, content) VALUES (:chatId, :userId, :content)'
        );
        $statement->execute(['chatId' => $chatId, 'userId' => $userId, 'content' => $content]);
    }

    /**
     * {@inheritdoc}
     */
    public function listMessages(int $chatId, int $userId): array
    {
        $this->checkChatExists($chatId);
        $this->checkUserExists($userId);
        $this->checkUserExistsChat($chatId, $userId);

        $statement = $this->connection->prepare(
            'SELECT * FROM messages WHERE chat_id = :chatId'
        );
        $statement->execute(['chatId' => $chatId]);
        $messages = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new Message($row['id'], $row['chat_id'], $row['user_id'], $row['content']);
        }
        return $messages;
    }
}
