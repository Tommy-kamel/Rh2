<?php

namespace app\controllers\employe;

use app\models\employe\CongeModel;
use app\models\employe\DashboardModel;
use app\models\employe\NotificationModel;
use Flight;
use DateTime;
use Exception;

class CongeController
{
    private $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function addConge(){
        $id_employe = $_SESSION['user_id'];
        $id_type_conge = Flight::request()->data['type_conge'];
        $date_demande = date('Y-m-d');
        $date_debut = Flight::request()->data['date_debut'];
        $duree = Flight::request()->data['duree'];
        $raison = Flight::request()->data['raison'] ?? null;

        // Validation de la date (minimum 14 jours à l'avance)
        $validation = $this->validerDelaiPrevenance($date_demande, $date_debut);
        if (!$validation['valide']) {
            // Stocker le message d'erreur en session
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => $validation['message']
            ];
            Flight::redirect('/employe/dashboard');
            return;
        }

        // Vérifier si l'employé a assez de jours de congé restants
        $dashboardModel = new DashboardModel();
        $joursRestants = $dashboardModel->getNombreJourCongeRestantAnnee($id_employe);
        
        if ($duree > $joursRestants && $id_type_conge == 1) { // Supposons que le type_conge 1 est pour les congés annuels
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => "Vous ne disposez que de $joursRestants jour(s) de congé restants. Durée demandée : $duree jour(s)."
            ];
            Flight::redirect('/employe/dashboard');
            return;
        }

        // Calcul de la date de fin
        $date_fin = $this->calculerDateFin($date_debut, $duree);

        // Insertion dans la base de données
        $congeModel = new CongeModel();
        $result = $congeModel->addConge($id_employe, $id_type_conge, $date_demande, $date_debut, $date_fin, $raison, null);

        if ($result) {
            // Stocker le message de succès en session
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Demande de congé soumise avec succès ! Elle sera examinée par votre responsable.'
            ];
        } else {
            // Stocker le message d'erreur en session
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Erreur lors de la soumission de la demande. Veuillez réessayer.'
            ];
        }

        Flight::redirect('/employe/dashboard');
    }

    /**
     * Affiche la liste des congés de l'employé
     */
    public function liste() {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employe') {
            Flight::redirect('/login');
            return;
        }

        $id_employe = $_SESSION['user_id'];
        $dashboardModel = new DashboardModel();
        $demandes = $dashboardModel->getToutesDemandesConge($id_employe);

        Flight::render('employe/conges_liste', [
            'demandes' => $demandes
        ]);
    }

    /**
     * Valide que la date de début est au moins 14 jours après la date de demande
     */
    private function validerDelaiPrevenance($date_demande, $date_debut) {
        try {
            $demande = new DateTime($date_demande);
            $debut = new DateTime($date_debut);

            $diff = $demande->diff($debut);
            $jours_difference = $diff->days;

            if ($debut <= $demande) {
                return [
                    'valide' => false,
                    'message' => 'La date de début doit être dans le futur'
                ];
            }

            if ($jours_difference < 14) {
                return [
                    'valide' => false,
                    'message' => "Vous devez demander votre congé au moins 14 jours à l'avance. Il ne reste que {$jours_difference} jour(s)."
                ];
            }

            return [
                'valide' => true,
                'message' => 'Validation réussie'
            ];

        } catch (Exception $e) {
            return [
                'valide' => false,
                'message' => 'Erreur dans le format des dates'
            ];
        }
    }

    /**
     * Retourne le nombre de notifications non lues pour l'employé connecté
     */
    public function getUnreadNotificationsCount() {
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $id_employe = $_SESSION['user_id'];
        $count = $this->notificationModel->getUnreadNotifications($id_employe);
        
        Flight::json(['count' => $count]);
    }

    /**
     * Retourne la dernière notification non lue pour l'employé connecté
     */
    public function getLatestNotification() {
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $id_employe = $_SESSION['user_id'];
        $notifications = $this->notificationModel->getNotifications($id_employe, 1); // Dernière notification
        
        if (!empty($notifications)) {
            $latest = $notifications[0];
            if (!$latest['lu']) {
                Flight::json(['notification' => $latest]);
                return;
            }
        }
        
        Flight::json(['notification' => null]);
    }

    /**
     * Marque une notification comme lue
     */
    public function markNotificationAsRead($id_notification) {
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $id_employe = $_SESSION['user_id'];
        $success = $this->notificationModel->markAsRead($id_notification, $id_employe);
        
        Flight::json(['success' => $success]);
    }

    /**
     * Calcule la date de fin en fonction de la date de début et de la durée
     */
    private function calculerDateFin($date_debut, $duree) {
        try {
            $debut = new DateTime($date_debut);
            $debut->modify("+{$duree} days");
            return $debut->format('Y-m-d');
        } catch (Exception $e) {
            return null;
        }
    }

}