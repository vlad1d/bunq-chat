<?php

declare(strict_types=1);

namespace App\Domain\Message;

use App\Domain\DomainException\DomainRecordNotFoundException;

class UserAlreadyExistsInChatException extends DomainRecordNotFoundException
{
    public $message = 'The user is already in the chat group.';
}
