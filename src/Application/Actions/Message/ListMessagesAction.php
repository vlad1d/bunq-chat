<?php

declare(strict_types=1);

namespace App\Application\Actions\Message;

use Psr\Http\Message\ResponseInterface as Response;

class ListMessagesAction extends MessageAction
{
    protected function action(): Response
    {
        $chatId = (int) $this->resolveArg('chatId');
        $userId = (int) $this->resolveArg('userId');
        $messages = $this->messageRepository->listMessages($chatId, $userId);

        return $this->respondWithData($messages);
    }
}
