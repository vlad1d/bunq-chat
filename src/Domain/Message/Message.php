<?php

declare(strict_types=1);

namespace App\Domain\Message;

use JsonSerializable;

class Message implements JsonSerializable
{
    private int $id;
    private int $chatId;
    private int $userId;
    private string $content;

    public function __construct(int $id, int $chatId, int $userId, string $content)
    {
        $this->id = $id;
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->content = $content;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'chatId' => $this->chatId,
            'userId' => $this->userId,
            'content' => $this->content
        ];
    }
}