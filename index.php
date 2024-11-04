<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Deefy</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<header>
    <div class="d1"><h1>Deefy</h1></div>
    <div class="d2">
        <?php
        require_once __DIR__ . '/src/loader/vendor/autoload.php';
        session_start();
        if (isset($_SESSION['connection'])) {
            $_SESSION['compteCourant'] = $_SESSION['connection'];
            echo <<<BOUTON
                <a href="?action=pageCompte">
                    <button class="bouton">Page perso</button>
                </a>
                <a href="?action=mesPlaylists">
                    <button class="bouton">Mes playlists</button>
                </a>
                <a href="?action=creerPlaylist">
                    <button class="bouton">Créer une playlist</button>
                </a>
                <a href="?action=deconnexion">
                    <button class="bouton">Déconnexion</button>
                </a>
            BOUTON;
        } else {
            echo <<<BOUTON
                <a href="?action=connexion">
                    <button class="bouton">Connexion</button>
                </a>
                <a href="?action=inscription">
                    <button class="bouton">Inscription</button>
                </a>
            BOUTON;
        }
        ?>
    </div>
</header>

<div class="main">
    <nav>
        <a href="?">
            <button class="boutonNav">Accueil</button>
        </a>
    </nav>
    <section>
        <br>

        <?php


        use iutnc\deefy\bd\ConnectionFactory;
        use iutnc\deefy\dispatch\Dispatcher;

        // Initialise la configuration de la base de données
        ConnectionFactory::setConfig(__DIR__ . '/conf/bd.ini');

        // Crée et exécute le dispatcher
        $dispatcher = new Dispatcher();
        $dispatcher->run();
        ?>
    </section>
</div>
</body>
</html>
