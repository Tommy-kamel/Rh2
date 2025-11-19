<?php

namespace app\models\admin;

use Flight;
use DateTime;

class RojoPaieModel
{
    public function getDonneesPaiePourMois($mois, $annee)
    {
        $dateDebut = $annee . '-' . $mois . '-01';
        $dateFin = date('Y-m-t', strtotime($dateDebut));
        
        $employes = $this->getEmployesActifs();
        $donneesPaie = [];
        
        foreach ($employes as $employe) {
            $donnees = [
                'id_employe' => $employe['id_employe'],
                'nom' => $employe['nom'],
                'prenom' => $employe['prenom'],
                'poste' => $employe['nom_poste'],
                'salaire' => $employe['salaire'],
                'total_minutes_travaillees' => $this->getHeuresTravaillees($employe['id_employe'], $dateDebut, $dateFin),
                'total_minutes_supp' => $this->getHeuresSupplementaires($employe['id_employe'], $dateDebut, $dateFin),
                'total_minutes_retard' => $this->getRetards($employe['id_employe'], $dateDebut, $dateFin),
                'total_absences' => $this->getAbsences($employe['id_employe'], $dateDebut, $dateFin)
            ];
            
            $donneesPaie[] = $donnees;
        }
        
        return $donneesPaie;
    }

