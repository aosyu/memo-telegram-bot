<?php

namespace App\handlers\commands;

interface Command
{
    public static function getName(): string;

    public static function getDescription(): string;
}