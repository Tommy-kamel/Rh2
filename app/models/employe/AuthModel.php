<?php

namespace app\models\employe;

use Flight;

class AuthModel
{
    /**
     * Vérifie les credentials d'un employé
     */
    public function verifierCredentials($email)
    {
        $sql = "SELECT e.*, c.id_departement, d.nom_departement, p.nom as nom_poste
                FROM employe e
                LEFT JOIN contrat c ON e.id_employe = c.id_employe AND c.date_fin >= CURDATE()
                LEFT JOIN departement d ON c.id_departement = d.id_departement
                LEFT JOIN poste p ON c.id_poste = p.id_poste
                WHERE e.email = :email";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetch();
    }

    /**
     * Valide le mot de passe
     */
    public function validerMotDePasse($employe, $mot_de_passe)
    {
        if (!$employe) {
            return false;
        }
        
        return $employe['mot_de_passe'] === $mot_de_passe;
    }

    /**
     * Créer la session pour l'employé
     */
    public function creerSession($employe)
    {
        $_SESSION['user_id'] = $employe['id_employe'];
        $_SESSION['user_type'] = 'employe';
        $_SESSION['nom'] = $employe['nom'];
        $_SESSION['prenom'] = $employe['prenom'];
        $_SESSION['email'] = $employe['email'];
        $_SESSION['id_departement'] = $employe['id_departement'];
        $_SESSION['nom_departement'] = $employe['nom_departement'];
        $_SESSION['nom_poste'] = $employe['nom_poste'];
    }

    /**
     * Vérifie si l'employé est connecté
     */
    public function estConnecte()
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'employe';
    }
}
