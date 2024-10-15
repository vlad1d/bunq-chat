<?php

declare(strict_types=1);

namespace App\Application\Actions\Chat;

use Psr\Http\Message\ResponseInterface as Response;

class LeaveChatAction extends ChatAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $chatId = (int) $this->resolveArg('chatId');
        $userId = (int) $this->resolveArg('userId');
        $this->chatRepository->leaveMember($chatId, $userId);
        $this->logger->info("User of id `{$userId}` left chat of id `{$chatId}`.");
        return $this->respondWithData(['message' => 'User left chat successfully.']);
    }
}
