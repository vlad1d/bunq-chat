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

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->initialiseMessages();
    }

    /**
     * Initialise the messages table.
     */
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
        // Error checking
        $this->checkChatExists($chatId);
        $this->checkUserExists($userId);
        $this->checkUserExistsChat($chatId, $userId);

        // Insert message into the database
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
        // Error checking
        $this->checkChatExists($chatId);
        $this->checkUserExists($userId);
        $this->checkUserExistsChat($chatId, $userId);

        // Return a list of all messages in the chat
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
