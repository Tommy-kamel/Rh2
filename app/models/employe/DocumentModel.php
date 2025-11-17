<?php

namespace app\models\employe;

use Flight;

class DocumentModel
{
    public function addDocument($id_employe, $nom, $chemin, $type)
    {
        $sql = "INSERT INTO documents (id_employe, nom_document, chemin_document, type_document) VALUES (:id_employe, :nom, :chemin, :type)";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            'id_employe' => $id_employe,
            'nom' => $nom,
            'chemin' => $chemin,
            'type' => $type
        ]);
    }

    public function listByEmployee($id_employe)
    {
        $sql = "SELECT * FROM documents WHERE id_employe = :id ORDER BY uploaded_at DESC";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id' => $id_employe]);
        return $stmt->fetchAll();
    }
}