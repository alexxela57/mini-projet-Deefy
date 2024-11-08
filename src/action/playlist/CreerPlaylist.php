<?php

namespace iutnc\deefy\action\playlist;

use iutnc\deefy\bd\ConnectionFactory;
use PDO;
use Exception;
use iutnc\deefy\compte\compteUtil;

class CreerPlaylist
{
    /**
     * fonction qui cree une playlist vide
     * @param $titre
     * @param $username
     * @return void
     * @throws \iutnc\deefy\exception\CompteException
     */
    public function creerPlaylist($titre, $username)
    {
        $bd = ConnectionFactory::makeConnection();

        try {
            // Insère la nouvelle playlist dans la base de données
            $stmt = $bd->prepare("INSERT INTO playlists (titre, username) VALUES (:titre, :username)");
            $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            // Récupère l'ID de la nouvelle playlist et l'enregistre comme playlist courante
            $_SESSION['current_playlist_id'] = $bd->lastInsertId();

        } catch (Exception $e) {
            throw new Exception("Erreur lors de la création de la playlist : " . $e->getMessage());
        }
    }

    /**
     * fonction qui s'active lorsqu'on appuie sur le bouton
     * @return string
     */
    public function execute(): string
    {
        $s = '<div class="container">';
        $s .= "<h2>Créer une Playlist</h2>";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Utilisation de filter_var pour éviter les injections XSS
            $titre = filter_var($_POST["titre"] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (isset($_SESSION['connection']) && $_SESSION['connection'] instanceof compteUtil) {
                $username = $_SESSION['connection']->username;

                try {
                    $this->creerPlaylist($titre, $username);

                    // Rediriger vers l'affichage de la playlist courante
                    header("Location: ?action=mesPlaylists&current_playlist_id=" . $_SESSION['current_playlist_id']);
                    exit; // Terminez le script pour éviter d'afficher le reste de la page

                } catch (Exception $e) {
                    $s .= "<p>Erreur : " . $e->getMessage() . "</p>";
                }
            } else {
                $s .= "<p>Erreur : utilisateur non connecté. Veuillez vous connecter pour créer une playlist.</p>";
            }
        }

        $s .= '<form action="?action=creerPlaylist" method="post">
        <input class="ajoutForm" type="text" name="titre" placeholder="Titre de la playlist" required>
        <button id="creer" type="submit">Créer</button>
       </form>';
        $s .= '</div>';

        return $s;
    }
}
