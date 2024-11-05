<?php

namespace iutnc\deefy\action\playlist;

use Exception;
use iutnc\deefy\bd\ConnectionFactory;
use PDO;

class MesPlaylists {

    /**
     * fonction qui affiche la liste des playlistes
     * @param string $username
     * @return string
     * @throws \iutnc\deefy\exception\CompteException
     */
    public function AffichePlaylist(string $username): string {
        $bd = ConnectionFactory::makeConnection();

        // Récupérer toutes les playlists si l'utilisateur est admin, sinon filtrer par username
        $stmt = $bd->prepare("
        SELECT p.playlist_id, p.titre, p.date_creation, 
            CASE 
                WHEN :isAdmin = 1 THEN u.username 
                ELSE NULL 
            END AS created_by
        FROM playlists p
        LEFT JOIN users u ON p.username = u.username
        " . (isset($_SESSION['connection']) && $_SESSION['connection']->role !== 'ADMIN' ? ' WHERE p.username = :username' : '')
        );

        $isAdmin = (isset($_SESSION['connection']) && $_SESSION['connection']->role === 'ADMIN') ? 1 : 0;
        $stmt->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT);

        if (!isset($_SESSION['connection']) || $_SESSION['connection']->role !== 'ADMIN') {
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        }

        $stmt->execute();

        $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '<div class="container">';

        // Vérifie s'il y a des playlists
        if (empty($playlists)) {
            $html .= '<p>Vous n\'avez encore créé aucune playlist !</p>'; // Message à afficher si aucune playlist
        } else {
            foreach ($playlists as $playlist) {
                $html .= '<a href="?action=mesPlaylists&current_playlist_id=' . htmlspecialchars($playlist['playlist_id']) . '" class="playlist-container">';
                $html .= '<div class="playlist-content">';
                $html .= '<span class="playlist-title">' . htmlspecialchars($playlist['titre']) . '</span>';

                // Afficher "Créée par [USERNAME]" seulement pour les admins
                if (isset($_SESSION['connection']) && $_SESSION['connection']->role === 'ADMIN') {
                    $html .= '<span class="playlist-created-by">Créée par : ' . htmlspecialchars($playlist['created_by']) . '</span>';
                } else {
                    $html .= '<span class="playlist-date">Créée le : ' . htmlspecialchars($playlist['date_creation']) . '</span>'; // Affiche la date de création pour les utilisateurs standards
                }

                $html .= '</div></a>';
            }
        }

        $html .= '</div>';

        return $html;
    }


    /**
     * fonction qui affiche la playlist courante
     * @param int $playlistId
     * @return string
     * @throws \iutnc\deefy\exception\CompteException
     */
    public function AffichePlaylistCourante(int $playlistId): string {
        $bd = ConnectionFactory::makeConnection();
        $stmt = $bd->prepare("SELECT titre FROM playlists WHERE playlist_id = :playlist_id");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$playlist) return "<p>Playlist introuvable.</p>";

        $html = '<div class="container"><h3>' . htmlspecialchars($playlist['titre']) . '</h3><ul>';

        // Récupère toutes les pistes de la playlist courante à partir de la table stock
        $stmtTracks = $bd->prepare("
        SELECT t.titre, t.artiste 
        FROM stock s 
        JOIN tracks t ON s.track_titre = t.titre AND s.track_artiste = t.artiste 
        WHERE s.playlist_id = :playlist_id
    ");
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


    /**
     * fonction qui permet d'ajouter une piste a la playlist courante
     * @param int $playlistId
     * @return string
     */
    public function ajouterPiste(int $playlistId): string {
        $html = '<div class="container">';
        $html .= '<h3>Ajouter une musique</h3>';
        $html .= '<form method="post" action="?action=mesPlaylists&current_playlist_id=' . $playlistId . '&addTrack=1">';
        $html .= '<label for="titre">Titre :</label>';
        $html .= '<input class="ajoutForm" type="text" name="titre" id="titre" required>';
        $html .= '<label for="artiste">Artiste :</label>';
        $html .= '<input class="ajoutForm" type="text" name="artiste" id="artiste" required>';

        // Nouveau conteneur pour le bouton
        $html .= '<div class="button-container">';
        $html .= '<button type="submit" id="valider" class="bouton">Valider</button>'; // Ajout de la classe 'bouton'
        $html .= '</div>'; // Fin du conteneur
        $html .= '</form>';
        $html .= '</div>';
        return $html;
    }

    /**
     * methode qui permet de sauvegarder une piste dans la base de donnees
     * @param int $playlistId
     * @return void
     * @throws \iutnc\deefy\exception\CompteException
     */
    private function sauvegarderPiste(int $playlistId) {
        if (isset($_POST['titre']) && isset($_POST['artiste'])) {
            $titre = $_POST['titre'];
            $artiste = $_POST['artiste'];
            $bd = ConnectionFactory::makeConnection();

            // Vérifier si la piste existe déjà dans la table tracks
            $stmt = $bd->prepare("SELECT COUNT(*) FROM tracks WHERE titre = :titre AND artiste = :artiste");
            $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
            $stmt->bindParam(':artiste', $artiste, PDO::PARAM_STR);
            $stmt->execute();

            $exists = $stmt->fetchColumn();

            if ($exists > 0) {
                // La musique existe déjà, ajoutez la relation dans la table stock
                $stmtStock = $bd->prepare("INSERT IGNORE INTO stock (playlist_id, track_titre, track_artiste) VALUES (:playlist_id, :track_titre, :track_artiste)");
                $stmtStock->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
                $stmtStock->bindParam(':track_titre', $titre, PDO::PARAM_STR);
                $stmtStock->bindParam(':track_artiste', $artiste, PDO::PARAM_STR);
                $stmtStock->execute();
            } else {
                // Ajouter la nouvelle piste à la table tracks
                $stmt = $bd->prepare("INSERT INTO tracks (titre, artiste) VALUES (:titre, :artiste)");
                $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
                $stmt->bindParam(':artiste', $artiste, PDO::PARAM_STR);
                $stmt->execute();

                // Maintenant, ajouter la relation dans la table stock
                $stmtStock = $bd->prepare("INSERT INTO stock (playlist_id, track_titre, track_artiste) VALUES (:playlist_id, :track_titre, :track_artiste)");
                $stmtStock->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
                $stmtStock->bindParam(':track_titre', $titre, PDO::PARAM_STR);
                $stmtStock->bindParam(':track_artiste', $artiste, PDO::PARAM_STR);
                $stmtStock->execute();
            }
        }
    }


    /**
     * methode qui s'active lorsqu'on appuie sur le bouton
     * @return string
     */
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

                    // Redirige vers l'affichage de la playlist courante après l'ajout
                    header("Location: ?action=mesPlaylists&current_playlist_id=" . $currentPlaylistId);
                    exit; // Terminez le script pour éviter d'afficher le reste de la page
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
