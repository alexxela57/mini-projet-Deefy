<?php

namespace iutnc\deefy\action;

class Deconnexion
{

    public static function deconnexion()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'deconnexion') {
            session_unset();
            session_destroy();
            header("Location: ?"); // Redirige vers la page d'accueil ou une autre page après la déconnexion
            exit();
        }
    }

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

