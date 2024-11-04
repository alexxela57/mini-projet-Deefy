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

    public function execute(): string
    {
        $s = '<div class="container">';
        $s .= "<h2>Créer une Playlist</h2>";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $titre = $_POST["titre"] ?? '';

            if (isset($_SESSION['connection']) && $_SESSION['connection'] instanceof compteUtil) {
                $username = $_SESSION['connection']->username;

                try {
                    $this->creerPlaylist($titre, $username);

                    // Utiliser AffichePlaylistCourante pour afficher uniquement la playlist nouvellement créée
                    $mesPlaylists = new MesPlaylists();
                    $s = $mesPlaylists->AffichePlaylistCourante($_SESSION['current_playlist_id']);
                    return $s;  // Affiche la playlist courante directement
                } catch (Exception $e) {
                    $s .= "<p>Erreur : " . $e->getMessage() . "</p>";
                }
            } else {
                $s .= "<p>Erreur : utilisateur non connecté. Veuillez vous connecter pour créer une playlist.</p>";
            }
        }

        $s .= '<form action="?action=creerPlaylist" method="post">
            <input type="text" name="titre" placeholder="Titre de la playlist" required>
            <button type="submit">Créer</button>
           </form>';
        $s .= '</div>';

        return $s;
    }
}
