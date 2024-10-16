<?php

declare(strict_types=1);

namespace Tests\Domain\Chat;

use App\Domain\Chat\Chat;
use PHPUnit\Framework\TestCase;

class ChatTest extends TestCase
{
    public function chatProvider(): array
    {
        return [
            [1, [1, 2]],
            [2, [2, 3]],
            [3, []],
            [4, [1]],
            [5, [2, 3, 4]],
        ];
    }

    /**
     * @dataProvider chatProvider
     * @param int $id
     * @param array $members
     */
    public function testGetters(int $id, array $members)
    {
        $chat = new Chat($id, $members);
        $this->assertEquals($id, $chat->getId());
        $this->assertEquals($members, $chat->getMembers());
    }

    /**
     * @dataProvider chatProvider
     * @param int $id
     * @param array $members
     */
    public function testJsonSerialize(int $id, array $members)
    {
        $chat = new Chat($id, $members);

        $expectedPayload = json_encode([
            'id' => $id,
            'members' => array_values($members)
        ]);

        $this->assertEquals($expectedPayload, json_encode($chat));
    }
}
