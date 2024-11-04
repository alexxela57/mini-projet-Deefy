<?php

namespace iutnc\deefy\compte;

class compteUtil
{
    // Nom d'utilisateur
    private string $username;

    // Adresse email de l'utilisateur
    private string $email;

    // Rôle de l'utilisateur (STANDARD ou ADMIN)
    private string $role;

    public function __construct(string $username, string $email, string $role) {
        if (strlen($username) > 50) {
            echo '<script>window.alert("Nom d\'utilisateur trop long (max 50 caractères)")</script>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            echo '<script>window.alert("Email non valide ou trop long (max 100 caractères)")</script>';
        } elseif (!in_array($role, ['STANDARD', 'ADMIN'])) {
            echo '<script>window.alert("Rôle invalide")</script>';
        } else {
            $this->username = $username;
            $this->email = $email;
            $this->role = $role;
        }
    }

    /**
     * Méthode magique __get pour récupérer les propriétés protégées
     * @param string $at Nom de la propriété à récupérer
     * @return mixed Valeur de la propriété
     * @throws \Exception Si la propriété est invalide
     */
    public function __get(string $at): mixed
    {
        if (property_exists($this, $at)) {
            return $this->$at;
        } else {
            throw new \Exception("$at: propriété invalide");
        }
    }
}
