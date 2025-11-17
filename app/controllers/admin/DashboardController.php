<?php

namespace app\controllers\admin;

use app\models\admin\DashboardModel;
use Flight;
use Exception;

class DashboardController
{
    private $model;

    public function __construct()
    {
        $this->model = new DashboardModel();
    }

    /**
     * Affiche la liste complète des congés en attente
     */
    public function listeCongesAttente()
    {
        if (!$this->verifierAcces()) {
            return;
        }

        $id_departement = $_SESSION['id_departement'] ?? null;
        
        // Si c'est RH (id_departement = 1), passer null pour voir tous les départements
        if ($id_departement === 1) {
            $conges_en_attente = $this->model->listeCongeEnAttente(null);
        } else {
            $conges_en_attente = $this->model->listeCongeEnAttente($id_departement);
        }
        
        // Récupérer les messages de la session
        $success_message = $_SESSION['success_message'] ?? null;
        $error_message = $_SESSION['error_message'] ?? null;
        
        // Effacer les messages après les avoir récupérés
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        
        Flight::render('admin/conges_attente', [
            'conges_en_attente' => $conges_en_attente,
            'success_message' => $success_message,
            'error_message' => $error_message
        ]);
    }

    public function listeCongesHistorique()
    {
        if (!$this->verifierAcces()) {
            return;
        }

        $id_departement = $_SESSION['id_departement'] ?? null;
        
        // Si c'est RH (id_departement = 1), passer null pour voir tous les départements
        if ($id_departement == 1) {
            $historique_conges = $this->model->historiqueConges(null);
        } else {
            $historique_conges = $this->model->historiqueConges($id_departement);
        }
        
        // Récupérer les messages de la session
        $success_message = $_SESSION['success_message'] ?? null;
        $error_message = $_SESSION['error_message'] ?? null;
        
        // Effacer les messages après les avoir récupérés
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        
        Flight::render('admin/conges_historique', [
            'historique_conges' => $historique_conges,
            'success_message' => $success_message,
            'error_message' => $error_message
        ]);
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

    public function validerConge() {
        $id_conge = Flight::request()->data->id_conge;
        $id_departement = $_SESSION['id_departement'] ?? null;
        
        if ($id_conge === null || $id_departement === null) {
            $_SESSION['error_message'] = 'Erreur: Paramètres manquants pour valider le congé.';
            Flight::redirect('/dashboard');
            return;
        }
        
        try {
            $this->model->validerConge($id_conge, $id_departement);
            $_SESSION['success_message'] = 'Le congé a été validé avec succès.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la validation du congé: ' . $e->getMessage();
        }
        
        if ($id_departement === 1) {
            Flight::redirect('/rh/dashboard');
        } else {
            Flight::redirect('/dashboard');
        }
    }

    public function refuserConge($id_conge) {
        $id_departement = $_SESSION['id_departement'] ?? null;
        
        if ($id_conge === null || empty($id_conge) || $id_departement === null) {
            $_SESSION['error_message'] = 'Erreur: Paramètres manquants pour refuser le congé.';
            Flight::redirect('/dashboard');
            return;
        }
        
        try {
            $this->model->refuserConge($id_conge);
            $_SESSION['success_message'] = 'Le congé a été refusé avec succès.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors du refus du congé: ' . $e->getMessage();
        }
        
        if ($id_departement === 1) {
            Flight::redirect('/rh/dashboard');
        } else {
            Flight::redirect('/dashboard');
        }
    }

    public function voirDetailsConge($id_conge) {
        $id_departement = $_SESSION['id_departement'] ?? null;
        
        if ($id_conge === null || empty($id_conge) || $id_departement === null) {
            $_SESSION['error_message'] = 'Erreur: Paramètres manquants pour voir les détails du congé.';
            Flight::redirect('/dashboard');
            return;
        }
        
        try {
            $details_conge = $this->model->voirDetailsConge($id_conge, $id_departement);
            Flight::render('admin/conge_details', [
                'details_conge' => $details_conge
            ]);
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la récupération des détails du congé: ' . $e->getMessage();
            if ($id_departement === 1) {
                Flight::redirect('/rh/dashboard');
            } else {
                Flight::redirect('/dashboard');
            }
        }
    }

    public function modifierConge($id_conge) {
        $id_departement = $_SESSION['id_departement'] ?? null;
        
        if ($id_conge === null || empty($id_conge) || $id_departement === null) {
            $_SESSION['error_message'] = 'Erreur: Paramètres manquants pour modifier le congé.';
            Flight::redirect('/dashboard');
            return;
        }
        
        try {
            $details_conge = $this->model->voirDetailsConge($id_conge, $id_departement);
            Flight::render('admin/conge_modifier', [
                'details_conge' => $details_conge
            ]);
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la récupération du congé à modifier: ' . $e->getMessage();
            if ($id_departement === 1) {
                Flight::redirect('/rh/dashboard');
            } else {
                Flight::redirect('/dashboard');
            }
        }
    }

    public function traiterModifierConge($id_conge) {
        $id_departement = $_SESSION['id_departement'] ?? null;
        $date_debut = Flight::request()->data->date_debut ?? null;
        $date_fin = Flight::request()->data->date_fin ?? null;
        $type_conge = Flight::request()->data->type_conge ?? null;
        $motif = Flight::request()->data->motif ?? null;
        
        if ($id_conge === null || empty($id_conge) || $date_debut === null || $date_fin === null || $type_conge === null || $id_departement === null) {
            $_SESSION['error_message'] = 'Erreur: Tous les champs obligatoires doivent être remplis.';
            Flight::redirect('/admin/conges/modifier/' . $id_conge);
            return;
        }
        
        // Validation des dates
        $dateDebut = new \DateTime($date_debut);
        $dateFin = new \DateTime($date_fin);
        
        if ($dateFin < $dateDebut) {
            $_SESSION['error_message'] = 'Erreur: La date de fin ne peut pas être antérieure à la date de début.';
            Flight::redirect('/admin/conges/modifier/' . $id_conge);
            return;
        }
        
        try {
            // Calcul du nombre de jours
            $nb_jours = $dateDebut->diff($dateFin)->days + 1;
            
            // Modification du congé
            $this->model->modifierConge($id_conge, $date_debut, $date_fin, $type_conge, $motif, $nb_jours);
            $_SESSION['success_message'] = 'Le congé a été modifié avec succès.';
            if ($id_departement === 1) {
                Flight::redirect('/rh/dashboard');
            } else {
                Flight::redirect('/dashboard');
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la modification du congé: ' . $e->getMessage();
            Flight::redirect('/admin/conges/modifier/' . $id_conge);
        }
    }

    /**
     * Rend la vue du dashboard avec les données
     */
    private function rendreDashboard($statistiques, $conges_en_attente)
    {
        // Récupérer les messages de la session
        $success_message = $_SESSION['success_message'] ?? null;
        $error_message = $_SESSION['error_message'] ?? null;
        
        // Effacer les messages après les avoir récupérés
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        
        Flight::render('admin/dashboard', [
            'total_employes' => $statistiques['total_employes'],
            'conges_attente' => $statistiques['conges_attente'],
            'absences_aujourd_hui' => $statistiques['absences_aujourd_hui'],
            'presents_aujourd_hui' => $statistiques['presents_aujourd_hui'],
            'conges_en_attente' => $conges_en_attente,
            'success_message' => $success_message,
            'error_message' => $error_message
        ]);
    }

    public function getSuggestionsConge($id_conge) {
        $id_departement = $_SESSION['id_departement'] ?? null;
        
        if ($id_conge === null || empty($id_conge) || $id_departement === null) {
            Flight::json(['error' => 'Paramètres manquants'], 400);
            return;
        }
        
        try {
            // Récupérer les détails du congé
            $congeDetails = $this->model->voirDetailsConge($id_conge, $id_departement);
            if (!$congeDetails) {
                Flight::json(['error' => 'Congé non trouvé'], 404);
                return;
            }
            
            // Récupérer tous les congés en attente pour contexte
            $tousCongesAttente = $this->model->listeCongeEnAttente($id_departement == 1 ? null : $id_departement);
            
            // Récupérer tous les congés validés futurs pour détecter les chevauchements
            $congesValidesFuturs = $this->model->listeCongeValidesApresAujourdHui($id_departement == 1 ? null : $id_departement);
            
            // Appeler Gemini
            require_once __DIR__ . '/../../services/GeminiService.php';
            $geminiService = new \GeminiService();
            $suggestion = $geminiService->suggererActionsPourConge($congeDetails, $tousCongesAttente, $congesValidesFuturs);
            
            error_log("Suggestion reçue: " . substr($suggestion, 0, 200));
            
            Flight::json(['suggestion' => $suggestion]);
        } catch (Exception $e) {
            error_log("Erreur getSuggestionsConge: " . $e->getMessage());
            Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
}
