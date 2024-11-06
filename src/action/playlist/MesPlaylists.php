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

            // Vérifier si l'utilisateur a les droits nécessaires pour voir la playlist
            if (!isset($_SESSION['connection']) || $_SESSION['connection']->username !== $username && $_SESSION['connection']->role !== 'ADMIN') {
                throw new \iutnc\deefy\exception\AuthorizeException("Vous n'avez pas l'autorisation d'accéder à cette playlist.");
            }

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
            $username = filter_var($username, FILTER_SANITIZE_STRING);
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

            // Vérifier si l'utilisateur a l'autorisation d'accéder à la playlist
            $stmt = $bd->prepare("SELECT titre, username FROM playlists WHERE playlist_id = :playlist_id");
            $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
            $stmt->execute();
            $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$playlist) {
                throw new \iutnc\deefy\exception\AuthorizeException("Playlist introuvable.");
            }

            // Vérifier si l'utilisateur est le propriétaire ou administrateur
            if ($playlist['username'] !== $_SESSION['connection']->username && $_SESSION['connection']->role !== 'ADMIN') {
                throw new \iutnc\deefy\exception\AuthorizeException("Vous n'avez pas l'autorisation d'accéder à cette playlist.");
            }

            // Création du HTML pour l'affichage de la playlist
            $html = '<div class="container"><button class="bouton" onclick="toggleShuffle()">Jouer en Aléatoire</button><ul>';
            $html .= '<h3>' . htmlspecialchars($playlist['titre']) . '</h3>';

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
                $trackIndex = 0;
                foreach ($tracks as $track) {
                    $src = "./audio/" . $track['artiste'] . "¤" . $track["titre"] . ".mp3";
                    $html .= '<li>' . htmlspecialchars($track['titre']) . ' - ' . htmlspecialchars($track['artiste']);
                    $html .= '<br><audio id="audio' . $trackIndex . '" controls><source src="' . $src . '" type="audio/mp3"></audio></li>';
                    $trackIndex++;
                }

                // JavaScript pour le mode aléatoire et le rebouclage des pistes
                $html .= '
        <script>
            let isShuffle = false;
            const audios = Array.from(document.querySelectorAll("audio"));
            let currentAudioIndex = 0;

            // Mélanger les pistes de manière aléatoire
            function shuffleArray(array) {
                for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]];
                }
            }

            // Activer/désactiver le mode aléatoire
            function toggleShuffle() {
                isShuffle = !isShuffle;
                if (isShuffle) {
                    shuffleArray(audios);
                } else {
                    audios.sort((a, b) => parseInt(a.id.replace("audio", "")) - parseInt(b.id.replace("audio", "")));
                }
                currentAudioIndex = 0;
                playCurrentAudio();
            }

            // Jouer la piste courante
            function playCurrentAudio() {
                audios.forEach(audio => audio.pause());
                audios[currentAudioIndex].play();
            }

            // Gérer la fin de chaque piste
            audios.forEach((audio, index) => {
                audio.addEventListener("ended", function() {
                    if (isShuffle) {
                        currentAudioIndex = (currentAudioIndex + 1) % audios.length;
                    } else {
                        currentAudioIndex = index + 1 < audios.length ? index + 1 : 0;
                    }
                    playCurrentAudio();
                });
            });
        </script>
        ';
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
            $html .= '<form method="post" action="?action=mesPlaylists&current_playlist_id=' . $playlistId . '&addTrack=1" enctype="multipart/form-data">'; // ajout de enctype
            $html .= '<label for="titre">Titre :</label>';
            $html .= '<input class="ajoutForm" type="text" name="titre" id="titre" required>';
            $html .= '<br><label for="artiste">Artiste :</label>';
            $html .= '<input class="ajoutForm" type="text" name="artiste" id="artiste" required>';
            $html .= '<br><label for="fichier_mp3">Fichier MP3 :</label>';
            $html .= '<input class="ajoutForm" type="file" name="fichier_mp3" id="fichier_mp3" accept=".mp3" required>'; // champ pour le fichier MP3

            $html .= '<div class="button-container">';
            $html .= '<button type="submit" id="valider" class="bouton">Valider</button>';
            $html .= '</div>';
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
            if (isset($_POST['titre']) && isset($_POST['artiste']) && isset($_FILES['fichier_mp3'])) {
                $titre = filter_var($_POST['titre'], FILTER_SANITIZE_STRING); // Filtrage du titre de la piste
                $artiste = filter_var($_POST['artiste'], FILTER_SANITIZE_STRING); // Filtrage de l'artiste
                $playlistId = filter_var($playlistId, FILTER_VALIDATE_INT); // Validation de l'ID de la playlist
                $fichier = $_FILES['fichier_mp3'];

                // Définir le chemin complet pour le fichier MP3 avec le séparateur manquant
                $destination = __DIR__ . '../../../../audio/' . basename($artiste."¤".$titre.".mp3");

                // Déplacer le fichier uploadé
                if ($fichier['error'] === UPLOAD_ERR_OK && mime_content_type($fichier['tmp_name']) === 'audio/mpeg' && move_uploaded_file($fichier['tmp_name'], $destination)) {
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

                        // Ajouter la relation dans la table stock
                        $stmtStock = $bd->prepare("INSERT INTO stock (playlist_id, track_titre, track_artiste) VALUES (:playlist_id, :track_titre, :track_artiste)");
                        $stmtStock->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
                        $stmtStock->bindParam(':track_titre', $titre, PDO::PARAM_STR);
                        $stmtStock->bindParam(':track_artiste', $artiste, PDO::PARAM_STR);
                        $stmtStock->execute();
                    }
                } else {
                    echo "<p>Erreur lors de l'upload du fichier : " . $fichier['error'] . "</p>";
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
