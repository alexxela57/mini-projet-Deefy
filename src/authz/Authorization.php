<?php

use iutnc\deefy\compte\compteUtil;

class Authorization {
    private CompteUtil $user;

    public function __construct(CompteUtil $user) {
        $this->user = $user;
    }

    public static function checkRole(){

    }
}