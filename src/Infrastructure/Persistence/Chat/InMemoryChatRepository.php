<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Chat;

use App\Domain\Chat\Chat;
use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Chat\ChatAlreadyExistsException;
use App\Domain\Chat\ChatRepository;

class InMemoryChatRepository implements ChatRepository
{
    /**
     * @var Chat[]
     */
    private array $chats;

    /**
     * @param Chat[]|null $chats
     */
    public function __construct(array $chats = null)
    {
        $this->chats = $chats ?? [
            1 => new Chat(1),
            2 => new Chat(2),
            3 => new Chat(3)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->chats);
    }

    /**
     * {@inheritdoc}
     */
    public function findChatOfId(int $id): Chat
    {
        if (!isset($this->chats[$id])) {
            throw new ChatNotFoundException();
        }
        return $this->chats[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function create(int $id): Chat
    {
        if (isset($this->chats[$id])) {
            throw new ChatAlreadyExistsException();
        }
        $this->chats[$id] = new Chat($id);
        return $this->chats[$id];
    }
}
