<?php

namespace App\middleware;

use App\handlers\commands\category\SelectCategoryMenu;
use App\utils\StoredVarNames;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;

class CategoryMiddleware
{
    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(Nutgram $bot, $next): void
    {
        $currentCategory = $bot->getUserData(StoredVarNames::CURRENT_CATEGORY);
        if (!$currentCategory) {
            SelectCategoryMenu::begin($bot);
        } else {
            $bot->sendMessage('Текущая категория: ' . $currentCategory['name']);
            $next($bot);
        }
    }
}