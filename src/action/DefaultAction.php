<?php

namespace iutnc\deefy\action;

use iutnc\deefy\bd as bd;

class DefaultAction
{
    public function __construct()
    {

    }

    public function execute(): string
    {
        $pdo = bd\ConnectionFactory::makeConnection();
        $s="";

        return $s;
    }
}
