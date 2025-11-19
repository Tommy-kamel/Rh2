<?php

namespace app\models\admin;

use Flight;
use DateTime;

class RojoPointageModel
{
    public function getEmployesAvecStatutPointage()
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                e.*, 
                p.nom as nom_poste, 
                hp.heure_debut, 
                hp.heure_fin,
                po.id_pointage,
                po.date_heure_arrive,
                po.date_heure_depart,
                CASE 
                    WHEN po.id_pointage IS NOT NULL THEN 'deja_pointe'
                    WHEN a.id_absence IS NOT NULL THEN 'absent'
                    ELSE 'non_pointe'
                END as statut_pointage
            FROM employe e
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            JOIN heure_pointage hp ON hp.id_poste = p.id_poste
            LEFT JOIN pointage po ON po.id_employe = e.id_employe 
                AND DATE(po.date_heure_arrive) = CURDATE()
            LEFT JOIN absence a ON a.id_employe = e.id_employe 
                AND a.date_absence = CURDATE()
            WHERE (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function pointerArriveeAvecRetard($id_employe, $date_heure_arrive = null)
    {
        $date_heure_arrive = $date_heure_arrive ?: date('Y-m-d H:i:s');

        if ($this->aDejaPointe($id_employe)) {
            return ['status' => 'error', 'message' => 'L\'employé a déjà pointé aujourd\'hui.'];
        }

        if ($this->absenceExistante($id_employe, date('Y-m-d'))) {
            $this->supprimerAbsence($id_employe, date('Y-m-d'));
        }

        $stmt = Flight::db()->prepare("
            INSERT INTO pointage (id_employe, date_heure_arrive)
            VALUES (:id_employe, :date_heure_arrive)
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_heure_arrive' => $date_heure_arrive
        ]);

        $resultat_retard = $this->calculerEtEnregistrerRetard($id_employe, $date_heure_arrive);

        return [
            'status' => 'success', 
            'message' => 'Arrivée enregistrée avec succès.',
            'retard' => $resultat_retard
        ];
    }

    public function getEmployesArrivesSansDepart()
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                e.*, 
                p.nom as nom_poste, 
                hp.heure_fin,
                po.id_pointage,
                po.date_heure_arrive,
                po.date_heure_depart
            FROM pointage po
            JOIN employe e ON e.id_employe = po.id_employe
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            JOIN heure_pointage hp ON hp.id_poste = p.id_poste
            WHERE DATE(po.date_heure_arrive) = CURDATE()
            AND po.date_heure_depart IS NULL
            AND (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function enregistrerDepartAvecHeure($id_employe, $date_heure_depart = null)
    {
        $date_heure_depart = $date_heure_depart ?: date('Y-m-d H:i:s');
        $date_jour = date('Y-m-d');
        
        $pointage = $this->getPointagePourDate($id_employe, $date_jour);
        if (!$pointage) return false;

        $stmt = Flight::db()->prepare("
            UPDATE pointage
            SET date_heure_depart = :date_heure_depart
            WHERE id_employe = :id_employe
              AND DATE(date_heure_arrive) = CURDATE()
              AND date_heure_depart IS NULL
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_heure_depart' => $date_heure_depart
        ]);

        $success = $stmt->rowCount() > 0;

        if ($success) {
            $this->calculerEtEnregistrerHeuresSup($id_employe, $pointage['date_heure_arrive'], $date_heure_depart);
        }

        return $success;
    }

