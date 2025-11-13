<?php

namespace app\controllers\admin;

use app\models\admin\DashboardModel;
use Flight;

class RhDashboardController
{
    private $model;

    public function __construct()
    {
        $this->model = new DashboardModel();
    }

    /**
     * Affiche le dashboard RH
     */
    public function afficher()
    {
        if (!$this->verifierAccesRh()) {
            return;
        }

        // RH voit tous les départements (id_departement = null)
        $statistiques = $this->model->getStatistiques(null);
        $conges_en_attente = $this->model->listeCongeEnAttente(null);
        
        $this->rendreDashboardRh($statistiques, $conges_en_attente);
    }

    /**
     * Vérifie si l'utilisateur est RH
     */
    private function verifierAccesRh()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            Flight::redirect('/login');
            return false;
        }
        
        // Vérifier que l'utilisateur fait partie du département RH
        if (!isset($_SESSION['nom_departement']) || $_SESSION['nom_departement'] !== 'Ressources Humaines') {
            Flight::redirect('/dashboard');
            return false;
        }
        
        return true;
    }

    /**
     * Rend la vue du dashboard RH avec les données
     */
    private function rendreDashboardRh($statistiques, $conges_en_attente)
    {
        Flight::render('admin/rh-dashboard', [
            'total_employes' => $statistiques['total_employes'],
            'conges_attente' => $statistiques['conges_attente'],
            'absences_aujourd_hui' => $statistiques['absences_aujourd_hui'],
            'presents_aujourd_hui' => $statistiques['presents_aujourd_hui'],
            'conges_en_attente' => $conges_en_attente
        ]);
    }
}
