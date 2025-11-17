<?php

namespace app\models\admin;

use Flight;

class CalendrierModel
{
    /**
     * Récupère tous les congés d'un mois donné
     */
    public function getCongesDuMois($mois, $annee, $id_departement = null)
    {
        $sql = "SELECT 
                    c.id_conge,
                    c.date_debut,
                    c.date_fin,
                    c.status,
                    e.id_employe,
                    e.nom,
                    e.prenom,
                    tc.type as type_conge,
                    d.nom_departement,
                    d.id_departement
                FROM conge c
                JOIN employe e ON c.id_employe = e.id_employe
                JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge
                JOIN contrat ct ON e.id_employe = ct.id_employe
                JOIN departement d ON ct.id_departement = d.id_departement
                WHERE (
                    (YEAR(c.date_debut) = :annee AND MONTH(c.date_debut) = :mois)
                    OR (YEAR(c.date_fin) = :annee AND MONTH(c.date_fin) = :mois)
                    OR (c.date_debut <= :date_fin AND c.date_fin >= :date_debut)
                )";
        
        $params = [
            'mois' => $mois,
            'annee' => $annee,
            'date_debut' => "$annee-$mois-01",
            'date_fin' => date("Y-m-t", strtotime("$annee-$mois-01"))
        ];
        
        // Filtrer par département si spécifié (sauf pour RH qui voit tout)
        if ($id_departement !== null && $id_departement != 1) {
            $sql .= " AND d.id_departement = :id_departement";
            $params['id_departement'] = $id_departement;
        }
        
        $sql .= " ORDER BY c.date_debut ASC";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les congés pour une date spécifique
     */
    public function getCongesPourDate($date, $id_departement = null)
    {
        $sql = "SELECT 
                    c.id_conge,
                    c.date_debut,
                    c.date_fin,
                    c.status,
                    e.id_employe,
                    e.nom,
                    e.prenom,
                    tc.type as type_conge,
                    d.nom_departement
                FROM conge c
                JOIN employe e ON c.id_employe = e.id_employe
                JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge
                JOIN contrat ct ON e.id_employe = ct.id_employe
                JOIN departement d ON ct.id_departement = d.id_departement
                WHERE :date BETWEEN c.date_debut AND c.date_fin";
        
        $params = ['date' => $date];
        
        if ($id_departement !== null && $id_departement != 1) {
            $sql .= " AND d.id_departement = :id_departement";
            $params['id_departement'] = $id_departement;
        }
        
        $sql .= " ORDER BY e.nom, e.prenom ASC";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
