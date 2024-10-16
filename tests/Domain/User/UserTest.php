<?php

declare(strict_types=1);

namespace Tests\Domain\User;

use App\Domain\User\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function userProvider(): array
    {
        return [
            [1],
            [2],
            [3],
            [4],
            [5],
        ];
    }

    /**
     * @dataProvider userProvider
     * @param int $id
     */
    public function testGetters(int $id)
    {
        $user = new User($id);
        $this->assertEquals($id, $user->getId());
    }

    /**
     * @dataProvider userProvider
     * @param int    $id
     */
    public function testJsonSerialize(int $id)
    {
        $user = new User($id);

        $expectedPayload = json_encode([
            'id' => $id
        ]);

        $this->assertEquals($expectedPayload, json_encode($user));
    }
}
