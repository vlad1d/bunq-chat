<?php

declare(strict_types=1);

namespace App\Application\Actions\Chat;

use Psr\Http\Message\ResponseInterface as Response;

class CreateChatAction extends ChatAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $chatId = (int) $this->resolveArg('id');
        $chat = $this->chatRepository->create($chatId);
        $this->logger->info("Chat of id `{$chatId}` was created.");
        return $this->respondWithData($chat);
    }
}
