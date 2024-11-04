<?php

namespace iutnc\deefy\action;

use iutnc\deefy\bd\ConnectionFactory;
use PDO;
use Exception;
use iutnc\deefy\compte\compteUtil;

class CreerPlaylist
{
    public function creerPlaylist($titre, $username)
    {
        // Connexion à la base de données
        $bd = ConnectionFactory::makeConnection();

        try {
            // Insère la nouvelle playlist dans la base de données avec le titre et le username
            $stmt = $bd->prepare("INSERT INTO playlists (titre, username) VALUES (:titre, :username)");
            $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

        } catch (Exception $e) {
            throw new Exception("Erreur lors de la création de la playlist : " . $e->getMessage());
        }
    }

    /**
     * Gère le processus de création de playlist et affiche le formulaire
     */
    public function execute(): string
    {
        $s = '<div class="container">';
        $s .= "<h2>Créer une Playlist</h2>";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $titre = $_POST["titre"] ?? '';
            var_dump($_SESSION);

            // Vérification de la session de connexion de l'utilisateur
            if (isset($_SESSION['connection']) && $_SESSION['connection'] instanceof compteUtil) {
                // Utilisation de la méthode magique __get pour obtenir le nom d'utilisateur
                $username = $_SESSION['connection']->username; // Utilisation du getter

                try {
                    $this->creerPlaylist($titre, $username);
                    header("Location: ?action=menu");  // Redirection vers le menu
                    exit;
                } catch (Exception $e) {
                    $s .= "<p>Erreur : " . $e->getMessage() . "</p>";
                }
            } else {
                // Message d'erreur si l'utilisateur n'est pas connecté
                $s .= "<p>Erreur : utilisateur non connecté. Veuillez vous connecter pour créer une playlist.</p>";
            }
        }

        // Formulaire HTML
        $s .= '<form action="?action=creerPlaylist" method="post">
            <input type="text" name="titre" placeholder="Titre de la playlist" required>
            <button type="submit">Créer</button>
           </form>';
        $s .= '</div>';

        return $s;
    }
}
