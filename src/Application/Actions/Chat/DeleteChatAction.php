<?php

declare(strict_types=1);

namespace App\Application\Actions\Chat;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteChatAction extends ChatAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $chatId = (int) $this->resolveArg('id');
        $this->chatRepository->delete($chatId);
        $this->logger->info("Chat of id `{$chatId}` was deleted.");
        return $this->respondWithData(['message' => 'Chat deleted successfully']);
    }
}
