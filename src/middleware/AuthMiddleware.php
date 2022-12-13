<?php

namespace App\middleware;

use App\Repository\UserRepository;
use App\utils\StoredVarNames;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;

class AuthMiddleware
{
    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(Nutgram $bot, $next): void
    {
        if (!$bot->getUserData(StoredVarNames::LOGGED_IN)) {
            /** @var UserRepository $userRepository */
            $userRepository = $bot->getGlobalData(StoredVarNames::USER_SERVICE);
            $userRepository->addIfAbsent($bot->userId());
            $bot->setUserData(StoredVarNames::LOGGED_IN, true);
        }
        $next($bot);
    }
}