<?php

declare(strict_types=1);

namespace App\Domain\Message;

use App\Domain\Chat\ChatNotFoundException;
use App\Domain\User\UserNotFoundException;

interface MessageRepository
{
    /**
     * @param int $chatId
     * @param int $userId
     * @param string $content
     * @throws ChatNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotInChatException
     */
    public function sendMessage(int $chatId, int $userId, string $content): void;

    /**
     * @param int $chatId
     * @param int $userId
     * @return array
     * @throws ChatNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotInChatException
     */
    public function listMessages(int $chatId, int $userId): array;
}
