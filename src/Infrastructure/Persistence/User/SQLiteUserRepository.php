<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserAlreadyExistsException;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\RepositoryHandler;
use PDO;

class SQLiteUserRepository implements UserRepository
{
    use RepositoryHandler;

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->initialiseUsers();
    }

    private function initialiseUsers(): void
    {
        $this->connection->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY)');
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
        $this->checkUserExists($id);
        $statements = $this->connection->prepare('SELECT * FROM users WHERE id = :id');
        $statements->execute(['id' => $id]);
        $row = $statements->fetch(PDO::FETCH_ASSOC);
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
