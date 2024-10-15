<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Domain\User\UserAlreadyExistsException;
use App\Domain\User\UserNotFoundException;

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
     * @throws ChatAlreadyExistsException
     */
    public function findChatOfId(int $id): Chat;

    /**
     * @param int $id
     * @throws ChatAlreadyExistsException
     */
    public function create(int $id): void;

    /**
     * @param int $id
     * @throws ChatNotFoundException
     */
    public function delete(int $id): void;

    /**
     * @param int $chatId
     * @param int $userId
     * @throws ChatNotFoundException
     * @throws UserAlreadyExistsException
     * @throws ChatAlreadyExistsException
     * @throws UserNotFoundException
     */
    public function joinMember(int $chatId, int $userId): void;

    /**
     * @param int $chatId
     * @param int $userId
     * @throws ChatNotFoundException
     * @throws UserAlreadyExistsException
     * @throws UserNotFoundException
     */
    public function leaveMember(int $chatId, int $userId): void;
}
