<?php

namespace app\controllers\employe;

use app\models\employe\DashboardModel;
use Flight;

class DashboardController
{
    private $model;

    public function __construct()
    {
        $this->model = new DashboardModel();
    }

    /**
     * Affiche le dashboard employé
     */
    public function afficher()
    {
        if (!$this->verifierAcces()) {
            return;
        }

        $id_employe = $this->recupererIdEmploye();
        $donnees = $this->model->getDonneesDashboard($id_employe);
        
        $this->rendreDashboard($donnees);
    }

    /**
     * Vérifie si l'employé est connecté
     */
    private function verifierAcces()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employe') {
            Flight::redirect('/login');
            return false;
        }
        return true;
    }

    /**
     * Récupère l'ID de l'employé connecté
     */
    private function recupererIdEmploye()
    {
        return $_SESSION['user_id'];
    }

    /**
     * Rend la vue du dashboard avec les données
     */
    private function rendreDashboard($donnees)
    {
        Flight::render('employe/dashboard', [
            'dernieres_demandes' => $donnees['dernieres_demandes'],
            'derniers_pointages' => $donnees['derniers_pointages'],
            'solde_conges' => $donnees['solde_conges'],
            'heures_travaillees' => $donnees['heures_travaillees'],
            'demandes_attente' => $donnees['demandes_attente'],
            'nb_documents' => $donnees['nb_documents'],
            'type_conges' => $donnees['type_conges']
        ]);
    }
}
