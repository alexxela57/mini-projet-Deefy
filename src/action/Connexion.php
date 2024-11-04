<?php

namespace iutnc\deefy\action;

use iutnc\deefy\exception\CompteException;
use iutnc\deefy\bd\ConnectionFactory;
use iutnc\deefy\compte\compteUtil;
use Exception;

class Connexion
{
    public static function connexion($email, $password) {
        $bd = ConnectionFactory::makeConnection();
        $st = $bd->prepare("SELECT * FROM users WHERE email = :email");
        $st->execute(['email' => $email]);

        $user = $st->fetch();

        try{$_SESSION['connection'] = new compteUtil($user['username'], $user['email'], $user['role']);
        }catch(Exception $e){
            throw new CompteException("La connexion a échoué. Vérifiez votre email et votre mot de passe.");
        }
    }

    public function execute(): string {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST['email'];
            $password = $_POST['Password'];
            try {
                self::connexion($email, $password);
                $message = "Connexion réussie !";
                header("Location: ?action=pageCompte");
                exit;
            } catch (CompteException $e) {
                $message = $e->getMessage();
            }
        }

        $form = '<div class="container">
                    <h2>Connexion</h2>
                    <form action="?action=connexion" method="post">
                        <input type="text" name="email" placeholder="Email" required>
                        <input type="password" name="Password" placeholder="Mot de passe" required>
                        <button type="submit">Valider</button>
                    </form>
                    <p>' . ($message ?? '') . '</p>
                 </div>';
        return $form;
    }
}
