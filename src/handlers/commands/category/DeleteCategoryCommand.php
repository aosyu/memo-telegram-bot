<?php

namespace App\handlers\commands\category;

use App\handlers\commands\Command;
use App\service\CategoryService;
use App\utils\StoredVarNames;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class DeleteCategoryCommand extends InlineMenu implements Command
{
    /**
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot)
    {
        $category = $bot->getUserData(StoredVarNames::CURRENT_CATEGORY);

        $this->menuText('Вы уверены, что хотите удалить категорию \''
            . $category['name']
            . '\'? Обратите внимание, что при этом удалятся и все ассоциированные с этой категорией карточки.')
            ->addButtonRow(InlineKeyboardButton::make('удалить', callback_data: '@deleteCategory'))
            ->addButtonRow(InlineKeyboardButton::make('отменить', callback_data: '@cancelOperation'))
            ->showMenu();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function deleteCategory(Nutgram $bot)
    {
        /** @var CategoryService $categoryService */
        $categoryService = $bot->getGlobalData(StoredVarNames::CATEGORY_SERVICE);
        $categoryService->deleteById($bot->getUserData(StoredVarNames::CURRENT_CATEGORY)['id']);
        $bot->setUserData(StoredVarNames::CURRENT_CATEGORY, null);
        $this->end();
        $bot->sendMessage('Категория успешно удалена.');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cancelOperation(Nutgram $bot)
    {
        $bot->sendMessage('Удаление категории отменено.');
        $this->end();
    }

    public static function getName(): string
    {
        return 'delete_current_category';
    }

    public static function getDescription(): string
    {
        return 'удалить текущую категорию';
    }
}