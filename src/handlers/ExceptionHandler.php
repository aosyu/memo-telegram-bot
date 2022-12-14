<?php

namespace App\handlers;

use App\exceptions\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class ExceptionHandler
{
    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(Nutgram $bot, Throwable $exception): void
    {
        if ($_ENV['MODE'] == 'dev') {
            echo $exception->getMessage();
            error_log($exception);
        }

        if ($exception instanceof ValidationException) {
            $bot->sendMessage($exception->getMessage());
            $bot->endConversation();
        } else {
            $bot->sendMessage('Неопознанная ошибка( Попробуйте снова.');
            $bot->sendMessage($exception->getMessage());
        }
    }
}