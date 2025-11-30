<?php

namespace app\models\admin;

use Flight;

class CompetenceModel
{

    public function getCompetenceStats()
    {
        $sql = "
            SELECT 
                c.id_competence,
                c.nom_competence,
                COUNT(ec.id_employe) AS nb_employes,
                IFNULL(AVG(ec.niveau), 0) AS niveau_moyen
            FROM competence c
            LEFT JOIN employe_competence ec 
                ON ec.id_competence = c.id_competence
            GROUP BY c.id_competence, c.nom_competence
            ORDER BY nb_employes DESC;
        ";

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCompetenceById($id)
    {
        $sql = "SELECT * FROM competence WHERE id_competence = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ---- Détails d'une compétence : employés qui la possèdent ----
    public function getEmployeesByCompetence($id_competence)
    {
        $sql = "
            SELECT 
                e.id_employe,
                e.nom,
                e.prenom,
                e.email,
                e.telephone,
                e.photo,
                ec.niveau AS niveau_actuel,
                p.nom AS nom_poste,
                d.nom_departement
            FROM employe_competence ec
            INNER JOIN employe e ON e.id_employe = ec.id_employe
            LEFT JOIN contrat c ON c.id_contrat = (
                SELECT id_contrat FROM contrat 
                WHERE id_employe = e.id_employe 
                ORDER BY date_debut DESC LIMIT 1
            )
            LEFT JOIN poste p ON c.id_poste = p.id_poste
            LEFT JOIN departement d ON c.id_departement = d.id_departement
            WHERE ec.id_competence = ?
            ORDER BY ec.niveau DESC
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_competence]);
        return $stmt->fetchAll();
    }

    // ---- Dashboard global : employés et leurs écarts ----
    public function getEmployeesCompetencesSummary()
    {
        $sql = "
            SELECT 
                e.id_employe,
                e.nom,
                e.prenom,
                COUNT(ec.id_competence) AS nb_competences,
                SUM(CASE WHEN ec.niveau < IFNULL(pc.niveau_requis,0) THEN 1 ELSE 0 END) AS nb_gaps
            FROM employe e
            LEFT JOIN employe_competence ec ON ec.id_employe = e.id_employe
            LEFT JOIN poste_competence pc ON pc.id_competence = ec.id_competence
            GROUP BY e.id_employe, e.nom, e.prenom
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ---- Détails employé ----
    public function getEmployeeById($id)
    {
        $sql = "SELECT * FROM employe WHERE id_employe = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getEmployeeCompetencesWithGaps($id)
    {
        $sql = "
            SELECT 
                c.nom_competence,
                ec.niveau AS niveau_actuel,
                pc.niveau_requis,
                CASE WHEN ec.niveau < pc.niveau_requis THEN 'Oui' ELSE 'Non' END AS besoin_formation,
                CASE WHEN ec.niveau < pc.niveau_requis THEN CONCAT('Formation recommandée: ', c.nom_competence) ELSE NULL END AS suggestion_formation
            FROM employe_competence ec
            INNER JOIN competence c ON c.id_competence = ec.id_competence
            LEFT JOIN poste_competence pc ON pc.id_competence = c.id_competence
            WHERE ec.id_employe = ?
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    // ---- Total des compétences ----
    public function getTotalCompetences()
    {
        $sql = "SELECT COUNT(*) as total FROM competence";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // ---- Total des écarts ----
    public function getTotalGaps()
    {
        $sql = "
            SELECT COUNT(*) AS total_gaps
            FROM employe_competence ec
            INNER JOIN poste_competence pc ON pc.id_competence = ec.id_competence
            WHERE ec.niveau < pc.niveau_requis
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total_gaps'];
    }

    public function getCompetencesComparisonForPoste($id_employe, $id_poste)
    {
        $sql = "
        SELECT 
            pr.id_competence,
            c.nom_competence,
            pr.niveau_requis,
            ec.niveau AS niveau_employe,
            CASE 
                WHEN ec.niveau IS NULL THEN 'N/A'
                WHEN ec.niveau >= pr.niveau_requis THEN 'Pas besoin'
                ELSE 'Formation recommandée'
            END AS besoin_formation
        FROM poste_competence pr
        INNER JOIN competence c ON c.id_competence = pr.id_competence
        LEFT JOIN employe_competence ec 
            ON ec.id_competence = pr.id_competence AND ec.id_employe = ?
        WHERE pr.id_poste = ?
        ORDER BY c.nom_competence
    ";

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe, $id_poste]);
        return $stmt->fetchAll();
    }

    // Fonction pour récupérer toutes les compétences de l'employé (pour le pie chart)
    public function getAllCompetencesOfEmployee($id_employe)
    {
        $sql = "
        SELECT 
            c.nom_competence,
            ec.niveau
        FROM employe_competence ec
        INNER JOIN competence c ON c.id_competence = ec.id_competence
        WHERE ec.id_employe = ?
    ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe]);
        return $stmt->fetchAll();
    }
}
