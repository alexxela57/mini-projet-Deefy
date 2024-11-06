<?php
namespace iutnc\deefy\authz;

use iutnc\deefy\compte\compteUtil;
use iutnc\deefy\bd\ConnectionFactory;

class Authorization {
    private CompteUtil $user;

    public function __construct(CompteUtil $user) {
        $this->user = $user;
    }

    /**
     * Vérifie si l'utilisateur est un administrateur
     * @return bool
     */
    public function isAdmin(): bool {
        return $this->user->role === 'ADMIN';
    }

    /**
     * Vérifie si l'utilisateur peut voir la playlist
     * @param int $playlistId
     * @return bool
     */
    public function voitPlaylist(int $playlistId): bool {
        // Si l'utilisateur est administrateur, il peut voir toutes les playlists
        if ($this->isAdmin()) {
            return true;
        }

        // Si l'utilisateur n'est pas administrateur, vérifier s'il est le propriétaire de la playlist
        $bd = ConnectionFactory::makeConnection();

        // Récupérer le nom d'utilisateur
        $username = $this->user->getUsername();  // Stocker la valeur dans une variable

        // Rechercher si l'utilisateur possède la playlist en fonction de l'ID
        $stmt = $bd->prepare("SELECT COUNT(*) FROM playlists WHERE playlist_id = :playlist_id AND username = :username");
        $stmt->bindParam(':playlist_id', $playlistId, \PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);  // Passer la variable
        $stmt->execute();

        // Si le compte utilisateur possède cette playlist, retourner true
        return $stmt->fetchColumn() > 0;
    }
}
