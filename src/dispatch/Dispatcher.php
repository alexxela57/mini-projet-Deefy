<?php

namespace dispatch;

use \action as Action;

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
                $action_class = new Action\Connexion();
                break;
            case("inscription"):
                $action_class = new Action\Inscription();
                break;
            default:
                $action_class = new Action\defaultAction();
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





