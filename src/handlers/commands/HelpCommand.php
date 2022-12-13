<?php

namespace App\handlers\commands;

use App\handlers\commands\card\CreateCardCommand;
use App\handlers\commands\category\CreateCategoryCommand;
use App\handlers\commands\category\DeleteCategoryCommand;
use App\handlers\commands\category\SelectCategoryMenu;
use SergiX44\Nutgram\Nutgram;

class HelpCommand implements Command
{
    public function __invoke(Nutgram $bot)
    {
        $bot->sendMessage('Доступные команды:'
            . $this->buildDescription(StartPracticeCommand::class)
            . "\n"
            . $this->buildDescription(SelectCategoryMenu::class)
            . "\n"
            . $this->buildDescription(CreateCategoryCommand::class)
            . $this->buildDescription(CreateCardCommand::class)
            . "\n"
            . $this->buildDescription(DeleteCategoryCommand::class)
        );
    }

    public static function begin(Nutgram $bot): self
    {
        $instance = new static();
        $instance($bot);

        return $instance;
    }

    private function buildDescription($class): string
    {
        return '
        /' . $class::getName() . ' - ' . $class::getDescription();
    }

    public static function getName(): string
    {
        return 'help';
    }

    public static function getDescription(): string
    {
        return 'доступные команды';
    }
}