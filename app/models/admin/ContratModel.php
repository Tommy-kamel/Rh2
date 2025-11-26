<?php

namespace app\models\admin;

use Flight;

class ContratModel
{
    public function getContratsExpirantProchainement()
    {
        $sql = "SELECT * FROM vue_get_fin_contrat_moins_1_mois";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
