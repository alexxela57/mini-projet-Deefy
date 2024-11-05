<?php

namespace iutnc\deefy\action\connexion;

use iutnc\deefy\exception\CompteException;
use iutnc\deefy\bd\ConnectionFactory;
use iutnc\deefy\compte\compteUtil;
use Exception;

class Connexion
{
    /**
     * methode qui permet a un utilisateur de se connecter
     * @param $username
     * @param $password
     * @return void
     * @throws CompteException
     */
    public static function connexion($username, $password) {
        $bd = ConnectionFactory::makeConnection();
        $st = $bd->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $st->execute(['username' => $username, 'password' => $password]);

        $user = $st->fetch();

        try{$_SESSION['connection'] = new compteUtil($user['username'], $user['email'], $user['role']);
        }catch(Exception $e){
            throw new CompteException("La connexion a échoué. Vérifiez votre email et votre mot de passe.");
        }
    }

    /**
     * methode qui s'execute quand le bouton est cliqué
     * @return string
     */
    public function execute(): string {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST['Username'];
            $password = $_POST['Password'];
            try {
                self::connexion($username, $password);
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
                        <input type="text" name="Username" placeholder="utilisateur" required>
                        <input type="password" name="Password" placeholder="Mot de passe" required>
                        <button type="submit">Valider</button>
                    </form>
                    <p>' . ($message ?? '') . '</p>
                 </div>';
        return $form;
    }
}