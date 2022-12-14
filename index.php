<?php
require 'vendor/autoload.php';

use App\handlers\ApiErrorHandler;
use App\handlers\commands\card\CreateCardCommand;
use App\handlers\commands\category\CreateCategoryCommand;
use App\handlers\commands\category\DeleteCategoryCommand;
use App\handlers\commands\category\SelectCategoryMenu;
use App\handlers\commands\HelpCommand;
use App\handlers\commands\StartPracticeCommand;
use App\handlers\ExceptionHandler;
use App\middleware\AuthMiddleware;
use App\middleware\CategoryMiddleware;
use App\repository\CardRepository;
use App\repository\CategoryRepository;
use App\repository\UserRepository;
use App\service\CardService;
use App\service\CategoryService;
use App\utils\StoredVarNames;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$bot = new Nutgram($_ENV['BOT_TOKEN']);
$bot->setRunningMode(Polling::class);

$mysqli = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
$bot->setGlobalData(StoredVarNames::USER_SERVICE, new UserRepository($mysqli));
$bot->setGlobalData(StoredVarNames::CATEGORY_SERVICE, new CategoryService(new CategoryRepository($mysqli)));
$bot->setGlobalData(StoredVarNames::CARD_SERVICE, new CardService(new CardRepository($mysqli)));

$bot->middleware(AuthMiddleware::class);

$bot->fallback(function (Nutgram $bot) {
    $bot->sendMessage('Извините, не понял.');
    HelpCommand::begin($bot);
});

$bot->onException(ExceptionHandler::class);

$bot->onApiError(ApiErrorHandler::class);

$bot->onCommand(SelectCategoryMenu::getName(), SelectCategoryMenu::class)
    ->description(SelectCategoryMenu::getDescription());

$bot->onCommand(CreateCardCommand::getName(), CreateCardCommand::class)
    ->middleware(CategoryMiddleware::class)
    ->description(CreateCardCommand::getDescription());

$bot->onCommand(StartPracticeCommand::getName(), StartPracticeCommand::class)
    ->middleware(CategoryMiddleware::class)
    ->description(StartPracticeCommand::getDescription());

$bot->onCommand(CreateCategoryCommand::getName(), CreateCategoryCommand::class)
    ->description(CreateCategoryCommand::getDescription());

$bot->onCommand(HelpCommand::getName(), HelpCommand::class)
    ->description(HelpCommand::getDescription());

$bot->onCommand(DeleteCategoryCommand::getName(), DeleteCategoryCommand::class)
    ->middleware(CategoryMiddleware::class)
    ->description(DeleteCategoryCommand::getDescription());;

$bot->registerMyCommands();

try {
    $bot->run();
} catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
    die('Connect Error');
}