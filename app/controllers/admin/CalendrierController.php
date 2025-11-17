<?php

namespace app\controllers\admin;

use app\models\admin\CalendrierModel;
use Flight;

class CalendrierController
{
    private $model;

    public function __construct()
    {
        $this->model = new CalendrierModel();
    }

    /**
     * Affiche le calendrier
     */
    public function afficher()
    {
        if (!$this->verifierAcces()) {
            return;
        }

        $id_departement = $_SESSION['id_departement'] ?? null;
        
        // Récupérer le mois et l'année depuis les paramètres GET, sinon utiliser le mois actuel
        $mois = isset($_GET['mois']) ? (int)$_GET['mois'] : date('n');
        $annee = isset($_GET['annee']) ? (int)$_GET['annee'] : date('Y');
        
        // Validation des paramètres
        if ($mois < 1 || $mois > 12) {
            $mois = date('n');
        }
        if ($annee < 2000 || $annee > 2100) {
            $annee = date('Y');
        }
        
        // Récupérer les congés du mois
        $conges = $this->model->getCongesDuMois($mois, $annee, $id_departement);
        
        $this->rendreCalendrier($mois, $annee, $conges);
    }

    /**
     * Vérifie si l'admin est connecté
     */
    private function verifierAcces()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            Flight::redirect('/login');
            return false;
        }
        return true;
    }

    /**
     * Rend la vue du calendrier avec les données
     */
    private function rendreCalendrier($mois, $annee, $conges)
    {
        // Récupérer les messages de la session
        $success_message = $_SESSION['success_message'] ?? null;
        $error_message = $_SESSION['error_message'] ?? null;
        
        // Effacer les messages après les avoir récupérés
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        
        Flight::render('admin/calendrier', [
            'mois' => $mois,
            'annee' => $annee,
            'conges' => $conges,
            'success_message' => $success_message,
            'error_message' => $error_message
        ]);
    }
}
