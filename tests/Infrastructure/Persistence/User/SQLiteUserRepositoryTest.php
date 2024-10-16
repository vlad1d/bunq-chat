<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\User;

use App\Domain\User\UserAlreadyExistsException;
use App\Domain\User\UserNotFoundException;
use App\Infrastructure\Persistence\User\SQLiteUserRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class SQLiteUserRepositoryTest extends TestCase
{
    private SQLiteUserRepository $userRepository;

    protected function setUp(): void
    {
        $connection = new PDO('sqlite::memory:');
        $this->userRepository = new SQLiteUserRepository($connection);
    }

    public function testFindAll()
    {
        $this->userRepository->create(1);
        $this->userRepository->create(2);
        $users = $this->userRepository->findAll();
        $this->assertCount(2, $users);
        $this->assertEquals(1, $users[0]->getId());
        $this->assertEquals(2, $users[1]->getId());
    }

    public function testCreateFindUserOfId()
    {
        $this->userRepository->create(1);
        $user = $this->userRepository->findUserOfId(1);
        $this->assertEquals(1, $user->getId());
    }

    public function testFindUserOfIdThrowsNotFoundException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->userRepository->findUserOfId(1);
    }

    public function testCreateUserThrowsAlreadyExistsException()
    {
        $this->userRepository->create(1);
        $this->expectException(UserAlreadyExistsException::class);
        $this->userRepository->create(1);
    }
}
