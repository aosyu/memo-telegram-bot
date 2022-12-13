<?php

namespace App\repository;

use mysqli;

class AbstractRepository
{
    protected mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }
}