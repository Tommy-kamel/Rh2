<?php

namespace app\models\admin;

use Flight;

class MatchingModel
{

    public function getAllPostesForMatching()
    {
        $sql = "
        SELECT 
            p.id_poste,
            p.nom AS nom_poste,
            count(pc.id_competence) AS nb_competences
        FROM poste p
        JOIN poste_libre pl ON pl.id_poste_libre = p.id_poste
        LEFT JOIN poste_competence pc ON pc.id_poste = p.id_poste
        GROUP BY p.id_poste, p.nom
        ORDER BY p.nom
    ";

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function getPosteById($id_poste)
    {
        $sql = "SELECT * FROM poste WHERE id_poste = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_poste]);
        return $stmt->fetch();
    }

    public function getRequiredCompetences($id_poste)
    {
        $sql = "
            SELECT pc.id_poste, c.id_competence, c.nom_competence, pc.niveau_requis
            FROM poste_competence pc
            INNER JOIN competence c ON c.id_competence = pc.id_competence
            WHERE pc.id_poste = ?
            ORDER BY pc.niveau_requis DESC, c.nom_competence
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_poste]);
        return $stmt->fetchAll();
    }

    public function getEmployeesWithCompetences()
    {
        $sql = "
            SELECT 
                e.id_employe,
                e.nom,
                e.prenom,
                e.email,
                e.telephone,
                e.photo,
                p.nom AS nom_poste,
                d.nom_departement,
                ec.id_competence,
                ec.niveau
            FROM employe e
            LEFT JOIN contrat c ON c.id_contrat = (
                SELECT id_contrat FROM contrat WHERE id_employe = e.id_employe ORDER BY date_debut DESC LIMIT 1
            )
            LEFT JOIN poste p ON c.id_poste = p.id_poste
            LEFT JOIN departement d ON c.id_departement = d.id_departement
            LEFT JOIN employe_competence ec ON ec.id_employe = e.id_employe
            ORDER BY e.nom, e.prenom
        ";

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        // grouper
        $employees = [];
        foreach ($rows as $r) {
            $id = $r['id_employe'];
            if (!isset($employees[$id])) {
                $employees[$id] = [
                    'id_employe' => $r['id_employe'],
                    'nom' => $r['nom'],
                    'prenom' => $r['prenom'],
                    'email' => $r['email'],
                    'telephone' => $r['telephone'],
                    'photo' => $r['photo'],
                    'nom_poste' => $r['nom_poste'],
                    'nom_departement' => $r['nom_departement'],
                    'competences' => []
                ];
            }
            if (!empty($r['id_competence'])) {
                $employees[$id]['competences'][(int)$r['id_competence']] = (int)$r['niveau'];
            }
        }

        // ré-index numeric
        return array_values($employees);
    }

    public function insertPosteCompetence($id_poste, $id_competence, $niveau)
    {
        $sql = "INSERT INTO poste_competence (id_poste, id_competence, niveau_requis) VALUES (?, ?, ?)";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_poste, $id_competence, $niveau]);
    }

    public function softDeletePosteLibre($id, $dateHier)
    {
        $sql = "UPDATE poste_libre SET date_expiration = ? WHERE id_poste_libre = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$dateHier, $id]);
    }

// Postes libres actuels
    public function getPostesLibresActuels()
    {
        $sql = "
            SELECT pl.*, p.nom AS nom_poste, d.nom_departement
            FROM poste_libre pl
            INNER JOIN poste p ON p.id_poste = pl.id_poste
            INNER JOIN departement d ON d.id_departement = pl.id_departement
            WHERE pl.date_expiration >= CURDATE()
            ORDER BY pl.date_publication DESC
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        $postes = $stmt->fetchAll();

        foreach ($postes as &$pl) {
            $pl['competences'] = $this->getCompetencesPoste($pl['id_poste']);
            $pl['suggestions_formation'] = $this->getSuggestionsFormation($pl['id_poste']);
        }

        return $postes;
    }

    // Historique postes expirés
    public function getHistoriquePostes()
    {
        $sql = "
            SELECT pl.*, p.nom AS nom_poste, d.nom_departement
            FROM poste_libre pl
            INNER JOIN poste p ON p.id_poste = pl.id_poste
            INNER JOIN departement d ON d.id_departement = pl.id_departement
            WHERE pl.date_expiration < CURDATE()
            ORDER BY pl.date_expiration DESC
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Compétences requises pour un poste
    public function getCompetencesPoste($id_poste)
    {
        $sql = "
            SELECT c.id_competence, c.nom_competence, pc.niveau_requis
            FROM poste_competence pc
            INNER JOIN competence c ON c.id_competence = pc.id_competence
            WHERE pc.id_poste = ?
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_poste]);
        return $stmt->fetchAll();
    }

    // Suggestions de formation pour écarts de compétence
    public function getSuggestionsFormation($id_poste)
    {
        $competences = $this->getCompetencesPoste($id_poste);
        // $suggestions = [];

        // foreach ($competences as $comp) {
        //     $sql = "
        //         SELECT e.nom, e.prenom, ec.niveau AS niveau_employe, ?
        //         FROM employe e
        //         LEFT JOIN employe_competence ec 
        //         ON ec.id_employe = e.id_employe AND ec.id_competence = ?
        //         WHERE ec.niveau IS NULL OR ec.niveau < ?
        //     ";
        //     $stmt = Flight::db()->prepare($sql);
        //     $stmt->execute([$comp['niveau_requis'], $comp['id_competence'], $comp['niveau_requis']]);
        //     $rows = $stmt->fetchAll();

        //     foreach ($rows as $r) {
        //         $manque = $comp['niveau_requis'] - ($r['niveau_employe'] ?? 0);
        //         if ($manque > 0) {
        //             $suggestions[] = $r['prenom'].' '.$r['nom'].' : formation "'.$comp['nom_competence'].'" (manque '.$manque.' niveau)';
        //         }
        //     }
        // }

        return $competences;
    }

    // CRUD : Ajouter
    public function insertPosteLibre($data)
    {
        $sql = "
            INSERT INTO poste_libre 
            (id_poste, id_departement, date_publication, date_expiration, description)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([
            $data['id_poste'],
            $data['id_departement'],
            $data['date_publication'],
            $data['date_expiration'],
            $data['description']
        ]);
    }

    // CRUD : Modifier
    public function updatePosteLibre($id, $data)
    {
        $sql = "
            UPDATE poste_libre SET
            id_poste = ?, id_departement = ?, date_publication = ?, date_expiration = ?, description = ?
            WHERE id_poste_libre = ?
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([
            $data['id_poste'],
            $data['id_departement'],
            $data['date_publication'],
            $data['date_expiration'],
            $data['description'],
            $id
        ]);
    }

    // CRUD : Supprimer
    public function deletePosteLibre($id)
    {
        $sql = "DELETE FROM poste_libre WHERE id_poste_libre = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id]);
    }
}
