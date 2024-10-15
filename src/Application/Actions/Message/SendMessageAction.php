<?php

declare(strict_types=1);

namespace App\Application\Actions\Message;

use Psr\Http\Message\ResponseInterface as Response;

class SendMessageAction extends MessageAction
{
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        $chatId = (int) $this->resolveArg('chatId');
        $userId = (int) $this->resolveArg('userId');
        $content = (string) $data['content'];

        if (empty($content)) {
            return $this->respondWithData(['message' => 'Content is required'], 400);
        }

        $this->messageRepository->sendMessage($chatId, $userId, $content);

        return $this->respondWithData(['message' => 'Message sent successfully']);
    }
}
