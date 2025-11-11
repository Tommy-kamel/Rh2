<?php

namespace app\controllers\admin;

use app\models\admin\DashboardModel;
use Flight;

class DashboardController
{
    private $model;

    public function __construct()
    {
        $this->model = new DashboardModel();
    }

    /**
     * Affiche le dashboard admin
     */
    public function afficher()
    {
        if (!$this->verifierAcces()) {
            return;
        }

        $id_departement = $_SESSION['id_departement'] ?? null;
        $statistiques = $this->model->getStatistiques($id_departement);
        
        $this->rendreDashboard($statistiques);
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
     * Rend la vue du dashboard avec les données
     */
    private function rendreDashboard($statistiques)
    {
        Flight::render('admin/dashboard', [
            'total_employes' => $statistiques['total_employes'],
            'conges_attente' => $statistiques['conges_attente'],
            'absences_aujourd_hui' => $statistiques['absences_aujourd_hui'],
            'presents_aujourd_hui' => $statistiques['presents_aujourd_hui']
        ]);
    }
}