    private function getEmployesActifs()
    {
        $stmt = Flight::db()->prepare("
            SELECT e.id_employe, e.nom, e.prenom, p.nom as nom_poste, c.salaire
            FROM employe e
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            WHERE (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private function getHeuresTravaillees($id_employe, $dateDebut, $dateFin)
    {
        $stmt = Flight::db()->prepare("
            SELECT COALESCE(SUM(
                CASE 
                    WHEN date_heure_depart IS NULL THEN 0
                    WHEN date_heure_depart >= date_heure_arrive THEN 
                        TIMESTAMPDIFF(MINUTE, date_heure_arrive, date_heure_depart)
                    ELSE 
                        -- Cas où le départ est le lendemain
                        TIMESTAMPDIFF(MINUTE, date_heure_arrive, DATE_ADD(date_heure_depart, INTERVAL 1 DAY))
                END
            ), 0) as total_minutes
            FROM pointage 
            WHERE id_employe = :id_employe
            AND DATE(date_heure_arrive) BETWEEN :date_debut AND :date_fin
            AND date_heure_depart IS NOT NULL
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_minutes'];
    }

    private function getHeuresSupplementaires($id_employe, $dateDebut, $dateFin)
    {
        $stmt = Flight::db()->prepare("
            SELECT COALESCE(SUM(nombre_minutes), 0) as total_minutes
            FROM heure_sup_employe 
            WHERE id_employe = :id_employe
            AND date_heure_sup BETWEEN :date_debut AND :date_fin
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_minutes'];
    }

    private function getRetards($id_employe, $dateDebut, $dateFin)
    {
        $stmt = Flight::db()->prepare("
            SELECT COALESCE(SUM(duree_retard), 0) as total_minutes
            FROM retard 
            WHERE id_employe = :id_employe
            AND date_retard BETWEEN :date_debut AND :date_fin
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_minutes'];
    }

    private function getAbsences($id_employe, $dateDebut, $dateFin)
    {
        $stmt = Flight::db()->prepare("
            SELECT COUNT(*) as total_absences
            FROM absence 
            WHERE id_employe = :id_employe
            AND date_absence BETWEEN :date_debut AND :date_fin
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_absences'];
    }

    public function getDetailHeuresSup($id_employe, $mois, $annee)
    {
        $dateDebut = $annee . '-' . $mois . '-01';
        $dateFin = date('Y-m-t', strtotime($dateDebut));

        $stmt = Flight::db()->prepare("
            SELECT 
                hs.type,
                hs.pourcentage_majoration,
                SUM(hse.nombre_minutes) as total_minutes
            FROM heure_sup_employe hse
            JOIN heure_sup hs ON hs.id_heure_sup = hse.id_heure_sup
            WHERE hse.id_employe = :id_employe
            AND hse.date_heure_sup BETWEEN :date_debut AND :date_fin
            GROUP BY hs.type, hs.pourcentage_majoration
            ORDER BY hs.pourcentage_majoration DESC
        ");

        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getMoisDisponibles()
    {
        $stmt = Flight::db()->prepare("
            SELECT DISTINCT 
                YEAR(date_heure_arrive) as annee,
                MONTH(date_heure_arrive) as mois,
                CONCAT(MONTHNAME(date_heure_arrive), ' ', YEAR(date_heure_arrive)) as libelle
            FROM pointage 
            WHERE date_heure_arrive IS NOT NULL
            ORDER BY annee DESC, mois DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function formaterDuree($minutes)
    {
        // Gérer les valeurs négatives
        if ($minutes < 0) {
            return "0 min";
        }
        
        if ($minutes < 60) return "{$minutes} min";
        
        $heures = floor($minutes / 60);
        $minutes_restantes = $minutes % 60;
        
        return $minutes_restantes > 0 ? "{$heures}h {$minutes_restantes}min" : "{$heures}h";
    }

    public function getFicheEmployePourMois($id_employe, $mois, $annee)
    {
        $dateDebut = $annee . '-' . $mois . '-01';
        $dateFin = date('Y-m-t', strtotime($dateDebut));
        
        // Informations de base de l'employé
        $employe = $this->getInfosEmploye($id_employe);
        
        if (!$employe) {
            return null;
        }
        
        // Données du mois
        $donnees = [
            'employe' => $employe,
            'heures_travaillees' => $this->getHeuresTravaillees($id_employe, $dateDebut, $dateFin),
            'heures_supplementaires' => $this->getHeuresSupplementairesDetail($id_employe, $dateDebut, $dateFin),
            'retards' => $this->getRetardsDetail($id_employe, $dateDebut, $dateFin),
            'absences' => $this->getAbsencesDetail($id_employe, $dateDebut, $dateFin),
            'pointages' => $this->getPointagesMois($id_employe, $dateDebut, $dateFin)
        ];
        
        return $donnees;
    }

    private function getInfosEmploye($id_employe)
    {
        $stmt = Flight::db()->prepare("
            SELECT e.*, p.nom as poste, c.salaire, d.nom_departement as departement
            FROM employe e
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            LEFT JOIN departement d ON d.id_departement = c.id_departement
            WHERE e.id_employe = :id_employe
            AND (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            LIMIT 1
        ");
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function getHeuresSupplementairesDetail($id_employe, $dateDebut, $dateFin)
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                hs.type,
                hs.pourcentage_majoration,
                hse.date_heure_sup,
                hse.nombre_minutes
            FROM heure_sup_employe hse
            JOIN heure_sup hs ON hs.id_heure_sup = hse.id_heure_sup
            WHERE hse.id_employe = :id_employe
            AND hse.date_heure_sup BETWEEN :date_debut AND :date_fin
            ORDER BY hse.date_heure_sup
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getRetardsDetail($id_employe, $dateDebut, $dateFin)
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                date_retard,
                duree_retard
            FROM retard 
            WHERE id_employe = :id_employe
            AND date_retard BETWEEN :date_debut AND :date_fin
            ORDER BY date_retard
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getAbsencesDetail($id_employe, $dateDebut, $dateFin)
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                date_absence,
                estdeductible
            FROM absence 
            WHERE id_employe = :id_employe
            AND date_absence BETWEEN :date_debut AND :date_fin
            ORDER BY date_absence
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPointagesMois($id_employe, $dateDebut, $dateFin)
    {
        $stmt = Flight::db()->prepare("
            SELECT 
                id_pointage,
                date_heure_arrive,
                date_heure_depart,
                CASE 
                    WHEN date_heure_depart IS NULL THEN 0
                    WHEN date_heure_depart >= date_heure_arrive THEN 
                        TIMESTAMPDIFF(MINUTE, date_heure_arrive, date_heure_depart)
                    ELSE 
                        -- Cas où le départ est le lendemain (heure de départ < heure d'arrivée)
                        TIMESTAMPDIFF(MINUTE, date_heure_arrive, DATE_ADD(date_heure_depart, INTERVAL 1 DAY))
                END as duree_minutes
            FROM pointage 
            WHERE id_employe = :id_employe
            AND DATE(date_heure_arrive) BETWEEN :date_debut AND :date_fin
            ORDER BY date_heure_arrive
        ");
        $stmt->execute([
            'id_employe' => $id_employe,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function getListeEmployes()
    {
        $stmt = Flight::db()->prepare("
            SELECT e.id_employe, e.nom, e.prenom, p.nom as poste
            FROM employe e
            JOIN contrat c ON c.id_employe = e.id_employe
            JOIN poste p ON p.id_poste = c.id_poste
            WHERE (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            ORDER BY e.nom, e.prenom
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}