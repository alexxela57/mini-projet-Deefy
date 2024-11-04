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

        $html = '<div class="container"><h3>' . htmlspecialchars($playlist['titre']) . '</h3><ul>';

        // Récupère toutes les pistes de la playlist courante
        $stmtTracks = $bd->prepare("SELECT titre, artiste FROM tracks WHERE playlist_id = :playlist_id");
        $stmtTracks->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmtTracks->execute();

        $tracks = $stmtTracks->fetchAll(PDO::FETCH_ASSOC);
        if (empty($tracks)) {
            $html .= '</ul><p>Aucune piste pour le moment.</p>';
        } else {
            foreach ($tracks as $track) {
                $html .= '<li>' . htmlspecialchars($track['titre']) . ' - ' . htmlspecialchars($track['artiste']) . '</li>';
            }
        }

        $html .= '</ul></div>';
        return $html;
    }

    public function ajouterPiste(int $playlistId): string {
        $html = '<div class="container">';
        $html .= '<h3>Ajouter une musique</h3>';
        $html .= '<form method="post" action="?action=mesPlaylists&current_playlist_id=' . $playlistId . '&addTrack=1">';
        $html .= '<label for="titre">Titre :</label>';
        $html .= '<input type="text" name="titre" id="titre" required>';
        $html .= '<label for="artiste">Artiste :</label>';
        $html .= '<input type="text" name="artiste" id="artiste" required>';
        $html .= '<button type="submit">Valider</button>';
        $html .= '</form>';
        $html .= '</div>';
        return $html;
    }

    private function sauvegarderPiste(int $playlistId) {
        if (isset($_POST['titre']) && isset($_POST['artiste'])) {
            $titre = $_POST['titre'];
            $artiste = $_POST['artiste'];
            $bd = ConnectionFactory::makeConnection();

            $stmt = $bd->prepare("INSERT INTO tracks (titre, artiste, playlist_id) VALUES (:titre, :artiste, :playlist_id)");
            $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
            $stmt->bindParam(':artiste', $artiste, PDO::PARAM_STR);
            $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);

            $stmt->execute();
        }
    }

    public function execute(): string {
        $s = '<div class="container">';

        // Affiche le bouton "Retour" seulement si une playlist courante est sélectionnée
        if (isset($_GET['current_playlist_id'])) {
            $s .= '<a class="back-button" href="?action=mesPlaylists">← Retour</a>';
            $s .= '<a class="bouton" href="?action=mesPlaylists&current_playlist_id=' . $_GET['current_playlist_id'] . '&addTrack=1">Ajouter musique</a>';
        }

        $s .= "<h2>Mes Playlists</h2>";

        if (isset($_SESSION['connection']) && $_SESSION['connection'] instanceof \iutnc\deefy\compte\compteUtil) {
            try {
                $username = $_SESSION['connection']->__get('username');

                // Si une piste doit être ajoutée (formulaire d'ajout de piste affiché)
                if (isset($_GET['addTrack']) && isset($_GET['current_playlist_id'])) {
                    $currentPlaylistId = (int)$_GET['current_playlist_id'];
                    $s .= $this->ajouterPiste($currentPlaylistId);
                }

                // Si les données du formulaire sont envoyées pour ajouter une piste
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['current_playlist_id'])) {
                    $currentPlaylistId = (int)$_GET['current_playlist_id'];
                    $this->sauvegarderPiste($currentPlaylistId);
                }

                // Affiche la playlist courante ou toutes les playlists de l'utilisateur
                if (isset($_GET['current_playlist_id'])) {
                    $currentPlaylistId = (int)$_GET['current_playlist_id'];
                    $_SESSION['current_playlist_id'] = $currentPlaylistId;
                    $s .= $this->AffichePlaylistCourante($currentPlaylistId);
                } else {
                    $s .= $this->AffichePlaylist($username);
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
