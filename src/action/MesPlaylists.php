<?php

namespace iutnc\deefy\action;

use Exception;
use iutnc\deefy\bd\ConnectionFactory;
use PDO;

class MesPlaylists {

    public function AffichePlaylist(string $username): string {
        $bd = ConnectionFactory::makeConnection();
        $stmt = $bd->prepare("SELECT titre, date_creation FROM playlists WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '<div class="container"><ul>';
        foreach ($playlists as $playlist) {
            $html .= '<li>' . htmlspecialchars($playlist['titre']) . ' - Créée le : ' . htmlspecialchars($playlist['date_creation']) . '</li>';
        }
        $html .= '</ul></div>';

        return $html;
    }

    public function execute(): string {
        $s = '<div class="container">';
        $s .= "<h2>Mes Playlists</h2>";

        // Vérifie si l'utilisateur est connecté
        if (isset($_SESSION['connection']) && $_SESSION['connection'] instanceof \iutnc\deefy\compte\compteUtil) {
            try {
                $username = $_SESSION['connection']->__get('username');
                $s .= $this->AffichePlaylist($username);
            } catch (Exception $e) {
                $s .= "<p>Erreur : " . $e->getMessage() . "</p>";
            }
        } else {
            $s .= "<p>Vous devez être connecté pour voir vos playlists.</p>";
        }

        $s .= '</div>';
        return $s;
    }


}
