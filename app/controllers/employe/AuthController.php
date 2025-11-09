<?php

namespace app\controllers\employe;

use app\models\employe\AuthModel;
use Flight;

class AuthController
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    /**
     * Affiche la page de connexion employé
     */
    public function afficherPageConnexion()
    {
        // Si déjà connecté, rediriger
        if ($this->authModel->estConnecte()) {
            Flight::redirect('/employe/dashboard');
            return;
        }

        Flight::render('login');
    }

    /**
     * Traite la connexion employé
     */
    public function traiterConnexion()
    {
        $email = $this->recupererChamp('email');
        $mot_de_passe = $this->recupererChamp('mot_de_passe');

        // Validation des champs
        if (!$this->validerChamps($email, $mot_de_passe)) {
            Flight::render('login', ['error' => 'Veuillez remplir tous les champs']);
            return;
        }

        // Vérifier les credentials
        $employe = $this->authModel->verifierCredentials($email);

        // Valider le mot de passe
        if ($this->authModel->validerMotDePasse($employe, $mot_de_passe)) {
            $this->authentificationReussie($employe);
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
    private function validerChamps($email, $mot_de_passe)
    {
        return !empty($email) && !empty($mot_de_passe);
    }

    /**
     * Gère l'authentification réussie
     */
    private function authentificationReussie($employe)
    {
        $this->authModel->creerSession($employe);
        Flight::redirect('/employe/dashboard');
    }

    /**
     * Gère l'authentification échouée
     */
    private function authentificationEchouee()
    {
        Flight::render('login', ['error' => 'Email ou mot de passe incorrect']);
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
