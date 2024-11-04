<?php

namespace iutnc\deefy\action;

use Exception;
use iutnc\deefy\bd\ConnectionFactory;
use PDO;

class MesPlaylists {

    public function AffichePlaylist(string $username): string {
        $bd = ConnectionFactory::makeConnection();
        $stmt = $bd->prepare("SELECT playlist_id, titre, date_creation FROM playlists WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '<div class="container">';
        foreach ($playlists as $playlist) {
            // Conteneur cliquable sur toute la largeur, affichant la date de création à droite
            $html .= '<a href="?action=mesPlaylists&current_playlist_id=' . htmlspecialchars($playlist['playlist_id']) . '" class="playlist-container">';
            $html .= '<div class="playlist-content">';
            $html .= '<span class="playlist-title">' . htmlspecialchars($playlist['titre']) . '</span>';
            $html .= '<span class="playlist-date">Créée le : ' . htmlspecialchars($playlist['date_creation']) . '</span>';
            $html .= '</div></a>';
        }
        $html .= '</div>';

        return $html;
    }

    public function AffichePlaylistCourante(int $playlistId): string {
        $bd = ConnectionFactory::makeConnection();
        $stmt = $bd->prepare("SELECT titre FROM playlists WHERE playlist_id = :playlist_id");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$playlist) return "<p>Playlist introuvable.</p>";

        $html = '<div class="container"><h3>Playlist Courante : ' . htmlspecialchars($playlist['titre']) . '</h3><ul>';

        // Récupère toutes les pistes de la playlist courante
        $stmtTracks = $bd->prepare("SELECT titre, artiste FROM tracks WHERE playlist_id = :playlist_id");
        $stmtTracks->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmtTracks->execute();

        $tracks = $stmtTracks->fetchAll(PDO::FETCH_ASSOC);
        if (empty($tracks)) {
            $html .= '<li>Aucune piste pour le moment.</li>';
        } else {
            foreach ($tracks as $track) {
                $html .= '<li>' . htmlspecialchars($track['titre']) . ' - ' . htmlspecialchars($track['artiste']) . '</li>';
            }
        }

        $html .= '</ul></div>';
        return $html;
    }

    public function execute(): string {
        $s = '<div class="container">';

        // Affiche le bouton "Retour" seulement si une playlist courante est sélectionnée
        if (isset($_GET['current_playlist_id'])) {
            $s .= '<a class="back-button" href="?action=mesPlaylists">← Retour</a>';
        }

        $s .= "<h2>Mes Playlists</h2>";

        // Vérifie si l'utilisateur est connecté
        if (isset($_SESSION['connection']) && $_SESSION['connection'] instanceof \iutnc\deefy\compte\compteUtil) {
            try {
                $username = $_SESSION['connection']->__get('username');

                // Vérifie si un ID de playlist courante est spécifié dans l'URL
                if (isset($_GET['current_playlist_id'])) {
                    $currentPlaylistId = (int)$_GET['current_playlist_id'];
                    $_SESSION['current_playlist_id'] = $currentPlaylistId; // Stocke l'ID de la playlist courante en session
                    $s .= $this->AffichePlaylistCourante($currentPlaylistId);
                } else {
                    $s .= $this->AffichePlaylist($username); // Affiche toutes les playlists
                }
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
