<?php

namespace App\handlers\commands\card;

use App\exceptions\ValidationException;
use App\handlers\commands\Command;
use App\handlers\commands\StartPracticeCommand;
use App\service\CardService;
use App\utils\StoredVarNames;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class CreateCardCommand extends Conversation implements Command
{
    public string $question = '';

    /**
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot)
    {
        $bot->sendMessage('Введите вопрос/определение:');
        $this->next('inputQuestion');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function inputQuestion(Nutgram $bot)
    {
        $this->question = $bot->message()->text;
        $bot->sendMessage('Введите ответ:');
        $this->next('inputAnswer');
    }

    /**
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function inputAnswer(Nutgram $bot)
    {
        $answer = $bot->message()->text;
        $categoryId = $bot->getUserData(StoredVarNames::CURRENT_CATEGORY)['id'];

        /** @var CardService $cardService */
        $cardService = $bot->getGlobalData(StoredVarNames::CARD_SERVICE);

        $cardService->add($categoryId, $this->question, $answer);

        $bot->sendMessage("Карточка успешно добавлена, теперь можно начать тренировку: /" . StartPracticeCommand::getName());
        $this->end();
    }

    public static function getName(): string
    {
        return 'create_card';
    }

    public static function getDescription(): string
    {
        return 'создать новую карточку';
    }
}