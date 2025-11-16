<?php

namespace app\controllers\admin;

use app\models\admin\DashboardModel;
use Flight;
use Exception;

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
     * Refuse un congé (pour RH)
     */
    public function refuserConge($id_conge) {
        if ($id_conge === null || empty($id_conge)) {
            $_SESSION['error_message'] = 'Erreur: ID du congé manquant pour le refus.';
            Flight::redirect('/rh/dashboard');
            return;
        }
        
        try {
            $this->model->refuserConge($id_conge);
            $_SESSION['success_message'] = 'Le congé a été refusé avec succès.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors du refus du congé: ' . $e->getMessage();
        }
        
        Flight::redirect('/rh/dashboard');
    }

    /**
     * Affiche les détails d'un congé (pour RH)
     */
    public function voirDetailsConge($id_conge) {
        if ($id_conge === null || empty($id_conge)) {
            $_SESSION['error_message'] = 'Erreur: ID du congé manquant pour voir les détails.';
            Flight::redirect('/rh/dashboard');
            return;
        }
        
        try {
            // Pour RH, on passe 1 comme id_departement pour voir tous les congés
            $details_conge = $this->model->voirDetailsConge($id_conge, 1);
            Flight::render('admin/conge_details', [
                'details_conge' => $details_conge
            ]);
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la récupération des détails du congé: ' . $e->getMessage();
            Flight::redirect('/rh/dashboard');
        }
    }

    /**
     * Modifie un congé (pour RH)
     */
    public function modifierConge($id_conge) {
        if ($id_conge === null || empty($id_conge)) {
            $_SESSION['error_message'] = 'Erreur: ID du congé manquant pour la modification.';
            Flight::redirect('/rh/dashboard');
            return;
        }
        
        try {
            // Pour RH, on passe 1 comme id_departement pour voir tous les congés
            $details_conge = $this->model->voirDetailsConge($id_conge, 1);
            Flight::render('admin/conge_modifier', [
                'details_conge' => $details_conge
            ]);
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la récupération du congé à modifier: ' . $e->getMessage();
            Flight::redirect('/rh/dashboard');
        }
    }

    /**
     * Traite la modification d'un congé (pour RH)
     */
    public function traiterModifierConge($id_conge) {
        $date_debut = Flight::request()->data->date_debut ?? null;
        $date_fin = Flight::request()->data->date_fin ?? null;
        $type_conge = Flight::request()->data->type_conge ?? null;
        $motif = Flight::request()->data->motif ?? null;
        
        if ($id_conge === null || empty($id_conge) || $date_debut === null || $date_fin === null || $type_conge === null) {
            $_SESSION['error_message'] = 'Erreur: Tous les champs obligatoires doivent être remplis.';
            Flight::redirect('/rh/conges/modifier/' . $id_conge);
            return;
        }
        
        // Validation des dates
        $dateDebut = new \DateTime($date_debut);
        $dateFin = new \DateTime($date_fin);
        
        if ($dateFin < $dateDebut) {
            $_SESSION['error_message'] = 'Erreur: La date de fin ne peut pas être antérieure à la date de début.';
            Flight::redirect('/rh/conges/modifier/' . $id_conge);
            return;
        }
        
        try {
            // Calcul du nombre de jours
            $nb_jours = $dateDebut->diff($dateFin)->days + 1;
            
            // Modification du congé
            $this->model->modifierConge($id_conge, $date_debut, $date_fin, $type_conge, $motif, $nb_jours);
            $_SESSION['success_message'] = 'Le congé a été modifié avec succès.';
            Flight::redirect('/rh/dashboard');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la modification du congé: ' . $e->getMessage();
            Flight::redirect('/rh/conges/modifier/' . $id_conge);
        }
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
        // Récupérer les messages de la session
        $success_message = $_SESSION['success_message'] ?? null;
        $error_message = $_SESSION['error_message'] ?? null;
        
        // Effacer les messages après les avoir récupérés
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        
        Flight::render('admin/rh-dashboard', [
            'total_employes' => $statistiques['total_employes'],
            'conges_attente' => $statistiques['conges_attente'],
            'absences_aujourd_hui' => $statistiques['absences_aujourd_hui'],
            'presents_aujourd_hui' => $statistiques['presents_aujourd_hui'],
            'conges_en_attente' => $conges_en_attente,
            'success_message' => $success_message,
            'error_message' => $error_message
        ]);
    }
}
