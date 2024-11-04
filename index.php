<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Deefy</title>
    <link rel="stylesheet" type
    "text/css" href="style.css">

</head>


<body>
<header>
    <div class="d1"><h1>Deefy</h1></div>
    <div class="d2">
        <?php
        session_start();
        if (isset($_SESSION['connection'])) {
            $_SESSION['compteCourant']=$_SESSION['connection'];
            echo <<<BOUTON
                <a href="?action=pageCompte">
                    <button class="bouton">Page perso</button>
                </a>
                <a href="?action=ecrireTouite"
                    <button class="bouton">ecrire Un touite</button>
                </a>
                BOUTON;
        }else{

            echo <<<BOUTON
                <a href="?action=connexion">
                    <button class="bouton">connexion</button>
                </a>
                <a href="?action=inscription">
                    <button class="bouton">inscription</button>
                </a>
                BOUTON;
        }
        session_abort();
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

        require_once __DIR__ . '/src/loader/vendor/autoload.php';

        iutnc\deefy\bd\ConnectionFactory::setConfig("conf/bd.ini");

        $dispach = new \touiteur\dispatch\dispatcher();
        $dispach->run(); ?>
    </section>
</div>