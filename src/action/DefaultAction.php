<?php

namespace action;

require_once 'src/loader/vendor/autoload.php';

class DefaultAction
{
    public function __construct()
    {

    }

    public function execute(): string
    {
        //inserere un touite
        ////$pdo->query("INSERT INTO touite (idUtil,tailleT,datePubli,chemin,note,contenue) VALUES (1,36,07/11/23,'blabla',36,'Touite de test #SALUT #SAE');");

        $pdo = \touiteur\bd\ConnectionFactory::makeConnection();
        $s="";

        return $s;
    }
}
