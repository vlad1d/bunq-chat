<?php

declare(strict_types=1);

namespace App\Application\Actions\Chat;

use Psr\Http\Message\ResponseInterface as Response;

class JoinChatAction extends ChatAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $chatId = (int) $this->resolveArg('chatId');
        $userId = (int) $this->resolveArg('userId');
        $this->chatRepository->joinMember($chatId, $userId);
        $this->logger->info("User of id `{$userId}` joined chat of id `{$chatId}`.");
        return $this->respondWithData(['message' => 'User joined chat successfully.']);
    }
}
