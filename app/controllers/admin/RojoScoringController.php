<?php

namespace app\controllers\admin;

use app\models\admin\RojoScoringModel;
use Flight;

class RojoScoringController
{
    private $model;

    public function __construct()
    {
        $this->model = new RojoScoringModel();
    }

    public function pageEvaluations()
    {
        $periode = Flight::request()->query->periode ?? 'mensuelle';
        $evaluations = $this->model->getEvaluations($periode);
        
        // Calculer les statistiques via le modèle
        $stats = [
            'moyenne_globale' => $this->model->getMoyenneGlobale($evaluations),
            'nombre_excellent' => $this->model->getNombreExcellent($evaluations),
            'nombre_amelioration' => $this->model->getNombreAmelioration($evaluations)
        ];
        
        Flight::render('admin/evaluations_performance', [
            'evaluations' => $evaluations,
            'periode_selectionnee' => $periode,
            'stats' => $stats,
            'model' => $this->model // Passer le modèle à la vue pour les méthodes helper
        ]);
    }

    public function calculerEvaluations()
    {
        $data = Flight::request()->data;
        $periode = $data->periode ?? 'mensuelle';
        
        $employes = $this->model->getEmployesActifs();
        $resultats = [];

        foreach ($employes as $employe) {
            $scores_detail = $this->model->calculerScorePerformance($employe['id_employe'], $periode);
            $score_global = $this->model->calculerScoreGlobal($scores_detail);
            
            // IMPORTANT : Utiliser sauvegarderEvaluation qui supprime d'abord les doublons
            $this->model->sauvegarderEvaluation($employe['id_employe'], $periode, $score_global, $scores_detail);
            
            $resultats[] = [
                'employe' => $employe['prenom'] . ' ' . $employe['nom'],
                'score_global' => $score_global,
                'scores_detail' => $scores_detail
            ];
        }

        Flight::json([
            'status' => 'success',
            'message' => 'Évaluations recalculées avec succès.',
            'resultats' => $resultats
        ]);
    }

    public function genererRapport()
    {
        $id_employe = Flight::request()->query->id_employe;
        $periode = Flight::request()->query->periode ?? 'mensuelle';
        
        if (!$id_employe) {
            Flight::json(['status' => 'error', 'message' => 'ID employé manquant.']);
            return;
        }

        // Calculer le score
        $scores_detail = $this->model->calculerScorePerformance($id_employe, $periode);
        $score_global = $this->model->calculerScoreGlobal($scores_detail);
        
        // Récupérer les informations de l'employé
        $employes = $this->model->getEmployesActifs();
        $employe = array_filter($employes, fn($e) => $e['id_employe'] == $id_employe);
        $employe = reset($employe);

        $rapport = $this->genererContenuRapport($employe, $score_global, $scores_detail, $periode);
        
        Flight::json([
            'status' => 'success',
            'rapport' => $rapport
        ]);
    }

    private function genererContenuRapport($employe, $score_global, $scores_detail, $periode)
    {
        $points_forts = $this->identifierPointsForts($scores_detail);
        $points_amelioration = $this->identifierPointsAmelioration($scores_detail);
        $recommandations = $this->genererRecommandations($scores_detail);

        return [
            'employe' => $employe,
            'periode' => $periode,
            'score_global' => $score_global,
            'scores_detail' => $scores_detail,
            'points_forts' => $points_forts,
            'points_amelioration' => $points_amelioration,
            'recommandations' => $recommandations
        ];
    }

    private function identifierPointsForts($scores_detail)
    {
        $points = [];
        
        foreach ($scores_detail as $critere => $data) {
            if ($data['score'] >= 80) {
                switch ($critere) {
                    case 'assiduite':
                        $points[] = 'Excellente assiduité et ponctualité';
                        break;
                    case 'objectifs':
                        $points[] = 'Objectifs régulièrement atteints';
                        break;
                    case 'qualite':
                        $points[] = 'Travail de qualité constante';
                        break;
                    case 'comportement':
                        $points[] = 'Excellent comportement professionnel';
                        break;
                    case 'productivite':
                        $points[] = 'Forte productivité et engagement';
                        break;
                }
            }
        }

        return empty($points) ? ['Performance régulière dans tous les domaines'] : $points;
    }

    private function identifierPointsAmelioration($scores_detail)
    {
        $points = [];
        
        foreach ($scores_detail as $critere => $data) {
            if ($data['score'] < 70) {
                switch ($critere) {
                    case 'assiduite':
                        $points[] = 'Améliorer la ponctualité et réduire les absences';
                        break;
                    case 'objectifs':
                        $points[] = 'Renforcer l\'atteinte des objectifs';
                        break;
                    case 'qualite':
                        $points[] = 'Améliorer la qualité du travail';
                        break;
                    case 'comportement':
                        $points[] = 'Développer les compétences comportementales';
                        break;
                    case 'productivite':
                        $points[] = 'Augmenter la productivité';
                        break;
                }
            }
        }

        return empty($points) ? ['Maintenir le niveau de performance actuel'] : $points;
    }

    private function genererRecommandations($scores_detail)
    {
        $recommandations = [];
        
        if ($scores_detail['assiduite']['score'] < 70) {
            $recommandations[] = 'Suivi rapproché de la ponctualité';
        }
        
        if ($scores_detail['objectifs']['score'] < 70) {
            $recommandations[] = 'Formation sur la gestion des priorités';
        }
        
        if ($scores_detail['comportement']['score'] < 70) {
            $recommandations[] = 'Coaching professionnel';
        }

        return empty($recommandations) ? ['Continuer les bonnes pratiques actuelles'] : $recommandations;
    }




}