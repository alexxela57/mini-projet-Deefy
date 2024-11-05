<?php

namespace iutnc\deefy\action;

use iutnc\deefy\bd as bd;

class DefaultAction
{

    /**
     * s'active quand on appuie sur le bouton
     * @return string
     * @throws \iutnc\deefy\exception\CompteException
     */
    public function execute(): string
    {
        $pdo = bd\ConnectionFactory::makeConnection();
        $s="";

        return $s;
    }
}
