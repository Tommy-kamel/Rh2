<?php

namespace app\controllers\employe;

use app\models\employe\CongeModel;
use Flight;
use DateTime;
use Exception;

class CongeController
{

    public function __construct()
    {
    
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