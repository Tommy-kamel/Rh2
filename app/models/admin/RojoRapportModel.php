<?php

namespace app\models\admin;

use Flight;

class RojoRapportModel
{
    private $scoringModel;

    public function __construct()
    {
        $this->scoringModel = new RojoScoringModel();
    }

    public function genererRapportPerformance($id_employe, $periode)
    {
        // Récupérer les informations de l'employé
        $employe = $this->getInfosEmploye($id_employe);
        
        // Calculer le score de performance
        $scores_detail = $this->scoringModel->calculerScorePerformance($id_employe, $periode);
        $score_global = $this->scoringModel->calculerScoreGlobal($scores_detail);
        
        // Générer l'analyse
        $analyse = $this->genererAnalysePerformance($score_global, $scores_detail);
        
        // Historique des scores
        $historique = $this->getHistoriqueScores($id_employe, $periode);
        
        return [
            'employe' => $employe,
            'periode' => $periode,
            'score_global' => $score_global,
            'scores_detail' => $scores_detail,
            'analyse' => $analyse,
            'historique' => $historique,
            'date_generation' => date('d/m/Y à H:i')
        ];
    }

    public function genererRapportGlobal($periode)
    {
        $evaluations = $this->scoringModel->getEvaluations($periode);
        
        $stats_globales = [
            'total_employes' => count($evaluations),
            'moyenne_globale' => $this->scoringModel->getMoyenneGlobale($evaluations),
            'nombre_excellent' => $this->scoringModel->getNombreExcellent($evaluations),
            'nombre_bon' => $this->scoringModel->getNombreBon($evaluations),
            'nombre_moyen' => $this->scoringModel->getNombreMoyen($evaluations),
            'nombre_faible' => $this->scoringModel->getNombreFaible($evaluations)
        ];

        // Classement par département
        $classement_departements = $this->getClassementDepartements($evaluations);
        
        // Tendances
        $tendances = $this->analyserTendances($periode);

        return [
            'periode' => $periode,
            'stats_globales' => $stats_globales,
            'classement_departements' => $classement_departements,
            'top_performers' => array_slice($evaluations, 0, 5),
            'besoin_amelioration' => array_slice($evaluations, -5),
            'tendances' => $tendances,
            'date_generation' => date('d/m/Y à H:i')
        ];
    }

    private function getInfosEmploye($id_employe)
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                e.*,
                p.nom as poste,
                d.nom_departement,
                c.salaire,
                c.date_debut as date_embauche
            FROM employe e
            LEFT JOIN contrat c ON c.id_employe = e.id_employe 
                AND (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            LEFT JOIN poste p ON p.id_poste = c.id_poste
            LEFT JOIN departement d ON d.id_departement = c.id_departement
            WHERE e.id_employe = :id_employe
            ORDER BY c.date_debut DESC
            LIMIT 1
        ");
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    private function genererAnalysePerformance($score_global, $scores_detail)
    {
        $analyse = [
            'points_forts' => [],
            'points_amelioration' => [],
            'recommandations' => [],
            'niveau_performance' => $this->getNiveauPerformance($score_global)
        ];

        // Analyser chaque critère
        foreach ($scores_detail as $critere => $data) {
            $score = $data['score'];
            
            if ($score >= 80) {
                $analyse['points_forts'][] = $this->getPointFort($critere, $data);
            } elseif ($score < 60) {
                $analyse['points_amelioration'][] = $this->getPointAmelioration($critere, $data);
            }
        }

        // Générer les recommandations
        $analyse['recommandations'] = $this->genererRecommandations($scores_detail);

        // Message personnalisé selon le niveau
        $analyse['message_global'] = $this->getMessagePerformance($score_global);

        return $analyse;
    }

    private function getNiveauPerformance($score)
    {
        if ($score >= 90) return 'Exceptionnelle';
        if ($score >= 80) return 'Excellente';
        if ($score >= 70) return 'Bonne';
        if ($score >= 60) return 'Satisfaisante';
        return 'À améliorer';
    }

    private function getPointFort($critere, $data)
    {
        $points = [
            'assiduite' => 'Excellente assiduité et ponctualité remarquable',
            'objectifs' => 'Objectifs régulièrement dépassés',
            'qualite' => 'Travail de grande qualité et rigueur',
            'comportement' => 'Exemplaire dans le comportement professionnel',
            'productivite' => 'Productivité et efficacité exceptionnelles'
        ];
        
        return $points[$critere] ?? 'Performance remarquable';
    }

    private function getPointAmelioration($critere, $data)
    {
        $points = [
            'assiduite' => 'Amélioration nécessaire de la ponctualité',
            'objectifs' => 'Atteinte des objectifs à renforcer',
            'qualite' => 'Attention à la qualité du travail fourni',
            'comportement' => 'Comportement professionnel à améliorer',
            'productivite' => 'Productivité en dessous des attentes'
        ];
        
        return $points[$critere] ?? 'Point à améliorer identifié';
    }

    private function genererRecommandations($scores_detail)
    {
        $recommandations = [];

        if ($scores_detail['assiduite']['score'] < 70) {
            $recommandations[] = 'Mettre en place un suivi rapproché de la ponctualité';
        }
        
        if ($scores_detail['objectifs']['score'] < 70) {
            $recommandations[] = 'Formation sur la gestion des priorités et objectifs';
        }
        
        if ($scores_detail['qualite']['score'] < 70) {
            $recommandations[] = 'Accompagnement sur les standards qualité';
        }
        
        if ($scores_detail['comportement']['score'] < 70) {
            $recommandations[] = 'Coaching professionnel et développement des soft skills';
        }
        
        if ($scores_detail['productivite']['score'] < 70) {
            $recommandations[] = 'Optimisation des processus de travail';
        }

        // Recommandation générale si bonnes performances
        if (empty($recommandations)) {
            $recommandations[] = 'Continuer les bonnes pratiques actuelles';
            $recommandations[] = 'Partager les meilleures pratiques avec l\'équipe';
        }

        return $recommandations;
    }

    private function getMessagePerformance($score)
    {
        if ($score >= 90) {
            return "Performance exceptionnelle ! L'employé dépasse toutes les attentes et fait preuve d'un engagement remarquable.";
        } elseif ($score >= 80) {
            return "Excellente performance globale avec des résultats très satisfaisants dans tous les domaines.";
        } elseif ($score >= 70) {
            return "Bonne performance avec des points forts identifiés et quelques axes d'amélioration possibles.";
        } elseif ($score >= 60) {
            return "Performance satisfaisante mais nécessitant une attention sur certains aspects spécifiques.";
        } else {
            return "Performance nécessitant une amélioration significative. Un plan d'action spécifique est recommandé.";
        }
    }

    private function getHistoriqueScores($id_employe, $periode_actuelle)
    {
        $stmt = Flight::db()->prepare("
            SELECT periode, score_global, date_evaluation
            FROM evaluation_performance
            WHERE id_employe = :id_employe
            AND periode = :periode
            ORDER BY date_evaluation DESC
            LIMIT 6
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'periode' => $periode_actuelle
        ]);
        
        $historique = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Inverser pour avoir du plus ancien au plus récent
        return array_reverse($historique);
    }

