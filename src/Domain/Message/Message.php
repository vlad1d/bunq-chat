<?php

declare(strict_types=1);

namespace App\Domain\Message;

use JsonSerializable;

class Message implements JsonSerializable
{
    /**
     * @var int $id
     */
    private int $id;
    /**
     * @var int $chatId
     */
    private int $chatId;
    /**
     * @var int $userId
     */
    private int $userId;
    /**
     * @var string $content
     */
    private string $content;

    /**
     * Message constructor.
     * @param int $id
     * @param int $chatId
     * @param int $userId
     * @param string $content
     */
    public function __construct(int $id, int $chatId, int $userId, string $content)
    {
        $this->id = $id;
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
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
