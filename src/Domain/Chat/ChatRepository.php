<?php

declare(strict_types=1);

namespace App\Domain\Chat;

interface ChatRepository
{
    /**
     * @return Chat[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Chat
     * @throws ChatNotFoundException
     */
    public function findChatOfId(int $id): Chat;

    /**
     * @param int $id
     * @return Chat
     * @throws ChatAlreadyExistsException
     */
    public function create(int $id): Chat;

    /**
     * @param int $id
     * @return Chat
     * @throws ChatNotFoundException
     */
    public function delete(int $id): Chat;
}
