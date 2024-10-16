<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Message;

use App\Domain\Chat\ChatNotFoundException;
use App\Domain\User\UserNotFoundException;
use App\Domain\Message\UserNotInChatException;
use App\Infrastructure\Persistence\Message\SQLiteMessageRepository;
use App\Infrastructure\Persistence\Chat\SQLiteChatRepository;
use App\Infrastructure\Persistence\User\SQLiteUserRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class SQLiteMessageRepositoryTest extends TestCase
{
    private SQLiteMessageRepository $messageRepository;
    private SQLiteChatRepository $chatRepository;
    private SQLiteUserRepository $userRepository;

    protected function setUp(): void
    {
        $connection = new PDO('sqlite::memory:');
        $this->messageRepository = new SQLiteMessageRepository($connection);
        $this->chatRepository = new SQLiteChatRepository($connection);
        $this->userRepository = new SQLiteUserRepository($connection);
    }

    public function testSendMessage()
    {
        $this->chatRepository->create(1);
        $this->userRepository->create(1);
        $this->chatRepository->joinMember(1, 1);

        $this->messageRepository->sendMessage(1, 1, 'Hello, Bunq!');
        $messages = $this->messageRepository->listMessages(1, 1);
        $this->assertCount(1, $messages);
        $this->assertEquals('Hello, Bunq!', $messages[0]->getContent());
    }

    public function testSendMessageThrowsChatNotFoundException()
    {
        $this->userRepository->create(1);
        $this->expectException(ChatNotFoundException::class);
        $this->messageRepository->sendMessage(1, 1, 'Where am I?');
    }

    public function testSendMessageThrowsUserNotFoundException()
    {
        $this->chatRepository->create(1);
        $this->expectException(UserNotFoundException::class);
        $this->messageRepository->sendMessage(1, 1, 'Who is this?');
    }

    public function testSendMessageThrowsUserNotInChatException()
    {
        $this->chatRepository->create(1);
        $this->userRepository->create(1);
        $this->expectException(UserNotInChatException::class);
        $this->messageRepository->sendMessage(1, 1, 'I am glad to be here!');
    }

    public function testListMessages()
    {
        $this->chatRepository->create(1);
        $this->userRepository->create(1);
        $this->chatRepository->joinMember(1, 1);

        $this->messageRepository->sendMessage(1, 1, 'Hello, World!');
        $this->messageRepository->sendMessage(1, 1, 'Goodbye, World!');
        $messages = $this->messageRepository->listMessages(1, 1);
        $this->assertCount(2, $messages);
        $this->assertEquals('Hello, World!', $messages[0]->getContent());
        $this->assertEquals('Goodbye, World!', $messages[1]->getContent());
    }

    public function testListMessagesThrowsChatNotFoundException()
    {
        $this->userRepository->create(1);
        $this->expectException(ChatNotFoundException::class);
        $this->messageRepository->listMessages(1, 1);
    }

    public function testListMessagesThrowsUserNotFoundException()
    {
        $this->chatRepository->create(1);
        $this->expectException(UserNotFoundException::class);
        $this->messageRepository->listMessages(1, 1);
    }

    public function testListMessagesThrowsUserNotInChatException()
    {
        $this->chatRepository->create(1);
        $this->userRepository->create(1);
        $this->expectException(UserNotInChatException::class);
        $this->messageRepository->listMessages(1, 1);
    }
}
