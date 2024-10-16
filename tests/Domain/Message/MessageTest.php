<?php

declare(strict_types=1);

namespace Tests\Domain\Message;

use App\Domain\Message\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function messageProvider(): array
    {
        return [
            [1, 1, 1, 'Hey, Bunq!'],
            [2, 1, 2, 'Hey back! You good?'],
            [3, 1, 1, 'Totally!'],
            [4, 3, 3, 'Anyone here?'],
            [5, 20, 4, 'Hi chat!'],
        ];
    }

    /**
     * @dataProvider messageProvider
     * @param int $id
     * @param int $chatId
     * @param int $userId
     * @param string $content
     */
    public function testGetters(int $id, int $chatId, int $userId, string $content)
    {
        $message = new Message($id, $chatId, $userId, $content);
        $this->assertEquals($id, $message->getId());
        $this->assertEquals($chatId, $message->getChatId());
        $this->assertEquals($userId, $message->getUserId());
        $this->assertEquals($content, $message->getContent());
    }

    /**
     * @dataProvider messageProvider
     * @param int $id
     * @param int $chatId
     * @param int $userId
     * @param string $content
     */
    public function testJsonSerialize(int $id, int $chatId, int $userId, string $content)
    {
        $message = new Message($id, $chatId, $userId, $content);

        $expectedPayload = json_encode([
            'id' => $id,
            'chatId' => $chatId,
            'userId' => $userId,
            'content' => $content
        ]);

        $this->assertEquals($expectedPayload, json_encode($message));
    }
}
