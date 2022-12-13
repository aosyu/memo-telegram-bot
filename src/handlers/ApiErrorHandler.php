<?php

namespace App\handlers;

use SergiX44\Nutgram\Nutgram;
use Throwable;

class ApiErrorHandler
{
    public function __invoke(Nutgram $bot, Throwable $exception): void
    {
        $bot->sendMessage('Ошибка ' . $exception->getCode());
        if ($_ENV['MODE'] == 'dev') {
            echo $exception->getMessage();
            echo $exception->getCode();
            error_log($exception);
        }
    }
}