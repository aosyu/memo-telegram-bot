<?php

namespace App\handlers\commands\category;

use App\handlers\commands\Command;
use App\handlers\commands\HelpCommand;
use App\service\CategoryService;
use App\utils\StoredVarNames;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class SelectCategoryMenu extends InlineMenu implements Command
{
    /**
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot)
    {
        /** @var CategoryService $categoryService */
        $categoryService = $bot->getGlobalData(StoredVarNames::CATEGORY_SERVICE);
        $categories = $categoryService->findAllByUserId($bot->userId());

        $this->menuText('*Выберите категорию:*', ['parse_mode' => ParseMode::MARKDOWN]);
        foreach ($categories as $category) {
            $name = $category['name'];
            $id = $category['id'];
            $this->addButtonRow(
                InlineKeyboardButton::make($name, callback_data: $id . '@handleCategoryClick')
            );
        }
        $this->addButtonRow(
            InlineKeyboardButton::make('create new',
                callback_data: '@handleCreateCategoryClick'
            )
        );

        $this->showMenu();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleCategoryClick(Nutgram $bot)
    {
        $categoryId = $bot->callbackQuery()->data;
        $this->end();

        /** @var CategoryService $categoryService */
        $categoryService = $bot->getGlobalData(StoredVarNames::CATEGORY_SERVICE);
        $category = $categoryService->findById($categoryId);

        if ($category) {
            $name = $category['name'];
            $bot->sendMessage("Выбранная категория: $name");
            $bot->setUserData(StoredVarNames::CURRENT_CATEGORY, $category);
        }

        HelpCommand::begin($bot);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleCreateCategoryClick(Nutgram $bot)
    {
        $this->end();
        CreateCategoryCommand::begin($bot);
    }

    public static function getName(): string
    {
        return 'select_category';
    }

    public static function getDescription(): string
    {
        return 'выбрать категорию';
    }
}