<?php

declare(strict_types=1);

namespace App\Application\Actions\Chat;

use App\Application\Actions\Action;
use App\Domain\Chat\ChatRepository;
use Psr\Log\LoggerInterface;

abstract class ChatAction extends Action
{
    protected ChatRepository $chatRepository;

    public function __construct(LoggerInterface $logger, ChatRepository $chatRepository)
    {
        parent::__construct($logger);
        $this->chatRepository = $chatRepository;
    }
}
