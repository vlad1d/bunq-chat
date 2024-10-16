<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Chat;

use App\Domain\Chat\ChatAlreadyExistsException;
use App\Domain\Chat\ChatNotFoundException;
use App\Domain\Message\UserAlreadyExistsInChatException;
use App\Domain\Message\UserNotInChatException;
use App\Domain\User\UserNotFoundException;
use App\Infrastructure\Persistence\Chat\SQLiteChatRepository;
use App\Infrastructure\Persistence\Message\SQLiteMessageRepository;
use App\Infrastructure\Persistence\User\SQLiteUserRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class SQLiteChatRepositoryTest extends TestCase
{
    private SQLiteChatRepository $chatRepository;
    private SQLiteUserRepository $userRepository;
    private SQLiteMessageRepository $messageRepository;

    protected function setUp(): void
    {
        $connection = new PDO('sqlite::memory:');
        $this->chatRepository = new SQLiteChatRepository($connection);
        $this->userRepository = new SQLiteUserRepository($connection);
        $this->messageRepository = new SQLiteMessageRepository($connection);
    }

    public function testFindAll()
    {
        $this->chatRepository->create(1);
        $this->chatRepository->create(2);
        $chats = $this->chatRepository->findAll();
        $this->assertCount(2, $chats);
        $this->assertEquals(1, $chats[0]->getId());
        $this->assertEquals(2, $chats[1]->getId());
    }

    public function testFindChatOfId()
    {
        $this->chatRepository->create(1);
        $chat = $this->chatRepository->findChatOfId(1);
        $this->assertEquals(1, $chat->getId());
    }

    public function testFindChatOfIdThrowsNotFoundException()
    {
        $this->expectException(ChatNotFoundException::class);
        $this->chatRepository->findChatOfId(1);
    }

    public function testCreateChat()
    {
        $this->chatRepository->create(1);
        $chat = $this->chatRepository->findChatOfId(1);
        $this->assertEquals(1, $chat->getId());
    }

    public function testCreateChatThrowsAlreadyExistsException()
    {
        $this->chatRepository->create(1);
        $this->expectException(ChatAlreadyExistsException::class);
        $this->chatRepository->create(1);
    }

    public function testDeleteChatThrowsNotFoundException()
    {
        $this->chatRepository->create(1);
        $this->chatRepository->delete(1);
        $this->expectException(ChatNotFoundException::class);
        $this->chatRepository->delete(1);
    }

    public function testJoinMember()
    {
        $this->userRepository->create(1);
        $this->chatRepository->create(1);
        $this->chatRepository->joinMember(1, 1);
        $chat = $this->chatRepository->findChatOfId(1);
        $this->assertContains(1, $chat->getMembers());
    }

    public function testJoinMemberThrowsChatNotFoundException()
    {
        $this->userRepository->create(1);
        $this->expectException(ChatNotFoundException::class);
        $this->chatRepository->joinMember(1, 1);
    }

    public function testJoinMemberThrowsUserNotFoundException()
    {
        $this->chatRepository->create(1);
        $this->expectException(UserNotFoundException::class);
        $this->chatRepository->joinMember(1, 1);
    }

    public function testJoinMemberThrowsUserAlreadyExistsInChatException()
    {
        $this->userRepository->create(1);
        $this->chatRepository->create(1);
        $this->chatRepository->joinMember(1, 1);
        $this->expectException(UserAlreadyExistsInChatException::class);
        $this->chatRepository->joinMember(1, 1);
    }

    public function testLeaveMember()
    {
        $this->userRepository->create(1);
        $this->chatRepository->create(1);
        $this->chatRepository->joinMember(1, 1);
        $this->chatRepository->leaveMember(1, 1);
        $chat = $this->chatRepository->findChatOfId(1);
        $this->assertNotContains(1, $chat->getMembers());
    }

    public function testLeaveMemberThrowsChatNotFoundException()
    {
        $this->userRepository->create(1);
        $this->expectException(ChatNotFoundException::class);
        $this->chatRepository->leaveMember(1, 1);
    }

    public function testLeaveMemberThrowsUserNotFoundException()
    {
        $this->chatRepository->create(1);
        $this->expectException(UserNotFoundException::class);
        $this->chatRepository->leaveMember(1, 1);
    }

    public function testLeaveMemberThrowsUserNotInChatException()
    {
        $this->userRepository->create(1);
        $this->chatRepository->create(1);
        $this->expectException(UserNotInChatException::class);
        $this->chatRepository->leaveMember(1, 1);
    }
}
