<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ChatAlreadyExistsException extends DomainRecordNotFoundException
{
    public $message = 'The chat you requested already exists.';
}
