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

        // Rediriger les RH vers leur dashboard spécifique
        if (isset($_SESSION['nom_departement']) && $_SESSION['nom_departement'] === 'Ressources Humaines') {
            Flight::redirect('/rh/dashboard');
            return;
        }

        $id_departement = $_SESSION['id_departement'] ?? null;
        $statistiques = $this->model->getStatistiques($id_departement);
        $conges_en_attente = $this->model->listeCongeEnAttente($id_departement);
        
        $this->rendreDashboard($statistiques, $conges_en_attente);
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
    private function rendreDashboard($statistiques, $conges_en_attente)
    {
        Flight::render('admin/dashboard', [
            'total_employes' => $statistiques['total_employes'],
            'conges_attente' => $statistiques['conges_attente'],
            'absences_aujourd_hui' => $statistiques['absences_aujourd_hui'],
            'presents_aujourd_hui' => $statistiques['presents_aujourd_hui'],
            'conges_en_attente' => $conges_en_attente
        ]);
    }
}
