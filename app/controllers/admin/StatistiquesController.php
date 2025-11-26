<?php

namespace app\controllers\admin;

use Flight;
use app\models\admin\StatistiquesModel;

class StatistiquesController
{
    private $statistiquesModel;

    public function __construct()
    {
        $this->statistiquesModel = new StatistiquesModel();
    }

    /**
     * Affiche la page principale des statistiques
     * Pour RH: affiche toutes les statistiques
     * Pour autres départements: affiche uniquement les statistiques du département
     */
    public function afficherStatistiques()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_type'])) {
            Flight::redirect('/login');
            return;
        }

        // Déterminer si c'est un RH ou un chef de département
        $user_type = $_SESSION['user_type'];
        $nom_departement = $_SESSION['nom_departement'] ?? '';
        $id_departement = null;
        $is_rh = ($nom_departement === 'Ressources Humaines');
        
        // Si ce n'est pas RH, on filtre par département
        if (!$is_rh) {
            $id_departement = $_SESSION['id_departement'] ?? null;
            
            if ($id_departement === null) {
                Flight::redirect('/dashboard');
                return;
            }
        }

        // Récupérer toutes les statistiques avec ou sans filtre département
        $date_debut = $_GET['date_debut'] ?? date('Y-01-01');
        $date_fin = $_GET['date_fin'] ?? date('Y-12-31');
        
        $data = [
            'resume' => $this->statistiquesModel->getResumeEffectifs($id_departement),
            'effectifs_genre' => $this->statistiquesModel->getEffectifsParGenre($id_departement),
            'effectifs_age' => $this->statistiquesModel->getEffectifsParAge($id_departement),
            'effectifs_departement' => $this->statistiquesModel->getEffectifsParDepartement($id_departement),
            'effectifs_contrat' => $this->statistiquesModel->getEffectifsParTypeContrat($id_departement),
            'effectifs_poste' => $this->statistiquesModel->getEffectifsParPoste($id_departement),
            'effectifs_dept_genre' => $this->statistiquesModel->getEffectifsDepartementParGenre($id_departement),
            'evolution_effectifs' => $this->statistiquesModel->getEvolutionEffectifs($id_departement),
            'taux_turnover' => $this->statistiquesModel->getTauxTurnover($date_debut, $date_fin, null),
            'taux_absenteisme' => $this->statistiquesModel->getTauxAbsenteisme($date_debut, $date_fin, $id_departement),
            'anciennete_moyenne' => $this->statistiquesModel->getAncienneteMoyenne($id_departement),
            'is_rh' => $is_rh,
            'user_type' => $user_type,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ];

        // Préparer les données pour les graphiques (format JSON)
        $data['chart_genre'] = $this->prepareChartData($data['effectifs_genre'], 'genre', 'nombre');
        $data['chart_age'] = $this->prepareChartData($data['effectifs_age'], 'tranche_age', 'nombre');
        $data['chart_departement'] = $this->prepareChartData($data['effectifs_departement'], 'nom_departement', 'nombre');
        $data['chart_contrat'] = $this->prepareChartData($data['effectifs_contrat'], 'nom_type_contrat', 'nombre');

        Flight::render('admin/statistiques', $data);
    }

    /**
     * Prépare les données pour les graphiques Chart.js
     */
    private function prepareChartData($data, $labelKey, $valueKey)
    {
        $labels = [];
        $values = [];

        foreach ($data as $row) {
            $labels[] = $row[$labelKey];
            $values[] = $row[$valueKey];
        }

        return [
            'labels' => json_encode($labels),
            'values' => json_encode($values)
        ];
    }

    /**
     * API: Retourne les statistiques par genre (JSON)
     */
    public function getStatistiquesGenre()
    {
        $id_departement = $this->getIdDepartementIfNotRH();
        $data = $this->statistiquesModel->getEffectifsParGenre($id_departement);
        Flight::json($data);
    }

    /**
     * API: Retourne les statistiques par âge (JSON)
     */
    public function getStatistiquesAge()
    {
        $id_departement = $this->getIdDepartementIfNotRH();
        $data = $this->statistiquesModel->getEffectifsParAge($id_departement);
        Flight::json($data);
    }

    /**
     * API: Retourne les statistiques par département (JSON)
     */
    public function getStatistiquesDepartement()
    {
        $id_departement = $this->getIdDepartementIfNotRH();
        $data = $this->statistiquesModel->getEffectifsParDepartement($id_departement);
        Flight::json($data);
    }

    /**
     * API: Retourne les statistiques par type de contrat (JSON)
     */
    public function getStatistiquesContrat()
    {
        $id_departement = $this->getIdDepartementIfNotRH();
        $data = $this->statistiquesModel->getEffectifsParTypeContrat($id_departement);
        Flight::json($data);
    }

    /**
     * API: Retourne le résumé des effectifs (JSON)
     */
    public function getResumeEffectifs()
    {
        $id_departement = $this->getIdDepartementIfNotRH();
        $data = $this->statistiquesModel->getResumeEffectifs($id_departement);
        Flight::json($data);
    }

    /**
     * Méthode helper pour déterminer l'id_departement selon le type d'utilisateur
     */
    private function getIdDepartementIfNotRH()
    {
        $nom_departement = $_SESSION['nom_departement'] ?? '';
        
        if ($nom_departement === 'Ressources Humaines') {
            return null; // Pas de filtre pour RH
        }
        
        return $_SESSION['id_departement'] ?? null;
    }
}
