<?php

namespace app\models\admin;

use Flight;
use DateTime;

class RojoScoringModel
{
    public function getEmployesActifs()
    {
        $stmt = Flight::db()->prepare("
            SELECT e.*, p.nom as poste, d.nom_departement
            FROM employe e
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            JOIN departement d ON d.id_departement = c.id_departement
            WHERE (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function calculerScorePerformance($id_employe, $periode)
    {
        $date_debut = $this->getDateDebutPeriode($periode);
        
        return [
            'assiduite' => $this->calculerScoreAssiduite($id_employe, $date_debut),
            'objectifs' => $this->calculerScoreObjectifs($id_employe, $date_debut),
            'qualite' => $this->calculerScoreQualite($id_employe, $date_debut),
            'comportement' => $this->calculerScoreComportement($id_employe, $date_debut),
            'productivite' => $this->calculerScoreProductivite($id_employe, $date_debut)
        ];
    }

    public function calculerScoreGlobal($scores_detail)
    {
        $poids = [
            'assiduite' => 0.20,    // 20%
            'objectifs' => 0.30,    // 30%
            'qualite' => 0.25,      // 25%
            'comportement' => 0.15, // 15%
            'productivite' => 0.10  // 10%
        ];

        $score_global = 0;
        foreach ($scores_detail as $critere => $score) {
            $score_global += $score['score'] * $poids[$critere];
        }

        // CORRECTION : Limiter le score global à 100 maximum
        $score_global = min(100, round($score_global, 2));
        
        return $score_global;
    }

    private function calculerScoreAssiduite($id_employe, $date_debut)
    {
        // Calcul basé sur les présences, retards et absences
        $jours_travailles = $this->getJoursTravailles($id_employe, $date_debut);
        $retards = $this->getRetards($id_employe, $date_debut);
        $absences = $this->getAbsences($id_employe, $date_debut);

        $score_base = 100;
        
        // Pénalités
        $penalite_retard = min($retards['total'] * 2, 20); // 2 points par retard, max 20
        $penalite_absence = min($absences['deductibles'] * 5, 30); // 5 points par absence, max 30
        
        $score = max(0, $score_base - $penalite_retard - $penalite_absence);
        
        // CORRECTION : Limiter à 100 maximum
        $score = min(100, $score);
        
        return [
            'score' => $score,
            'details' => [
                'jours_travailles' => $jours_travailles,
                'retards' => $retards,
                'absences' => $absences,
                'penalites' => [
                    'retard' => $penalite_retard,
                    'absence' => $penalite_absence
                ]
            ]
        ];
    }

    private function calculerScoreObjectifs($id_employe, $date_debut)
    {
        // Pour l'instant, score fictif basé sur les heures supplémentaires et productivité
        $heures_sup = $this->getHeuresSupplementaires($id_employe, $date_debut);
        $taux_presence = $this->getTauxPresence($id_employe, $date_debut);
        
        // Score basé sur la régularité et l'engagement
        $score = 70 + ($taux_presence * 0.3) + (min($heures_sup['total_heures'], 20) * 1.5);
        
        // CORRECTION : Limiter à 100 maximum
        $score = min(100, round($score));
        
        return [
            'score' => $score,
            'details' => [
                'heures_supplementaires' => $heures_sup,
                'taux_presence' => $taux_presence
            ]
        ];
    }

    private function calculerScoreQualite($id_employe, $date_debut)
    {
        // Score basé sur les retards (inverse) et la ponctualité
        $retards = $this->getRetards($id_employe, $date_debut);
        $taux_ponctualite = $this->getTauxPonctualite($id_employe, $date_debut);
        
        $score = 80 + ($taux_ponctualite * 0.2) - (min($retards['total'], 10) * 2);
        
        // CORRECTION : Limiter à 100 maximum et minimum 0
        $score = max(0, min(100, round($score)));
        
        return [
            'score' => $score,
            'details' => [
                'taux_ponctualite' => $taux_ponctualite,
                'retards_moyens' => $retards['moyenne'] ?? 0
            ]
        ];
    }

    private function calculerScoreComportement($id_employe, $date_debut)
    {
        // Score basé sur la régularité et la fiabilité
        $taux_presence = $this->getTauxPresence($id_employe, $date_debut);
        $absences = $this->getAbsences($id_employe, $date_debut);
        
        $score = 75 + ($taux_presence * 0.25) - (min($absences['total'], 5) * 5);
        
        // CORRECTION : Limiter à 100 maximum et minimum 0
        $score = max(0, min(100, round($score)));
        
        return [
            'score' => $score,
            'details' => [
                'taux_presence' => $taux_presence,
                'absences_justifiees' => $absences['non_deductibles'] ?? 0
            ]
        ];
    }

   private function calculerScoreProductivite($id_employe, $date_debut)
    {
        // Score basé sur les heures supplémentaires et l'assiduité
        $heures_sup = $this->getHeuresSupplementaires($id_employe, $date_debut);
        $taux_presence = $this->getTauxPresence($id_employe, $date_debut);
        
        $score = 60 + ($taux_presence * 0.2) + (min($heures_sup['total_heures'], 30) * 1);
        
        // CORRECTION : Limiter à 100 maximum
        $score = min(100, round($score));
        
        return [
            'score' => $score,
            'details' => [
                'heures_supplementaires' => $heures_sup,
                'taux_presence' => $taux_presence
            ]
        ];
    }
    // === METHODES D'ACCES AUX DONNEES ===

    private function getJoursTravailles($id_employe, $date_debut)
    {
        $stmt = Flight::db()->prepare("
            SELECT COUNT(DISTINCT DATE(date_heure_arrive)) as total
            FROM pointage 
            WHERE id_employe = :id_employe 
            AND date_heure_arrive >= :date_debut
            AND date_heure_depart IS NOT NULL
        ");
        $stmt->execute(['id_employe' => $id_employe, 'date_debut' => $date_debut]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    private function getRetards($id_employe, $date_debut)
    {
        $stmt = Flight::db()->prepare("
            SELECT COUNT(*) as total, AVG(duree_retard) as moyenne
            FROM retard 
            WHERE id_employe = :id_employe 
            AND date_retard >= :date_debut
        ");
        $stmt->execute(['id_employe' => $id_employe, 'date_debut' => $date_debut]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['total' => 0, 'moyenne' => 0];
    }

    private function getAbsences($id_employe, $date_debut)
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(estdeductible) as deductibles,
                SUM(NOT estdeductible) as non_deductibles
            FROM absence 
            WHERE id_employe = :id_employe 
            AND date_absence >= :date_debut
        ");
        $stmt->execute(['id_employe' => $id_employe, 'date_debut' => $date_debut]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['total' => 0, 'deductibles' => 0, 'non_deductibles' => 0];
    }

    private function getHeuresSupplementaires($id_employe, $date_debut)
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                SUM(nombre_minutes) as total_minutes,
                COUNT(*) as occurrences
            FROM heure_sup_employe 
            WHERE id_employe = :id_employe 
            AND date_heure_sup >= :date_debut
        ");
        $stmt->execute(['id_employe' => $id_employe, 'date_debut' => $date_debut]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['total_minutes' => 0, 'occurrences' => 0];
        
        $result['total_heures'] = round($result['total_minutes'] / 60, 1);
        return $result;
    }

    


    private function getJoursOuvrables($date_debut)
    {
        // Simplification: compte les jours entre date_debut et aujourd'hui
        $debut = new DateTime($date_debut);
        $aujourdhui = new DateTime();
        $interval = $debut->diff($aujourdhui);
        return max(1, $interval->days); // Au moins 1 jour
    }

    private function getDateDebutPeriode($periode)
    {
        $aujourdhui = new DateTime();
        
        switch ($periode) {
            case 'mensuelle':
                return $aujourdhui->modify('-1 month')->format('Y-m-d');
            case 'trimestrielle':
                return $aujourdhui->modify('-3 months')->format('Y-m-d');
            case 'annuelle':
                return $aujourdhui->modify('-1 year')->format('Y-m-d');
            default:
                return $aujourdhui->modify('-1 month')->format('Y-m-d');
        }
    }

    

        public function getEvaluations($periode = null)
    {
        // REQUÊTE CORRIGÉE - Pas de jointures multiples
        $sql = "
            SELECT 
                ep.*, 
                e.nom, 
                e.prenom
            FROM evaluation_performance ep
            JOIN employe e ON e.id_employe = ep.id_employe
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($periode) {
            $sql .= " AND ep.periode = :periode";
            $params['periode'] = $periode;
        }
        
        $sql .= " ORDER BY ep.score_global DESC, e.nom, e.prenom";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        $evaluations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Enrichir avec les infos poste/département SANS créer de doublons
        foreach ($evaluations as &$eval) {
            $infos_employe = $this->getInfosEmployeActuel($eval['id_employe']);
            $eval['poste'] = $infos_employe['poste'] ?? 'Non défini';
            $eval['nom_departement'] = $infos_employe['departement'] ?? 'Non défini';
        }
        
        return $evaluations;
    }

    private function getInfosEmployeActuel($id_employe)
    {
        $stmt = Flight::db()->prepare("
            SELECT p.nom as poste, d.nom_departement as departement
            FROM contrat c
            JOIN poste p ON p.id_poste = c.id_poste
            JOIN departement d ON d.id_departement = c.id_departement
            WHERE c.id_employe = :id_employe
            AND (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            ORDER BY c.date_debut DESC
            LIMIT 1
        ");
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    // CORRECTION CRITIQUE : Empêcher les doublons
    public function sauvegarderEvaluation($id_employe, $periode, $score_global, $scores_detail)
    {
        // D'abord, supprimer toute évaluation existante pour cet employé et période
        $this->supprimerEvaluationExistante($id_employe, $periode);
        
        // Puis insérer la nouvelle
        $stmt = Flight::db()->prepare("
            INSERT INTO evaluation_performance 
            (id_employe, periode, score_global, scores_detail, date_evaluation)
            VALUES (:id_employe, :periode, :score_global, :scores_detail, NOW())
        ");
        
        return $stmt->execute([
            'id_employe' => $id_employe,
            'periode' => $periode,
            'score_global' => $score_global,
            'scores_detail' => json_encode($scores_detail)
        ]);
    }

    private function supprimerEvaluationExistante($id_employe, $periode)
    {
        $stmt = Flight::db()->prepare("
            DELETE FROM evaluation_performance 
            WHERE id_employe = :id_employe AND periode = :periode
        ");
        return $stmt->execute([
            'id_employe' => $id_employe,
            'periode' => $periode
        ]);
    }

    private function getInfosContratActuel($id_employe)
    {
        $stmt = Flight::db()->prepare("
            SELECT p.nom as poste, d.nom_departement as departement
            FROM contrat c
            JOIN poste p ON p.id_poste = c.id_poste
            JOIN departement d ON d.id_departement = c.id_departement
            WHERE c.id_employe = :id_employe
            AND (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            ORDER BY c.date_debut DESC
            LIMIT 1
        ");
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
    // Ajouter ces méthodes dans la classe RojoScoringModel

    public function getMoyenneGlobale($evaluations)
    {
        if (empty($evaluations)) return '0';
        $total = array_sum(array_column($evaluations, 'score_global'));
        return round($total / count($evaluations), 1);
    }

    public function getNombreExcellent($evaluations)
    {
        return count(array_filter($evaluations, fn($e) => $e['score_global'] >= 80));
    }

    public function getNombreAmelioration($evaluations)
    {
        return count(array_filter($evaluations, fn($e) => $e['score_global'] < 60));
    }

    public function getClasseScore($score)
    {
        if ($score >= 80) return 'score-high';
        if ($score >= 60) return 'score-medium';
        return 'score-low';
    }

    public function getClasseProgress($score)
    {
        if ($score >= 80) return 'bg-success';
        if ($score >= 60) return 'bg-warning';
        return 'bg-danger';
    }

    private function getTauxPresence($id_employe, $date_debut)
{
    $jours_total = $this->getJoursOuvrables($date_debut);
    $jours_presents = $this->getJoursTravailles($id_employe, $date_debut);
    
    $taux = $jours_total > 0 ? ($jours_presents / $jours_total) * 100 : 0;
    
    // CORRECTION : Limiter à 100% maximum
    return min(100, round($taux, 1));
}

private function getTauxPonctualite($id_employe, $date_debut)
{
    $jours_total = $this->getJoursTravailles($id_employe, $date_debut);
    $retards = $this->getRetards($id_employe, $date_debut);
    
    if ($jours_total == 0) return 100;
    
    $taux = max(0, 100 - (($retards['total'] / $jours_total) * 100));
    
    // CORRECTION : Limiter à 100% maximum
    return min(100, round($taux, 1));
}
}