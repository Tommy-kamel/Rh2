<?php

namespace app\models\employe;

use Flight;

class CongeModel
{
    public function addConge($id_employe, $id_type_conge ,$date_demande, $date_debut, $date_fin, $raison, $date_validation) {
        $sql = "INSERT INTO conge (id_employe, id_type_conge, date_demande, date_debut, date_fin, raison, date_validation, status)
                VALUES (:id_employe, :id_type_conge, :date_demande, :date_debut, :date_fin, :raison, :date_validation, 1)";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            'id_employe' => $id_employe,
            'id_type_conge' => $id_type_conge,
            'date_demande' => $date_demande,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'raison' => $raison,
            'date_validation' => $date_validation
        ]);
    }
}
