<?php

namespace app\models\paiement;

use Flight;

class PaiementEmployeModel {
    public function getAllEmployes() {
        $sql = "SELECT e.id_employe, e.nom, e.prenom, c.salaire, e.numero_cnaps,
                       c.date_debut, p.nom AS poste, d.nom_departement
                FROM employe e
                JOIN contrat c ON e.id_employe = c.id_employe
                JOIN poste p ON c.id_poste = p.id_poste
                JOIN departement d ON c.id_departement = d.id_departement
                WHERE c.date_fin IS NULL OR c.date_fin >= CURDATE()
                ORDER BY e.nom";

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        $stmt = Flight::db()->prepare("SELECT * FROM employes WHERE id_employe = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}   