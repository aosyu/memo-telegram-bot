<?php

namespace App\handlers\commands;

use App\handlers\commands\card\CreateCardCommand;
use App\service\CardService;
use App\utils\Grades;
use App\utils\StoredVarNames;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartPracticeCommand extends Conversation implements Command
{
    public array|null $card;

    /**
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot)
    {
        $categoryId = $bot->getUserData(StoredVarNames::CURRENT_CATEGORY)['id'];

        /** @var CardService $cardService */
        $cardService = $bot->getGlobalData(StoredVarNames::CARD_SERVICE);
        $this->card = $cardService->getRandomCard($categoryId);

        if (!$this->card) {
            $bot->sendMessage('В выбранной категории еще нет карточек. Чтобы исправить это, нажмите /' . CreateCardCommand::getName());
            $this->end();
        } else {
            $bot->sendMessage("*Вопрос: *" . $this->card['question'] . "\nОценка: " . $this->card['grade'],
                ['parse_mode' => ParseMode::MARKDOWN,
                    'reply_markup' => InlineKeyboardMarkup::make()
                        ->addRow(InlineKeyboardButton::make('Ответ ->', callback_data: 'next'))
                ]);

            $this->next('goNext');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function goNext(Nutgram $bot)
    {
        if ($this->isCallbackQuery($bot) && $bot->callbackQuery()->data == 'next') {
            $bot->sendMessage('*Ответ: *' . $this->card["answer"]
                . "\nВаша оценка:", ['parse_mode' => ParseMode::MARKDOWN,
                'reply_markup' => InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('Отлично!', callback_data: Grades::GRADE_EXC),
                        InlineKeyboardButton::make('Нормально.', callback_data: Grades::GRADE_OK),
                        InlineKeyboardButton::make('Плохо :(', callback_data: Grades::GRADE_BAD))
                    ->addRow(InlineKeyboardButton::make('Закончить сессию', callback_data: 'die'))
            ]);

            $this->next('gradeAnswer');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function gradeAnswer(Nutgram $bot)
    {
        if ($this->isCallbackQuery($bot)) {
            $result = $bot->callbackQuery()->data;
            if ($result == Grades::GRADE_EXC || $result == Grades::GRADE_OK || $result == Grades::GRADE_BAD) {
                /** @var CardService $cardService */
                $cardService = $bot->getGlobalData(StoredVarNames::CARD_SERVICE);
                $cardService->setGrade($this->card['id'], $bot->callbackQuery()->data);

                $this->start($bot);
            } else {
                $bot->sendMessage('Bye!');
                $this->end();
            }
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function isCallbackQuery(Nutgram $bot): bool
    {
        $bot->message()->delete();
        if (!$bot->isCallbackQuery()) {
            $bot->sendMessage('Неожиданная команда, сессия прервана.');
            $this->end();
            HelpCommand::begin($bot);
            return false;
        }
        return true;
    }

    public static function getName(): string
    {
        return 'start';
    }

    public static function getDescription(): string
    {
        return 'начать тренировку';
    }
}