<?php

namespace app\models\employe;

use Flight;

class DashboardModel
{
    /**
     * Récupère les dernières demandes de congé d'un employé
     */
    public function getDernieresDemandesConge($id_employe)
    {
        $sql = "SELECT c.*, tc.type 
                FROM conge c
                LEFT JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge
                WHERE c.id_employe = :id_employe
                ORDER BY c.date_demande DESC
                LIMIT 5";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        $demandes = $stmt->fetchAll();

        // Calculer la durée des congés
        foreach ($demandes as &$demande) {
            $debut = new \DateTime($demande['date_debut']);
            $fin = new \DateTime($demande['date_fin']);
            $demande['duree'] = $debut->diff($fin)->days + 1;
        }

        return $demandes;
    }

    /**
     * Récupère les derniers pointages d'un employé
     */
    public function getDerniersPointages($id_employe)
    {
        $sql = "SELECT * FROM pointage 
                WHERE id_employe = :id_employe
                ORDER BY date_heure_arrive DESC
                LIMIT 7";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        
        return $stmt->fetchAll();
    }

    /**
     * Calcule les heures travaillées ce mois
     */
    public function getHeuresTravailleesMois($id_employe)
    {
        $sql = "SELECT SUM(TIMESTAMPDIFF(HOUR, date_heure_arrive, date_heure_depart)) as total
                FROM pointage
                WHERE id_employe = :id_employe
                AND MONTH(date_heure_arrive) = MONTH(CURDATE())
                AND YEAR(date_heure_arrive) = YEAR(CURDATE())
                AND date_heure_depart IS NOT NULL";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Récupère le nombre de demandes en attente
     */
    public function getDemandesEnAttente($id_employe)
    {
        $sql = "SELECT COUNT(*) as nb FROM conge WHERE id_employe = :id_employe AND status = 1";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        $result = $stmt->fetch();
        
        return $result['nb'] ?? 0;
    }

    /**
     * Récupère le nombre de documents
     */
    public function getNombreDocuments($id_employe)
    {
        $sql = "SELECT COUNT(*) as nb FROM documents WHERE id_employe = :id_employe";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        $result = $stmt->fetch();
        
        return $result['nb'] ?? 0;
    }

    public function getNombreJourCongeRestantAnnee($id_employe)
    {
        $sql = "SELECT duree_totale_conges_jours FROM vue_nombre_conge WHERE id_employe = :id_employe";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        $result = $stmt->fetch();
        $nombre_vue_conge = $result['duree_totale_conges_jours'] ?? 0;
        $reste = 30 - $nombre_vue_conge;
        return $reste >= 0 ? $reste : 0;
    }

    public function getTypeConge() {
        $sql = "SELECT * FROM type_conge";
        $stmt = Flight::db()->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Récupère toutes les données du dashboard employé
     */
    public function getDonneesDashboard($id_employe)
    {
        return [
            'dernieres_demandes' => $this->getDernieresDemandesConge($id_employe),
            'derniers_pointages' => $this->getDerniersPointages($id_employe),
            'solde_conges' => $this->getNombreJourCongeRestantAnnee($id_employe),
            'heures_travaillees' => $this->getHeuresTravailleesMois($id_employe),
            'demandes_attente' => $this->getDemandesEnAttente($id_employe),
            'nb_documents' => $this->getNombreDocuments($id_employe),
            'type_conges' => $this->getTypeConge()
        ];
    }
}
