<?php

namespace app\models\admin;

use Flight;
use DateTime;

class RojoAutreModel
{
    public function marquerAbsentManuellement($id_employe)
    {
        $date = date('Y-m-d');
        
        if ($this->aDejaPointe($id_employe)) {
            return ['status' => 'error', 'message' => 'L\'employé a déjà pointé aujourd\'hui.'];
        }

        if ($this->absenceExistante($id_employe, $date)) {
            return ['status' => 'error', 'message' => 'L\'employé est déjà marqué absent.'];
        }

        $success = $this->enregistrerAbsence($id_employe, $date);
        
        return $success ? 
            ['status' => 'success', 'message' => 'Employé marqué absent avec succès.'] :
            ['status' => 'error', 'message' => 'Erreur lors du marquage d\'absence.'];
    }

    public function getAbsentsPourDate($date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $stmt = Flight::db()->prepare("
            SELECT e.*, p.nom as poste
            FROM employe e
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            WHERE (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            AND e.id_employe NOT IN (
                SELECT id_employe FROM pointage WHERE DATE(date_heure_arrive) = :date
            )
            AND e.id_employe IN (
                SELECT id_employe FROM absence WHERE date_absence = :date
            )
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getEmployesNonPointes($date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $stmt = Flight::db()->prepare("
            SELECT e.*, p.nom as poste, hp.heure_fin
            FROM employe e
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            JOIN heure_pointage hp ON hp.id_poste = p.id_poste
            WHERE (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            AND e.id_employe NOT IN (
                SELECT id_employe FROM pointage WHERE DATE(date_heure_arrive) = :date
            )
            AND e.id_employe NOT IN (
                SELECT id_employe FROM absence WHERE date_absence = :date
            )
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAbsencesPourDate($date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $stmt = Flight::db()->prepare("
            SELECT a.*, e.nom, e.prenom, p.nom as poste
            FROM absence a
            JOIN employe e ON e.id_employe = a.id_employe
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            WHERE a.date_absence = :date
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRelevePresence($date = null)
    {
        $date = $date ?: date('Y-m-d');
        $releve = [];

        $employesActifs = $this->getEmployesActifs();

        foreach ($employesActifs as $employe) {
            $pointage = $this->getPointagePourDate($employe['id_employe'], $date);
            $estAbsent = $this->absenceExistante($employe['id_employe'], $date);
            $heures_sup = $this->getHeuresSupPourDate($employe['id_employe'], $date);

            
            $statut = 'Non pointé';
            if ($pointage) {
                $statut = 'Présent';
            } elseif ($estAbsent) {
                $absence_info = $this->getInfoAbsence($employe['id_employe'], $date);
                $statut = $absence_info['estdeductible'] ? 'Absent (Déductible)' : 'Absent (Non déductible)';
            }

            // Calculer les informations
            $arrivee = $pointage ? date('H:i', strtotime($pointage['date_heure_arrive'])) : '—';
            $depart = $pointage && $pointage['date_heure_depart'] ? date('H:i', strtotime($pointage['date_heure_depart'])) : '—';
            
            // Calculer la durée de travail
            $duree = '—';
            if ($pointage && $pointage['date_heure_arrive'] && $pointage['date_heure_depart']) {
                $debut = new DateTime($pointage['date_heure_arrive']);
                $fin = new DateTime($pointage['date_heure_depart']);
                $interval = $debut->diff($fin);
                $duree = $interval->format('%hh %im');
            }

            // Calculer le retard avec formatage
            $retard = $this->calculerRetard($pointage, $employe['id_employe'], $date);
            $heures_sup_formatees = $this->formaterHeuresSup($heures_sup);

            $releve[] = [
                'employe' => $employe['prenom'] . ' ' . $employe['nom'],
                'date' => $date,
                'arrivee' => $arrivee,
                'depart' => $depart,
                'duree' => $duree,
                'retard' => $retard,
                'heures_sup' => $heures_sup_formatees,
                'statut' => $statut
            ];
        }

        return $releve;
    }

    private function getInfoAbsence($id_employe, $date)
    {
        $stmt = Flight::db()->prepare("
            SELECT estdeductible 
            FROM absence 
            WHERE id_employe = :id_employe AND date_absence = :date
        ");
        $stmt->execute(['id_employe' => $id_employe, 'date' => $date]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['estdeductible' => false];
    }

    private function calculerRetard($pointage, $id_employe, $date)
    {
        if (!$pointage || !$pointage['date_heure_arrive']) return '—';

        $horaire = $this->getHoraireEmploye($id_employe);
        if (!$horaire) return '—';

        $arriveeObj = new DateTime($pointage['date_heure_arrive']);
        $heureRef = new DateTime($date . ' ' . $horaire['heure_debut']);
        
        if ($arriveeObj > $heureRef) {
            $diff = $arriveeObj->getTimestamp() - $heureRef->getTimestamp();
            $minutesRetard = ceil($diff / 60);
            return $this->formaterDuree($minutesRetard);
        }

        return '0 min';
    }

    private function formaterHeuresSup($heures_sup)
    {
        if (empty($heures_sup)) return '—';

        $total_minutes_sup = 0;
        $types_heures_sup = [];

        foreach ($heures_sup as $hs) {
            $total_minutes_sup += $hs['nombre_minutes'];
            $types_heures_sup[] = $hs['type'] . ' (' . $this->formaterMinutes($hs['nombre_minutes']) . ')';
        }

        $result = implode(', ', $types_heures_sup);
        $result .= ' - Total: ' . $this->formaterMinutes($total_minutes_sup);
        
        return $result;
    }

    private function formaterDuree($minutes)
    {
        if ($minutes < 60) return "{$minutes} min";
        
        $heures = floor($minutes / 60);
        $minutes_restantes = $minutes % 60;
        
        return $minutes_restantes > 0 ? "{$heures}h {$minutes_restantes}min" : "{$heures}h";
    }

    private function formaterMinutes($minutes)
    {
        return $this->formaterDuree($minutes);
    }


    public function getEmployesActifs()
    {
        $stmt = Flight::db()->prepare("
            SELECT e.* FROM employe e
            JOIN contrat c ON c.id_employe = e.id_employe
            WHERE c.date_fin IS NULL OR c.date_fin >= CURDATE()
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function aDejaPointe($id_employe)
    {
        $stmt = Flight::db()->prepare("
            SELECT COUNT(*) as c FROM pointage
            WHERE id_employe = :id_employe AND DATE(date_heure_arrive) = CURDATE()
        ");
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['c'] > 0;
    }

    private function getHoraireEmploye($id_employe)
    {
        $stmt = Flight::db()->prepare("
            SELECT hp.* FROM heure_pointage hp
            JOIN contrat c ON c.id_poste = hp.id_poste
            WHERE c.id_employe = :id_employe
            AND (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            ORDER BY c.date_debut DESC LIMIT 1
        ");
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function getPointagePourDate($id_employe, $date)
    {
        $stmt = Flight::db()->prepare("
            SELECT * FROM pointage 
            WHERE id_employe = :id_employe AND DATE(date_heure_arrive) = :date
            LIMIT 1
        ");
        $stmt->execute(['id_employe' => $id_employe, 'date' => $date]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function absenceExistante($id_employe, $date)
    {
        $stmt = Flight::db()->prepare("
            SELECT COUNT(*) as count FROM absence 
            WHERE id_employe = :id_employe AND date_absence = :date
        ");
        $stmt->execute(['id_employe' => $id_employe, 'date' => $date]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['count'] > 0;
    }

    public function getHeuresSupPourDate($id_employe, $date)
    {
        $stmt = Flight::db()->prepare("
            SELECT hse.*, hs.type, hs.pourcentage_majoration
            FROM heure_sup_employe hse
            JOIN heure_sup hs ON hs.id_heure_sup = hse.id_heure_sup
            WHERE hse.id_employe = :id_employe AND hse.date_heure_sup = :date
        ");
        $stmt->execute(['id_employe' => $id_employe, 'date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function enregistrerAbsence($id_employe, $date)
    {
        // Vérifier le nombre de congés restants
        $conge_restant = $this->getCongeRestant($id_employe);
        $estdeductible = ($conge_restant > 0);

        $stmt = Flight::db()->prepare("
            INSERT INTO absence (id_employe, date_absence, estdeductible)
            VALUES (:id_employe, :date_absence, :estdeductible)
        ");
        return $stmt->execute([
            'id_employe' => $id_employe,
            'date_absence' => $date,
            'estdeductible' => $estdeductible
        ]);
    }

    private function getCongeRestant($id_employe)
    {
        // Supposons 21 jours de congé par an par défaut
        $conge_annuel = 21;
        
        // Calculer les congés déjà pris cette année
        $stmt = Flight::db()->prepare("
            SELECT COUNT(*) as conge_pris
            FROM conge 
            WHERE id_employe = :id_employe 
            AND YEAR(date_debut) = YEAR(CURDATE())
            AND status IN (11, 21) -- Congés validés
        ");
        $stmt->execute(['id_employe' => $id_employe]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $conge_pris = $result['conge_pris'];
        
        return max(0, $conge_annuel - $conge_pris);
    }
}