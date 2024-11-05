<?php

namespace iutnc\deefy\action\connexion;

class Deconnexion
{

    /**
     * methode qui permet de déconnecter un utilisateur
     * @return void
     */
    public static function deconnexion()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'deconnexion') {
            session_unset();
            session_destroy();
            header("Location: ?");
            exit();
        }
    }

    /**
     * methode qui s'active quand le bouton est cliqué
     * @return string
     */
    public function execute(): string
    {
        try {
            self::deconnexion();
        } catch (CompteException $e) {
            $message = $e->getMessage();
        }

        return "deconnexion";
    }
}