    private function calculerEtEnregistrerRetard($id_employe, $date_heure_arrive)
    {
        $horaire = $this->getHoraireEmploye($id_employe);
        if (!$horaire) {
            return ['status' => 'no_horaire', 'message' => 'Aucun horaire trouvé pour cet employé.'];
        }

        $arrivee = new DateTime($date_heure_arrive);
        $date_jour = $arrivee->format('Y-m-d');
        $heure_ref = new DateTime($date_jour . ' ' . $horaire['heure_debut']);

        $diff = $arrivee->getTimestamp() - $heure_ref->getTimestamp();
        $minutes_retard = ceil($diff / 60);

        if ($minutes_retard > 5) {
            $stmt = Flight::db()->prepare("
                INSERT INTO retard (id_employe, date_retard, duree_retard)
                VALUES (:id_employe, :date_retard, :duree_retard)
            ");
            $stmt->execute([
                'id_employe' => $id_employe,
                'date_retard' => $date_jour,
                'duree_retard' => $minutes_retard
            ]);

            return [
                'status' => 'retard_enregistre', 
                'minutes' => $minutes_retard,
                'message' => "Retard de {$this->formaterDuree($minutes_retard)} enregistré."
            ];
        }

        return ['status' => 'pas_de_retard', 'minutes' => 0, 'message' => 'Aucun retard détecté.'];
    }

    private function calculerEtEnregistrerHeuresSup($id_employe, $date_heure_arrive, $date_heure_depart)
    {
        $horaire = $this->getHoraireEmploye($id_employe);
        if (!$horaire) return;

        $arrivee = new DateTime($date_heure_arrive);
        $depart = new DateTime($date_heure_depart);

        // -> Si l'heure de départ est après minuit (moins que l'heure d'arrivée)
        if ($depart < $arrivee) {
            $depart->modify('+1 day');
        }

        // Heure de fin de travail normale
        $heure_fin_ref = new DateTime($arrivee->format('Y-m-d') . ' ' . $horaire['heure_fin']);

        // -> Si l'horaire normal finit après minuit (ex : 22h - 02h)
        if ($horaire['heure_fin'] < $horaire['heure_debut']) {
            $heure_fin_ref->modify('+1 day');
        }

        if ($depart > $heure_fin_ref) {
            $diff = $depart->getTimestamp() - $heure_fin_ref->getTimestamp();
            $minutes_supp = ceil($diff / 60);

            $type_heure_sup = $this->determinerTypeHeureSup($depart, $arrivee->format('Y-m-d'));

            if ($minutes_supp > 0 && $type_heure_sup) {
                $this->enregistrerHeureSup(
                    $id_employe,
                    $type_heure_sup,
                    $arrivee->format('Y-m-d'),
                    $minutes_supp
                );
            }
        }
    }

    private function determinerTypeHeureSup($heure_depart, $date)
    {
        $jour_semaine = $heure_depart->format('N');
        $heure = $heure_depart->format('H');

        if ($jour_semaine >= 6) return 'week-end';
        if ($heure >= 20 || $heure < 6) return 'nuit';
        if ($this->estJourFerie($date)) return 'jour_ferie';
        
        return 'imprevu';
    }

    private function estJourFerie($date)
    {
        $stmt = Flight::db()->prepare("
            SELECT COUNT(*) as count 
            FROM jour_ferier 
            WHERE date_jour_ferier = :date
        ");
        $stmt->execute(['date' => $date]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    private function enregistrerHeureSup($id_employe, $type, $date, $minutes)
    {
        $stmt = Flight::db()->prepare("SELECT id_heure_sup FROM heure_sup WHERE type = :type");
        $stmt->execute(['type' => $type]);
        $heure_sup = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($heure_sup) {
            $stmt = Flight::db()->prepare("
                INSERT INTO heure_sup_employe (id_employe, id_heure_sup, date_heure_sup, nombre_minutes)
                VALUES (:id_employe, :id_heure_sup, :date_heure_sup, :nombre_minutes)
            ");
            $stmt->execute([
                'id_employe' => $id_employe,
                'id_heure_sup' => $heure_sup['id_heure_sup'],
                'date_heure_sup' => $date,
                'nombre_minutes' => $minutes
            ]);
        }
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

    private function formaterDuree($minutes)
    {
        if ($minutes < 60) return "{$minutes} min";
        
        $heures = floor($minutes / 60);
        $minutes_restantes = $minutes % 60;
        
        return $minutes_restantes > 0 ? "{$heures}h {$minutes_restantes}min" : "{$heures}h";
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

    private function supprimerAbsence($id_employe, $date)
    {
        $stmt = Flight::db()->prepare("
            DELETE FROM absence 
            WHERE id_employe = :id_employe AND date_absence = :date
        ");
        return $stmt->execute(['id_employe' => $id_employe, 'date' => $date]);
    }
}