<?php

declare(strict_types=1);

namespace App\Application\Actions\Chat;

use Psr\Http\Message\ResponseInterface as Response;

class ViewChatAction extends ChatAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $chatId = (int) $this->resolveArg('id');
        $chat = $this->chatRepository->findChatOfId($chatId);

        $this->logger->info("Chat of id `{$chatId}` was viewed.");

        return $this->respondWithData($chat);
    }
}
