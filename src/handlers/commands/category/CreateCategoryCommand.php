<?php

namespace App\handlers\commands\category;

use App\handlers\commands\Command;
use App\service\CategoryService;
use App\utils\StoredVarNames;
use Exception;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class CreateCategoryCommand extends Conversation implements Command
{
    /**
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot)
    {
        $bot->sendMessage('Введите название категории:');
        $this->next('addCategory');
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function addCategory(Nutgram $bot)
    {
        $input = $bot->message()->text;

        /** @var CategoryService $categoryService */
        $categoryService = $bot->getGlobalData(StoredVarNames::CATEGORY_SERVICE);

        $categoryService->addCategory($input, $bot->userId());
        $this->end();
        $bot->sendMessage("Поздравляем, категория '$input' успешно добавлена!");
    }

    public static function getName(): string
    {
        return 'create_category';
    }

    public static function getDescription(): string
    {
        return 'создать новую категорию';
    }
}