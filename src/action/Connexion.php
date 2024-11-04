<?php

namespace iutnc\deefy\action;

use exception\CompteException;
use touiteur\compte\compteUtil;
use touiteur\bd\ConnectionFactory;

require_once 'vendor/autoload.php';

class Connexion
{
    public static function Connexion($email, $password)
    {
        $bd = ConnectionFactory::makeConnection();
        $st = $bd->prepare("SELECT nomUtil,prenomUtil,adresseUtil,mdpUtil FROM email 
                                    INNER JOIN utilisateur ON email.idUtil = utilisateur.idUtil
                                    WHERE adresseUtil = '" . $email . "'");
        $st->execute();

        $user = $st->fetch();

        if ($user && password_verify($password, $user['mdpUtil'])) {

            // Authentification réussie, renvoyer l'utilisateur
            $_SESSION['connection'] = new compteUtil($user['nomUtil'], $user['prenomUtil'], $user['mdpUtil'], $user['adresseUtil']);

        } else {
            throw new CompteException("La connexion a échoué.");
        }
    }

    public function execute(): string
    {

        $s = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $pwd = $_POST['Passord'];
            $em = $_POST['email'];
            self::Connexion($em, $pwd);

            $s = "<a href='?action=pageCompte'>
                    <button class='bouton'>Page perso</button>
                </a>";
            $_SESSION['compteCourrant'] = $_SESSION['connection'];
        } else {
            $s = '<div class="container">';
            $s = $s . "<h2>Connexion</h2>";
            $s .= '<form action="?action=connexion" method="post"><input type="text" name="email" placeholder="<email>" >
              <input type="password" name="Passord" placeholder="<Password>" >
              <button type="submit">Valider</button></form>';
        }


        return $s;
    }
}