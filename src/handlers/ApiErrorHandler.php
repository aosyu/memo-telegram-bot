<?php

namespace App\handlers;

use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class ApiErrorHandler
{
    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(Nutgram $bot, Throwable $exception): void
    {
        $bot->sendMessage('Ошибка ' . $exception->getCode());
        $bot->endConversation();
        if ($_ENV['MODE'] == 'dev') {
            echo $exception->getMessage();
            echo $exception->getCode();
            error_log($exception);
        }
    }
}