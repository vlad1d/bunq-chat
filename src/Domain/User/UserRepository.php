<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    /**
     * @param int $id
     * @throws UserAlreadyExistsException
     */
    public function create(int $id): void;

    /**
     * @param int $id
     * @throws UserNotFoundException
     */
    public function delete(int $id): void;
}
