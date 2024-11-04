<?php

namespace action;

use exception\CompteException;
use touiteur\bd\ConnectionFactory;

require_once 'vendor/autoload.php';

class Inscription
{
    private $email;
    private $passwd;
    private $role;
    private $nom;
    private $prenom;


    public function checkPasswordStrength(string $pass,
                                          int $minimumLength): bool {

        $length = (strlen($pass) < $minimumLength); // longueur minimale
        $digit = preg_match("#[\d]#", $pass); // au moins un digit
        $special = preg_match("#[\W]#", $pass); // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule
        if (!$length || !$digit || !$special || !$lower || !$upper)return false;
        return true;
    }

    public function CreerCompte($nom,$prenom,$email, $passwd, $role=1){
        // Vérifie la qualité du mot de passe
        if ($this->checkPasswordStrength($passwd,3)) {
            throw new CompteException("Le mot de passe doit avoir au moins 3 caractères.");
        }

        // Vérifie si l'utilisateur avec cet email existe déjà
        $bd = ConnectionFactory::makeConnection();
        $st = $bd->prepare("SELECT * FROM email WHERE adresseUtil = '".$email."'");
        $st->execute();
        $existingUser = $st->fetch();

        if ($existingUser) {
            throw new CompteException("Un compte avec cet email existe déjà.");
        }

        // Encode le mot de passe
        $hashedPassword = password_hash($passwd, PASSWORD_DEFAULT);

        // Insère le nouvel utilisateur dans la base de données

        $st = $bd->prepare("INSERT INTO utilisateur (nomUtil,prenomUtil,mdpUtil) VALUES ('".$nom."','".$prenom."','".$hashedPassword."')");
        $st->execute();

        $st = $bd->prepare("INSERT INTO email (idUtil,adresseUtil) VALUES ((SELECT idUtil FROM utilisateur where nomUtil ='".$nom."'),'".$email."')");
        $st->execute();

    }

    public function execute():string
    {
        $bd = ConnectionFactory::makeConnection();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $n = $_POST["Nom"];
            $p = $_POST["Prenom"];
            $e = $_POST["email"];
            $pwd = $_POST["Password"];
            self::CreerCompte($n,$p,$e,$pwd);
            $s = "ça marche bien";
        }
        $s = '<div class="container">';
        $s = $s . "<h2>Inscription</h2>";
        $s .= '<form id="f1" action="?action=inscription" method="post">
                <input type="text" name = "Nom" placeholder="<Nom>" >
                <input type="text" name = "Prenom" placeholder="<Prenom>" >
                <input type="text" name = "email" placeholder="<email>" >
                <input type="password" name = "Password" placeholder="<Password>">
                <button type="submit">Valider</button>
              </form>';
        $s .= '</div>';
        return $s;
    }
}