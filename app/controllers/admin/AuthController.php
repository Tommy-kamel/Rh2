<?php

namespace app\controllers\admin;

use app\models\admin\AuthModel;
use Flight;

class AuthController
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    /**
     * Affiche la page de connexion admin
     */
    public function afficherPageConnexion()
    {
        // Si déjà connecté, rediriger
        if ($this->authModel->estConnecte()) {
            Flight::redirect('/dashboard');
            return;
        }

        Flight::render('login');
    }

    /**
     * Traite la connexion admin
     */
    public function traiterConnexion()
    {
        $nom_utilisateur = $this->recupererChamp('nom_utilisateur');
        $mot_de_passe = $this->recupererChamp('mot_de_passe');

        // Validation des champs
        if (!$this->validerChamps($nom_utilisateur, $mot_de_passe)) {
            Flight::render('login', ['error' => 'Veuillez remplir tous les champs']);
            return;
        }

        // Vérifier les credentials
        $user = $this->authModel->verifierCredentials($nom_utilisateur);

        // Valider le mot de passe
        if ($this->authModel->validerMotDePasse($user, $mot_de_passe)) {
            $this->authentificationReussie($user);
        } else {
            $this->authentificationEchouee();
        }
    }

    /**
     * Récupère un champ du formulaire
     */
    private function recupererChamp($champ)
    {
        return Flight::request()->data->$champ ?? '';
    }

    /**
     * Valide que les champs ne sont pas vides
     */
    private function validerChamps($nom_utilisateur, $mot_de_passe)
    {
        return !empty($nom_utilisateur) && !empty($mot_de_passe);
    }

    /**
     * Gère l'authentification réussie
     */
    private function authentificationReussie($user)
    {
        $this->authModel->creerSession($user);
        Flight::redirect('/dashboard');
    }

    /**
     * Gère l'authentification échouée
     */
    private function authentificationEchouee()
    {
        Flight::render('login', ['error' => 'Nom d\'utilisateur ou mot de passe incorrect']);
    }

    /**
     * Déconnexion
     */
    public function deconnexion()
    {
        session_destroy();
        Flight::redirect('/login');
    }
}
