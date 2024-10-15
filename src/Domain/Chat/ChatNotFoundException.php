<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ChatNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The chat you requested does not exist.';
}
