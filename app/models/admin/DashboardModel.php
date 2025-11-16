<?php

namespace app\models\admin;

use Flight;

class DashboardModel
{
    /**
     * Récupère le nombre total d'employés actifs
     */
    public function getTotalEmployesActifs($id_departement = null)
    {
        $sql = "SELECT COUNT(DISTINCT e.id_employe) as total
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe";
        
        $params = [];
        if ($id_departement !== null) {
            $sql .= " WHERE c.id_departement = ? AND (c.date_fin >= CURDATE() OR c.date_fin IS NULL)";
            $params[] = $id_departement;
        } else {
            $sql .= " WHERE c.date_fin >= CURDATE() OR c.date_fin IS NULL";
        }
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Récupère le nombre de congés en attente
     */
    public function getCongesEnAttente($id_departement = null)
    {
        $sql = "SELECT COUNT(*) as total FROM conge c
                INNER JOIN employe e ON c.id_employe = e.id_employe
                INNER JOIN contrat ct ON e.id_employe = ct.id_employe";
        
        $params = [];
        if ($id_departement !== null) {
            $sql .= " WHERE c.status = 1 AND ct.id_departement = ?";
            $params[] = $id_departement;
        } else {
            // Pour RH, compter les congés validés par département (status = 11)
            $sql .= " WHERE c.status = 11";
        }
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Récupère le nombre d'absences aujourd'hui
     */
    public function getAbsencesAujourdhui($id_departement = null)
    {
        $sql = "SELECT COUNT(*) as total FROM absence a
                INNER JOIN employe e ON a.id_employe = e.id_employe
                INNER JOIN contrat c ON e.id_employe = c.id_employe";
        
        $params = [];
        if ($id_departement !== null) {
            $sql .= " WHERE a.date_absence = CURDATE() AND c.id_departement = ?";
            $params[] = $id_departement;
        } else {
            $sql .= " WHERE a.date_absence = CURDATE()";
        }
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Récupère le nombre de présents aujourd'hui (ont pointé)
     */
    public function getPresentsAujourdhui($id_departement = null)
    {
        $sql = "SELECT COUNT(DISTINCT pt.id_employe) as total 
                FROM pointage pt
                INNER JOIN employe e ON pt.id_employe = e.id_employe
                INNER JOIN contrat c ON e.id_employe = c.id_employe";
        
        $params = [];
        if ($id_departement !== null) {
            $sql .= " WHERE DATE(pt.date_heure_arrive) = CURDATE() AND c.id_departement = ?";
            $params[] = $id_departement;
        } else {
            $sql .= " WHERE DATE(pt.date_heure_arrive) = CURDATE()";
        }
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    public function listeCongeEnAttente($id_departement = null){
        if ($id_departement !== null) {
            $sql = "SELECT * FROM vue_conge_en_attente WHERE id_departement = ?";
            $params = [$id_departement];
        } else {
            $sql = "SELECT * FROM vue_conge_en_attente_rh";
            $params = [];
        }
        
        $sql .= " ORDER BY date_demande DESC";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function validerConge($id_conge, $id_departement) {
        if ($id_departement == 1) {
            $sql = "UPDATE conge SET status = 21 WHERE id_conge = :id_conge";
        }else{
            $sql = "UPDATE conge SET status = 11 WHERE id_conge = :id_conge";
        }
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute(['id_conge' => $id_conge]);
    }

    public function refuserConge($id_conge) {
        $sql = "UPDATE conge SET status = 0 WHERE id_conge = :id_conge";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute(['id_conge' => $id_conge]);
    }

    public function getDetailsConge($id_conge) {
        $sql = "SELECT * FROM vue_conge_en_attente WHERE id_conge = :id_conge";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id_conge' => $id_conge]);
        return $stmt->fetch();
    }

    public function voirDetailsConge($id_conge, $id_departement) {
        // Pour RH (id_departement = 1), utiliser la vue spécifique RH
        if ($id_departement === 1 || $id_departement === null) {
            $sql = "SELECT * FROM vue_conge_en_attente_rh WHERE id_conge = :id_conge";
            $params = ['id_conge' => $id_conge];
        } else {
            // Pour les autres départements, filtrer par département
            $sql = "SELECT * FROM vue_conge_en_attente WHERE id_conge = :id_conge AND id_departement = :id_departement";
            $params = ['id_conge' => $id_conge, 'id_departement' => $id_departement];
        }
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function modifierConge($id_conge, $date_debut, $date_fin, $type_conge, $motif, $nb_jours = null) {
        // Note: nb_jours n'est pas stocké dans la base, on le calcule à la volée
        $sql = "UPDATE conge
                SET date_debut = :date_debut, date_fin = :date_fin, raison = :motif, status = 1
                WHERE id_conge = :id_conge";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'motif' => $motif,
            'id_conge' => $id_conge
        ]);
    }

    /**
     * Récupère toutes les statistiques du dashboard
     */
    public function getStatistiques($id_departement = null)
    {
        return [
            'total_employes' => $this->getTotalEmployesActifs($id_departement),
            'conges_attente' => $this->getCongesEnAttente($id_departement),
            'absences_aujourd_hui' => $this->getAbsencesAujourdhui($id_departement),
            'presents_aujourd_hui' => $this->getPresentsAujourdhui($id_departement),
            'liste_conge' => $this->listeCongeEnAttente($id_departement),
        ];
    }
}
