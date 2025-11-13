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
            $sql .= " WHERE c.status = 1";
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
        $sql = "SELECT * FROM vue_conge_en_attente";
        $params = [];
        
        if ($id_departement !== null) {
            $sql .= " WHERE id_departement = ?";
            $params[] = $id_departement;
        }
        
        $sql .= " ORDER BY date_demande DESC";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
