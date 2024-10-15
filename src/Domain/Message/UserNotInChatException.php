<?php

declare(strict_types=1);

namespace App\Domain\Message;

use App\Domain\DomainException\DomainRecordNotFoundException;

class UserNotInChatException extends DomainRecordNotFoundException
{
    public $message = 'The user is not joined in the chat group.';
}
