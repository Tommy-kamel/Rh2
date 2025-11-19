<?php

namespace app\controllers\admin;

use app\models\admin\RojoPaieModel;
use Flight;

class RojoPaieController
{
    private $model;

    public function __construct()
    {
        $this->model = new RojoPaieModel();
    }

    public function pageIntegrationPaie()
    {
        $moisDisponibles = $this->model->getMoisDisponibles();
        
        // Mois par défaut (courant)
        $mois = Flight::request()->query->mois ?? date('m');
        $annee = Flight::request()->query->annee ?? date('Y');
        
        $donneesPaie = $this->model->getDonneesPaiePourMois($mois, $annee);
        
        Flight::render('admin/integration_paie', [
            'donneesPaie' => $donneesPaie,
            'moisDisponibles' => $moisDisponibles,
            'moisSelectionne' => $mois,
            'anneeSelectionnee' => $annee,
            'formaterDuree' => function($minutes) {
                return $this->model->formaterDuree($minutes);
            }
        ]);
    }

    public function exporterDonneesPaie()
    {
        $mois = Flight::request()->query->mois ?? date('m');
        $annee = Flight::request()->query->annee ?? date('Y');
        
        $donneesPaie = $this->model->getDonneesPaiePourMois($mois, $annee);
        
        // Générer un fichier CSV
        $filename = "donnees_paie_{$mois}_{$annee}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // En-tête CSV
        fputcsv($output, [
            'Employé', 'Poste', 'Heures travaillées', 'Heures supplémentaires', 
            'Retards', 'Absences', 'Salaire base'
        ]);
        
        // Données
        foreach ($donneesPaie as $ligne) {
            fputcsv($output, [
                $ligne['prenom'] . ' ' . $ligne['nom'],
                $ligne['poste'],
                $this->model->formaterDuree($ligne['total_minutes_travaillees']),
                $this->model->formaterDuree($ligne['total_minutes_supp']),
                $this->model->formaterDuree($ligne['total_minutes_retard']),
                $ligne['total_absences'] . ' jour(s)',
                number_format($ligne['salaire'], 2, ',', ' ') . ' €'
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function pageFicheEmploye()
    {
        $employes = $this->model->getListeEmployes();
        
        // Récupérer l'employé sélectionné ou le premier par défaut
        $id_employe = Flight::request()->query->id_employe ?? ($employes[0]['id_employe'] ?? null);
        $mois = Flight::request()->query->mois ?? date('m');
        $annee = Flight::request()->query->annee ?? date('Y');
        
        $ficheEmploye = null;
        if ($id_employe) {
            $ficheEmploye = $this->model->getFicheEmployePourMois($id_employe, $mois, $annee);
        }
        
        Flight::render('admin/fiche_employe_paie', [
            'employes' => $employes,
            'ficheEmploye' => $ficheEmploye,
            'id_employe_selectionne' => $id_employe,
            'mois_selectionne' => $mois,
            'annee_selectionnee' => $annee
        ]);
    }

    public function exporterFicheEmploye()
    {
        $id_employe = Flight::request()->query->id_employe;
        $mois = Flight::request()->query->mois ?? date('m');
        $annee = Flight::request()->query->annee ?? date('Y');
        
        if (!$id_employe) {
            Flight::json(['status' => 'error', 'message' => 'ID employé manquant']);
            return;
        }
        
        $ficheEmploye = $this->model->getFicheEmployePourMois($id_employe, $mois, $annee);
        
        if (!$ficheEmploye) {
            Flight::json(['status' => 'error', 'message' => 'Employé non trouvé']);
            return;
        }
        
        $filename = "fiche_paie_{$ficheEmploye['employe']['nom']}_{$ficheEmploye['employe']['prenom']}_{$mois}_{$annee}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // En-tête
        fputcsv($output, ["Fiche de paie - {$ficheEmploye['employe']['prenom']} {$ficheEmploye['employe']['nom']}"]);
        fputcsv($output, ["Période: $mois/$annee"]);
        fputcsv($output, []); // Ligne vide
        
        // Informations employé
        fputcsv($output, ['INFORMATIONS EMPLOYÉ']);
        fputcsv($output, ['Poste', $ficheEmploye['employe']['poste']]);
        fputcsv($output, ['Département', $ficheEmploye['employe']['departement'] ?? 'Non spécifié']);
        fputcsv($output, ['Salaire base', number_format($ficheEmploye['employe']['salaire'], 2, ',', ' ') . ' €']);
        fputcsv($output, []); // Ligne vide
        
        // Récapitulatif
        fputcsv($output, ['RÉCAPITULATIF DU MOIS']);
        fputcsv($output, ['Heures travaillées', $this->model->formaterDuree($ficheEmploye['heures_travaillees'])]);
        
        $totalHeuresSup = array_sum(array_column($ficheEmploye['heures_supplementaires'], 'nombre_minutes'));
        fputcsv($output, ['Heures supplémentaires', $this->model->formaterDuree($totalHeuresSup)]);
        
        $totalRetards = array_sum(array_column($ficheEmploye['retards'], 'duree_retard'));
        fputcsv($output, ['Retards', $this->model->formaterDuree($totalRetards)]);
        
        fputcsv($output, ['Absences', count($ficheEmploye['absences']) . ' jour(s)']);
        fputcsv($output, []); // Ligne vide
        
        // Détail heures supplémentaires
        if (!empty($ficheEmploye['heures_supplementaires'])) {
            fputcsv($output, ['DÉTAIL HEURES SUPPLÉMENTAIRES']);
            fputcsv($output, ['Date', 'Type', 'Majoration', 'Durée']);
            foreach ($ficheEmploye['heures_supplementaires'] as $hs) {
                fputcsv($output, [
                    $hs['date_heure_sup'],
                    $hs['type'],
                    $hs['pourcentage_majoration'] . '%',
                    $this->model->formaterDuree($hs['nombre_minutes'])
                ]);
            }
            fputcsv($output, []); // Ligne vide
        }
        
        fclose($output);
        exit;
    }
}