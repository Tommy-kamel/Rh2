<?php

namespace app\models\admin;

use Flight;

class StatistiquesModel
{
    /**
     * Récupère les statistiques des effectifs par genre
     * @param int|null $id_departement - Si null, retourne pour tous les départements (RH)
     */
    public function getEffectifsParGenre($id_departement = null)
    {
        $sql = "SELECT 
                    e.sexe as genre,
                    COUNT(*) as nombre
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe
                WHERE (CURDATE() BETWEEN c.date_debut AND c.date_fin OR c.date_fin IS NULL)";
        
        if ($id_departement !== null) {
            $sql .= " AND c.id_departement = :id_departement";
        }
        
        $sql .= " GROUP BY e.sexe";
        
        $stmt = Flight::db()->prepare($sql);
        if ($id_departement !== null) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Récupère les statistiques des effectifs par tranche d'âge
     * @param int|null $id_departement - Si null, retourne pour tous les départements (RH)
     */
    public function getEffectifsParAge($id_departement = null)
    {
        $sql = "SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(YEAR, e.date_naissance, CURDATE()) < 25 THEN 'Moins de 25 ans'
                        WHEN TIMESTAMPDIFF(YEAR, e.date_naissance, CURDATE()) BETWEEN 25 AND 34 THEN '25-34 ans'
                        WHEN TIMESTAMPDIFF(YEAR, e.date_naissance, CURDATE()) BETWEEN 35 AND 44 THEN '35-44 ans'
                        WHEN TIMESTAMPDIFF(YEAR, e.date_naissance, CURDATE()) BETWEEN 45 AND 54 THEN '45-54 ans'
                        ELSE '55 ans et plus'
                    END as tranche_age,
                    COUNT(*) as nombre
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe
                WHERE (CURDATE() BETWEEN c.date_debut AND c.date_fin OR c.date_fin IS NULL)";
        
        if ($id_departement !== null) {
            $sql .= " AND c.id_departement = :id_departement";
        }
        
        $sql .= " GROUP BY tranche_age
                ORDER BY 
                    CASE tranche_age
                        WHEN 'Moins de 25 ans' THEN 1
                        WHEN '25-34 ans' THEN 2
                        WHEN '35-44 ans' THEN 3
                        WHEN '45-54 ans' THEN 4
                        WHEN '55 ans et plus' THEN 5
                    END";
        
        $stmt = Flight::db()->prepare($sql);
        if ($id_departement !== null) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Récupère les statistiques des effectifs par département
     * @param int|null $id_departement - Si null, retourne tous les départements (RH)
     */
    public function getEffectifsParDepartement($id_departement = null)
    {
        $sql = "SELECT 
                    d.nom_departement,
                    COUNT(*) as nombre
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe
                INNER JOIN departement d ON c.id_departement = d.id_departement
                WHERE (CURDATE() BETWEEN c.date_debut AND c.date_fin OR c.date_fin IS NULL)";
        
        if ($id_departement !== null) {
            $sql .= " AND c.id_departement = :id_departement";
        }
        
        $sql .= " GROUP BY d.id_departement, d.nom_departement
                ORDER BY nombre DESC";
        
        $stmt = Flight::db()->prepare($sql);
        if ($id_departement !== null) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Récupère les statistiques des effectifs par type de contrat
     * @param int|null $id_departement - Si null, retourne pour tous les départements (RH)
     */
    public function getEffectifsParTypeContrat($id_departement = null)
    {
        $sql = "SELECT 
                    c.type as nom_type_contrat,
                    COUNT(*) as nombre
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe
                WHERE (CURDATE() BETWEEN c.date_debut AND c.date_fin OR c.date_fin IS NULL)";
        
        if ($id_departement !== null) {
            $sql .= " AND c.id_departement = :id_departement";
        }
        
        $sql .= " GROUP BY c.type
                ORDER BY nombre DESC";
        
        $stmt = Flight::db()->prepare($sql);
        if ($id_departement !== null) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Récupère un résumé global des effectifs
     * @param int|null $id_departement - Si null, retourne pour tous les départements (RH)
     */
    public function getResumeEffectifs($id_departement = null)
    {
        $sql = "SELECT 
                    COUNT(DISTINCT e.id_employe) as total_employes,
                    COUNT(DISTINCT CASE WHEN e.sexe = 'Homme' THEN e.id_employe END) as total_hommes,
                    COUNT(DISTINCT CASE WHEN e.sexe = 'Femme' THEN e.id_employe END) as total_femmes,
                    ROUND(AVG(TIMESTAMPDIFF(YEAR, e.date_naissance, CURDATE())), 1) as age_moyen,
                    COUNT(DISTINCT d.id_departement) as total_departements
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe
                LEFT JOIN departement d ON c.id_departement = d.id_departement
                WHERE (CURDATE() BETWEEN c.date_debut AND c.date_fin OR c.date_fin IS NULL)";
        
        if ($id_departement !== null) {
            $sql .= " AND c.id_departement = :id_departement";
        }
        
        $stmt = Flight::db()->prepare($sql);
        if ($id_departement !== null) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetch();
    }

    /**
     * Récupère les statistiques croisées département/genre
     * @param int|null $id_departement - Si null, retourne pour tous les départements (RH)
     */
    public function getEffectifsDepartementParGenre($id_departement = null)
    {
        $sql = "SELECT 
                    d.nom_departement,
                    e.sexe as genre,
                    COUNT(*) as nombre
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe
                INNER JOIN departement d ON c.id_departement = d.id_departement
                WHERE (CURDATE() BETWEEN c.date_debut AND c.date_fin OR c.date_fin IS NULL)";
        
        if ($id_departement !== null) {
            $sql .= " AND c.id_departement = :id_departement";
        }
        
        $sql .= " GROUP BY d.id_departement, d.nom_departement, e.sexe
                ORDER BY d.nom_departement, e.sexe";
        
        $stmt = Flight::db()->prepare($sql);
        if ($id_departement !== null) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Récupère les statistiques par poste
     * @param int|null $id_departement - Si null, retourne pour tous les départements (RH)
     */
    public function getEffectifsParPoste($id_departement = null)
    {
        $sql = "SELECT 
                    p.nom,
                    COUNT(*) as nombre,
                    d.nom_departement
                FROM employe e
                INNER JOIN contrat c ON e.id_employe = c.id_employe
                INNER JOIN poste p ON c.id_poste = p.id_poste
                LEFT JOIN departement d ON c.id_departement = d.id_departement
                WHERE (CURDATE() BETWEEN c.date_debut AND c.date_fin OR c.date_fin IS NULL)";
        
        if ($id_departement !== null) {
            $sql .= " AND c.id_departement = :id_departement";
        }
        
        $sql .= " GROUP BY p.id_poste, p.nom, d.nom_departement
                ORDER BY nombre DESC
                LIMIT 10";
        
        $stmt = Flight::db()->prepare($sql);
        if ($id_departement !== null) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Récupère l'évolution des effectifs par mois (12 derniers mois)
     * @param int|null $id_departement - Si null, retourne pour tous les départements (RH)
     */
    public function getEvolutionEffectifs($id_departement = null)
    {
        $sql = "SELECT 
                    DATE_FORMAT(c.date_debut, '%Y-%m') as mois,
                    COUNT(*) as embauches,
                    (SELECT COUNT(*) 
                     FROM contrat c2 
                     WHERE DATE_FORMAT(c2.date_fin, '%Y-%m') = DATE_FORMAT(c.date_debut, '%Y-%m')
                     AND c2.date_fin IS NOT NULL";
        
        if ($id_departement !== null) {
            $sql .= " AND c2.id_departement = :id_departement";
        }
        
        $sql .= ") as departs
                FROM contrat c
                WHERE c.date_debut >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        
        if ($id_departement !== null) {
            $sql .= " AND c.id_departement = :id_departement";
        }
        
        $sql .= " GROUP BY DATE_FORMAT(c.date_debut, '%Y-%m')
                ORDER BY mois DESC
                LIMIT 12";
        
        $stmt = Flight::db()->prepare($sql);
        if ($id_departement !== null) {
            $stmt->execute(['id_departement' => $id_departement]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }
}
