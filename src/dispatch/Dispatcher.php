<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\connexion as connexion;
use iutnc\deefy\action\playlist as playlist;
use iutnc\deefy\action as action;

class Dispatcher
{
    private string $action = "";

    public function __construct()
    {
        if (isset($_GET["action"])) {
            $this->action = $_GET["action"];
        }

    }

    public function run(): void
    {
        switch ($this->action) {
            case("connexion"):
                $action_class = new connexion\Connexion();
                break;
            case("inscription"):
                $action_class = new connexion\Inscription();
                break;
            case("deconnexion"):
                $action_class = new connexion\Deconnexion();
                break;
            case("mesPlaylists"):
                $action_class = new playlist\MesPlaylists();
                break;
            case("creerPlaylist"):
                $action_class = new playlist\CreerPlaylist();
                break;
            default:
                $action_class = new action\defaultAction();
                break;

        }

        $html = $action_class->execute();

        $this->renderPage($html);
    }

    private function renderPage(string $html): void
    {
        echo $html;
    }
}





