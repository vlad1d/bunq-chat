<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use JsonSerializable;

class Chat implements JsonSerializable
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id
        ];
    }
}
