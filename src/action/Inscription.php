<?php

namespace iutnc\deefy\action;

use iutnc\deefy\exception\CompteException;
use iutnc\deefy\bd\ConnectionFactory;
use PDO;
use Exception;

class Inscription
{
    /**
     * Vérifie la force du mot de passe
     */
    public function checkPasswordStrength(string $pass, int $minimumLength = 8): bool
    {
        $length = strlen($pass) >= $minimumLength;
        $digit = preg_match("#[0-9]#", $pass);
        $special = preg_match("#[\W]#", $pass);
        $lower = preg_match("#[a-z]#", $pass);
        $upper = preg_match("#[A-Z]#", $pass);
        return $length && $digit && $special && $lower && $upper;
    }

    /**
     * Crée un nouveau compte utilisateur
     */
    public function CreerCompte($username, $email, $password, $role = 'STANDARD')
    {
//        if (!$this->checkPasswordStrength($password)) {
//            throw new CompteException("Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
//        }

        // Connexion à la base de données
        $bd = ConnectionFactory::makeConnection();

        try {
            // Vérifie si l'utilisateur avec cet email ou nom d'utilisateur existe déjà
            $stmt = $bd->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->fetch()) {
                throw new CompteException("Un compte avec cet e-mail ou ce nom d'utilisateur existe déjà.");
            }

            // Encode le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insère le nouvel utilisateur dans la base de données
            $stmt = $bd->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->execute();

        } catch (Exception $e) {
            throw new CompteException("Erreur lors de l'inscription : " . $e->getMessage());
        }
    }

    /**
     * Gère le processus d'inscription et affiche le formulaire
     */
    public function execute(): string
    {
        $bd = ConnectionFactory::makeConnection();
        $s = '<div class="container">';
        $s .= "<h2>Inscription</h2>";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"] ?? '';
            $email = $_POST["email"] ?? '';
            $password = $_POST["password"] ?? '';

            try {
                $this->CreerCompte($username, $email, $password);
                $s .= "<p>Inscription réussie !</p>";
            } catch (CompteException $e) {
                $s .= "<p>Erreur : " . $e->getMessage() . "</p>";
            }
        }

        // Formulaire HTML
        $s .= '<form id="f1" action="?action=inscription" method="post">
                <input type="text" name="username" placeholder="Nom d\'utilisateur" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Valider</button>
               </form>';
        $s .= '</div>';

        return $s;
    }
}
