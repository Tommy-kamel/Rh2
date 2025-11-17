<?php

namespace app\models\employe;

use Flight;


class EmployeeModel
{
     public function getById($id)
    {
        // Récupère l'employé + dernier contrat (si présent) + poste + département
        $sql = "
            SELECT 
                e.*,
                c.id_contrat,
                c.salaire,
                c.date_debut AS contrat_date_debut,
                c.date_fin AS contrat_date_fin,
                c.type AS contrat_type,
                p.id_poste,
                p.nom AS nom_poste,
                d.id_departement,
                d.nom_departement
            FROM employe e
            LEFT JOIN contrat c ON c.id_contrat = (
                SELECT id_contrat FROM contrat WHERE id_employe = e.id_employe ORDER BY date_debut DESC LIMIT 1
            )
            LEFT JOIN poste p ON c.id_poste = p.id_poste
            LEFT JOIN departement d ON c.id_departement = d.id_departement
            WHERE e.id_employe = :id
            LIMIT 1
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE employe SET 
                    nom = :nom, 
                    prenom = :prenom, 
                    date_naissance = :date_naissance,
                    email = :email, 
                    telephone = :telephone, 
                    adresse = :adresse, 
                    numero_cnaps = :numero_cnaps,
                    sexe = :sexe,
                    cin = :cin, 
                    lieu_naissance = :lieu_naissance, 
                    photo = :photo 
                WHERE id_employe = :id";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'date_naissance' => $data['date_naissance'] ?? null,
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'],
            'numero_cnaps' => $data['numero_cnaps'] ?? null,
            'sexe' => $data['sexe'] ?? null,
            'cin' => $data['cin'],
            'lieu_naissance' => $data['lieu_naissance'],
            'photo' => $data['photo'] ?? null,
            'id' => $id
        ]);
    }


   /**
     * Termine un contrat existant (set date_fin)
     */
    public function endContract($id_contrat, $date_fin = null)
    {
        try {
            $date_fin = $date_fin ?? date('Y-m-d');
            $sql = "UPDATE contrat SET date_fin = :date_fin WHERE id_contrat = :id_contrat";
            $stmt = Flight::db()->prepare($sql);
            $res = $stmt->execute(['date_fin' => $date_fin, 'id_contrat' => $id_contrat]);
            if ($res) {
                // historiser l'état final
                $sql = "SELECT * FROM contrat WHERE id_contrat = :id_contrat";
                $stmt = Flight::db()->prepare($sql);
                $stmt->execute(['id_contrat' => $id_contrat]);
                $contrat = $stmt->fetch();
                if ($contrat) $this->addContratHistory($id_contrat, $contrat['id_employe'], $contrat);
                return true;
            }
            $err = $stmt->errorInfo();
            error_log("endContract failed: " . json_encode($err));
            return false;
        } catch (\Exception $ex) {
            error_log("endContract exception: " . $ex->getMessage());
            return false;
        }
    }


        public function getPosteHistory($id_employe)
    {
        $sql = "SELECT * FROM poste_history WHERE id_employe = ? ORDER BY changed_at DESC";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe]);
        return $stmt->fetchAll();
    }

        public function getContratHistory($id_employe)
    {
        $sql = "SELECT * FROM contrat_history WHERE id_employe = ? ORDER BY changed_at DESC";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe]);
        return $stmt->fetchAll();
    }


        public function getAll()
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
                d.nom_departement
            FROM employe e
            LEFT JOIN contrat c ON c.id_contrat = (
                SELECT id_contrat FROM contrat WHERE id_employe = e.id_employe ORDER BY date_debut DESC LIMIT 1
            )
            LEFT JOIN poste p ON c.id_poste = p.id_poste
            LEFT JOIN departement d ON c.id_departement = d.id_departement
            ORDER BY e.nom, e.prenom
        ";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

        public function getAllPostes()
    {
        $sql = "SELECT id_poste, nom FROM poste ORDER BY nom";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

        public function getAllDepartements()
    {
        $sql = "SELECT id_departement, nom_departement FROM departement ORDER BY nom_departement";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

        public function createContract($id_employe, $data)
    {
        try {
            $sql = "INSERT INTO contrat (id_employe, salaire, date_debut, date_fin, type, id_poste, id_departement, periode_essai_jours, renouvellement_count)
                    VALUES (:id_employe, :salaire, :date_debut, :date_fin, :type, :id_poste, :id_departement, :periode_essai_jours, 0)";
            $stmt = Flight::db()->prepare($sql);
            $res = $stmt->execute([
                'id_employe' => $id_employe,
                'salaire' => $data['salaire'],
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'] ?? null,
                'type' => $data['type'] ?? 'CDI',
                'id_poste' => $data['id_poste'] ?? null,
                'id_departement' => $data['id_departement'] ?? null,
                'periode_essai_jours' => $data['periode_essai_jours'] ?? 0
            ]);
            if ($res) {
                return Flight::db()->lastInsertId();
            }
            $err = $stmt->errorInfo();
            error_log("createContract failed: " . json_encode($err));
            return false;
        } catch (\Exception $ex) {
            error_log("createContract exception: " . $ex->getMessage());
            return false;
        }
    }

        public function renewContract($id_contrat, $newData)
    {
        try {
            // Récupérer contrat courant
            $sql = "SELECT * FROM contrat WHERE id_contrat = :id_contrat";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute(['id_contrat' => $id_contrat]);
            $old = $stmt->fetch();
            if (!$old) {
                error_log("renewContract: contrat introuvable id_contrat={$id_contrat}");
                return false;
            }

            // Clore l'ancien contrat (mettre date_fin à la veille du nouveau début si fourni)
            if (!empty($newData['date_debut'])) {
                $date_fin_old = date('Y-m-d', strtotime($newData['date_debut'] . ' -1 day'));
            } else {
                $date_fin_old = $old['date_fin'] ?? null;
            }

            $sql = "UPDATE contrat SET date_fin = :date_fin WHERE id_contrat = :id_contrat";
            $uStmt = Flight::db()->prepare($sql);
            $uStmt->execute(['date_fin' => $date_fin_old, 'id_contrat' => $id_contrat]);

            // Historiser l'ancien contrat
            $this->addContratHistory($id_contrat, $old['id_employe'], $old);

            // Préparer données du nouveau contrat (fallback sur l'ancien)
            $contractData = [
                'salaire' => $newData['salaire'] ?? $old['salaire'],
                'date_debut' => $newData['date_debut'] ?? date('Y-m-d', strtotime('+1 day')),
                'date_fin' => $newData['date_fin'] ?? null,
                'type' => $newData['type'] ?? $old['type'],
                'id_poste' => $newData['id_poste'] ?? $old['id_poste'],
                'id_departement' => $newData['id_departement'] ?? $old['id_departement'],
                'periode_essai_jours' => $newData['periode_essai_jours'] ?? ($old['periode_essai_jours'] ?? 0)
            ];

            // Créer nouveau contrat
            $newId = $this->createContract($old['id_employe'], $contractData);
            if (!$newId) {
                error_log("renewContract: createContract a échoué pour employe={$old['id_employe']}");
                return false;
            }

            // Mettre à jour compteur de renouvellement (colonne : renouvellement_count)
            $old_count = (int)($old['renouvellement_count'] ?? 0);
            $sql = "UPDATE contrat SET renouvellement_count = :count WHERE id_contrat = :id_contrat";
            Flight::db()->prepare($sql)->execute(['count' => $old_count + 1, 'id_contrat' => $newId]);

            return $newId;
        } catch (\Exception $ex) {
            error_log("renewContract exception: " . $ex->getMessage());
            return false;
        }
    }

        public function addContratHistory($id_contrat, $id_employe, $data)
    {
        try {
            $sql = "INSERT INTO contrat_history (id_contrat, id_employe, salaire, date_debut, date_fin, type, id_poste, id_departement)
                    VALUES (:id_contrat, :id_employe, :salaire, :date_debut, :date_fin, :type, :id_poste, :id_departement)";
            $stmt = Flight::db()->prepare($sql);
            $res = $stmt->execute([
                'id_contrat' => $id_contrat,
                'id_employe' => $id_employe,
                'salaire' => $data['salaire'] ?? null,
                'date_debut' => $data['date_debut'] ?? null,
                'date_fin' => $data['date_fin'] ?? null,
                'type' => $data['type'] ?? null,
                'id_poste' => $data['id_poste'] ?? null,
                'id_departement' => $data['id_departement'] ?? null
            ]);
            if (!$res) {
                $err = $stmt->errorInfo();
                error_log("addContratHistory failed: " . json_encode($err));
            }
            return $res;
        } catch (\Exception $ex) {
            error_log("addContratHistory exception: " . $ex->getMessage());
            return false;
        }
    }

        public function addPosteHistory($id_employe, $data)
    {
        try {
            $sql = "INSERT INTO poste_history (id_employe, id_poste, id_departement, date_debut, date_fin, motif)
                    VALUES (:id_employe, :id_poste, :id_departement, :date_debut, :date_fin, :motif)";
            $stmt = Flight::db()->prepare($sql);
            $res = $stmt->execute([
                'id_employe' => $id_employe,
                'id_poste' => $data['id_poste'] ?? null,
                'id_departement' => $data['id_departement'] ?? null,
                'date_debut' => $data['date_debut'] ?? null,
                'date_fin' => $data['date_fin'] ?? null,
                'motif' => $data['motif'] ?? null
            ]);
            if (!$res) {
                $err = $stmt->errorInfo();
                error_log("addPosteHistory failed: " . json_encode($err));
            }
            return (bool) $res;
        } catch (\Exception $ex) {
            error_log("addPosteHistory exception: " . $ex->getMessage());
            return false;
        }
    }

        public function create($data)
    {
        try {
            $sql = "INSERT INTO employe 
                (nom, prenom, date_naissance, email, mot_de_passe, sexe, telephone, adresse, numero_cnaps, cin, lieu_naissance, photo)
                VALUES (:nom, :prenom, :date_naissance, :email, :mot_de_passe, :sexe, :telephone, :adresse, :numero_cnaps, :cin, :lieu_naissance, :photo)";
            $stmt = Flight::db()->prepare($sql);
            $res = $stmt->execute([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'date_naissance' => $data['date_naissance'] ?? null,
                'email' => $data['email'] ?? null,
                'mot_de_passe' => $data['mot_de_passe'] ?? null,
                'sexe' => $data['sexe'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'adresse' => $data['adresse'] ?? null,
                'numero_cnaps' => $data['numero_cnaps'] ?? null,
                'cin' => $data['cin'] ?? null,
                'lieu_naissance' => $data['lieu_naissance'] ?? null,
                'photo' => $data['photo'] ?? null
            ]);
            if ($res) {
                return Flight::db()->lastInsertId();
            }
            $err = $stmt->errorInfo();
            error_log("EmployeeModel::create failed: " . json_encode($err));
            return false;
        } catch (\Exception $ex) {
            error_log("EmployeeModel::create exception: " . $ex->getMessage());
            return false;
        }
    }

}