    private function getClassementDepartements($evaluations)
    {
        $departements = [];
        
        foreach ($evaluations as $eval) {
            $departement = $eval['nom_departement'] ?? 'Non assigné';
            
            if (!isset($departements[$departement])) {
                $departements[$departement] = [
                    'total_scores' => 0,
                    'nb_employes' => 0,
                    'employes' => []
                ];
            }
            
            $departements[$departement]['total_scores'] += $eval['score_global'];
            $departements[$departement]['nb_employes']++;
            $departements[$departement]['employes'][] = [
                'nom' => $eval['prenom'] . ' ' . $eval['nom'],
                'score' => $eval['score_global']
            ];
        }
        
        // Calculer les moyennes et trier
        foreach ($departements as $nom => &$data) {
            $data['moyenne'] = round($data['total_scores'] / $data['nb_employes'], 2);
            
            // Trier les employés par score décroissant
            usort($data['employes'], function($a, $b) {
                return $b['score'] - $a['score'];
            });
        }
        
        // Trier les départements par moyenne décroissante
        uasort($departements, function($a, $b) {
            return $b['moyenne'] - $a['moyenne'];
        });
        
        return $departements;
    }

    private function analyserTendances($periode)
    {
        // Pour l'instant, retourner des tendances fictives
        // En production, il faudrait comparer avec les périodes précédentes
        return [
            'evolution_globale' => '+2.5%',
            'departement_plus_amelioré' => 'Ressources Humaines',
            'critere_plus_amelioré' => 'Assiduité',
            'tendance_generale' => 'Amélioration progressive'
        ];
    }

    // Méthodes pour RojoScoringModel
    public function getNombreBon($evaluations)
    {
        return count(array_filter($evaluations, fn($e) => $e['score_global'] >= 70 && $e['score_global'] < 80));
    }

    public function getNombreMoyen($evaluations)
    {
        return count(array_filter($evaluations, fn($e) => $e['score_global'] >= 60 && $e['score_global'] < 70));
    }

    public function getNombreFaible($evaluations)
    {
        return count(array_filter($evaluations, fn($e) => $e['score_global'] < 60));
    }

    // Ajouter ces méthodes dans la classe RojoRapportModel

public function getClasseScore($score)
{
    if ($score >= 80) return 'score-excellent';
    if ($score >= 70) return 'score-bon';
    if ($score >= 60) return 'score-moyen';
    if ($score >= 50) return 'score-faible';
    return 'score-critique';
}

public function getCouleurNiveau($niveau)
{
    $couleurs = [
        'Exceptionnelle' => 'success',
        'Excellente' => 'success',
        'Bonne' => 'info',
        'Satisfaisante' => 'warning',
        'À améliorer' => 'danger'
    ];
    return $couleurs[$niveau] ?? 'secondary';
}

public function getCouleurScore($score)
{
    if ($score >= 80) return 'success';
    if ($score >= 70) return 'info';
    if ($score >= 60) return 'warning';
    return 'danger';
}

public function getCouleurCritere($critere)
{
    $couleurs = [
        'assiduite' => 'primary',
        'objectifs' => 'success',
        'qualite' => 'warning',
        'comportement' => 'info',
        'productivite' => 'danger'
    ];
    return $couleurs[$critere] ?? 'secondary';
}

public function getIconeCritere($critere)
{
    $icones = [
        'assiduite' => 'clock',
        'objectifs' => 'target',
        'qualite' => 'award',
        'comportement' => 'users',
        'productivite' => 'trending-up'
    ];
    return $icones[$critere] ?? 'circle';
}

public function getLibelleCritere($critere)
{
    $libelles = [
        'assiduite' => 'Assiduité',
        'objectifs' => 'Objectifs',
        'qualite' => 'Qualité',
        'comportement' => 'Comportement',
        'productivite' => 'Productivité'
    ];
    return $libelles[$critere] ?? $critere;
}

public function getNiveauSimplifie($score)
{
    if ($score >= 80) return 'Excellent';
    if ($score >= 70) return 'Bon';
    if ($score >= 60) return 'Moyen';
    return 'Faible';
}
}