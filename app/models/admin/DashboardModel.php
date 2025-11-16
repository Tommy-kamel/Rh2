<?php

namespace app\models\admin;

use Flight;

class DashboardModel
{
    /**
     * Récupère le nombre total d'employés actifs
     */
    public function getTotalEmployesActifs()
    {
        $sql = "SELECT COUNT(DISTINCT e.id_employe) as total
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe
                WHERE c.date_fin >= CURDATE() OR c.date_fin IS NULL";
        
        $stmt = Flight::db()->query($sql);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Récupère le nombre de congés en attente
     */
    public function getCongesEnAttente()
    {
        $sql = "SELECT COUNT(*) as total FROM conge WHERE status = 1";
        
        $stmt = Flight::db()->query($sql);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Récupère le nombre d'absences aujourd'hui
     */
    public function getAbsencesAujourdhui()
    {
        $sql = "SELECT COUNT(*) as total FROM absence WHERE date_absence = CURDATE()";
        
        $stmt = Flight::db()->query($sql);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Récupère le nombre de présents aujourd'hui (ont pointé)
     */
    public function getPresentsAujourdhui()
    {
        $sql = "SELECT COUNT(DISTINCT id_employe) as total 
                FROM pointage 
                WHERE DATE(date_heure_arrive) = CURDATE()";
        
        $stmt = Flight::db()->query($sql);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Récupère toutes les statistiques du dashboard
     */
    public function getStatistiques()
    {
        return [
            'total_employes' => $this->getTotalEmployesActifs(),
            'conges_attente' => $this->getCongesEnAttente(),
            'absences_aujourd_hui' => $this->getAbsencesAujourdhui(),
            'presents_aujourd_hui' => $this->getPresentsAujourdhui()
        ];
    }
}
