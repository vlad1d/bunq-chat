<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserAlreadyExistsException;
use App\Domain\User\UserRepository;
use PDO;

class SQLiteUserRepository implements UserRepository
{
    /**
     * @var PDO
     */
    private PDO $connection;

    /**
     * @param PDO $connection
     * @throws UserAlreadyExistsException
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->initialiseUsers();
    }

    /**
     * @throws UserAlreadyExistsException
     */
    private function initialiseUsers(): void
    {
        $this->connection->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY)');
        $statements = $this->connection->query('SELECT COUNT(*) FROM users');
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
        $statements = $this->connection->query('SELECT * FROM users');
        $users = [];
        while ($row = $statements->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row['id']);
        }
        return $users;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): User
    {
        $statements = $this->connection->prepare('SELECT * FROM users WHERE id = :id');
        $statements->execute(['id' => $id]);
        $row = $statements->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new UserNotFoundException();
        }
        return new User($row['id']);
    }


    /**
     * {@inheritdoc}
     */
    public function create(int $id): void
    {
        $statements = $this->connection->prepare('SELECT * FROM users WHERE id = (:id)');
        $statements->execute(['id' => $id]);
        if ($statements->fetch(PDO::FETCH_ASSOC)) {
            throw new UserAlreadyExistsException();
        }

        $statements = $this->connection->prepare('INSERT INTO users (id) VALUES (:id)');
        $statements->execute(['id' => $id]);
    }
}
