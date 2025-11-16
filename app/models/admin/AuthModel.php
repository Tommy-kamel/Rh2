<?php

namespace app\models\admin;

use Flight;

class AuthModel
{
    /**
     * Vérifie les credentials d'un administrateur
     */
    public function verifierCredentials($nom_utilisateur)
    {
        $sql = "SELECT u.*, d.nom_departement 
                FROM user u
                LEFT JOIN departement d ON u.id_departement = d.id_departement
                WHERE u.nom_utilisateur = :nom_utilisateur";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['nom_utilisateur' => $nom_utilisateur]);
        
        return $stmt->fetch();
    }

    /**
     * Valide le mot de passe
     */
    public function validerMotDePasse($user, $mot_de_passe)
    {
        if (!$user) {
            return false;
        }
        
        return $user['mot_de_passe'] === $mot_de_passe;
    }

    /**
     * Créer la session pour l'administrateur
     */
    public function creerSession($user)
    {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_type'] = 'admin';
        $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
        $_SESSION['id_departement'] = $user['id_departement'];
        $_SESSION['nom_departement'] = $user['nom_departement'];
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    public function estConnecte()
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin';
    }
}
