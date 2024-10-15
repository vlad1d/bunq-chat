<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use JsonSerializable;

class Chat implements JsonSerializable
{
    private int $id;
    private array $members;

    public function __construct(int $id, array $members = [])
    {
        $this->id = $id;
        $this->members = $members;
    }

    public function getId(): int
    {
        return $this->id;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'members' => array_values($this->members),
        ];
    }
}
