<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\DomainException\DomainRecordNotFoundException;

class UserAlreadyExistsException extends DomainRecordNotFoundException
{
    public $message = 'The user you requested is already in the scope.';
}
