<?php

namespace app\models\paiement;

use flight;
use PDO;

class PrimeGlobalModel
{
    // ... vos fonctions existantes ...

    /**
     * Récupère tous les employés actifs pour le select
     */
    public function getAllEmployesActifs()
    {
        $sql = "SELECT id_employe, nom, prenom
                FROM employe 
                ORDER BY nom, prenom";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute une nouvelle prime
     */
    public function ajouterPrime($data)
    {
        $sql = "INSERT INTO prime_divers (id_employe, type, motif, montant_prime, date_prime) 
                VALUES (:id_employe, :type, :motif, :montant_prime, :date_prime)";
        
        $stmt = Flight::db()->prepare($sql);
        
        return $stmt->execute([
            ':id_employe' => $data['id_employe'],
            ':type' => $data['type_prime'],
            ':motif' => $data['motif'],
            ':montant_prime' => $data['montant_prime'],
            ':date_prime' => $data['date_prime']
        ]);
    }

    /**
     * Vérifie si une prime similaire existe déjà
     */
    public function primeExiste($id_employe, $type, $motif, $date_prime, $montant_prime)
    {
        $sql = "SELECT COUNT(*) as count FROM prime_divers 
                WHERE id_employe = :id_employe 
                AND type = :type 
                AND motif = :motif 
                AND date_prime = :date_prime 
                AND montant_prime = :montant_prime";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([
            ':id_employe' => $id_employe,
            ':type' => $type,
            ':motif' => $motif,
            ':date_prime' => $date_prime,
            ':montant_prime' => $montant_prime
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Toutes les primes sur toute la durée du contrat
  public function getAllPrimes($id_employe)
  {
    $sql = "SELECT 
                date_prime AS date,
                type,
                motif,
                montant_prime
            FROM prime_divers
            WHERE id_employe = ?
            ORDER BY date_prime DESC";

    $stmt = Flight::db()->prepare($sql);
    $stmt->execute([$id_employe]);
    return $stmt->fetchAll();
  }

  public function getSalaireActuel($id_employe)
  {
    $sql = "SELECT salaire FROM contrat WHERE id_employe = ? AND (date_fin IS NULL OR date_fin >= CURDATE()) LIMIT 1";
    $stmt = Flight::db()->prepare($sql);
    $stmt->execute([$id_employe]);
    $row = $stmt->fetch();
    return $row ? $row['salaire'] : 0;
  }

  public function getAllPrimesGlobal($employe_id = null, $search = '', $order = 'DESC')
  {
    $sql = "SELECT 
                pd.date_prime,
                pd.type,
                pd.motif,
                pd.montant_prime,
                e.id_employe,
                CONCAT(e.nom, ' ', e.prenom) AS employe_nom
            FROM prime_divers pd
            JOIN employe e ON pd.id_employe = e.id_employe
            WHERE 1=1";

    $params = [];

    if ($employe_id) {
      $sql .= " AND pd.id_employe = ?";
      $params[] = $employe_id;
    }

    if ($search) {
      $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR pd.motif LIKE ?)";
      $like = "%$search%";
      $params[] = $like;
      $params[] = $like;
      $params[] = $like;
    }

    $sql .= " ORDER BY pd.date_prime " . ($order === 'ASC' ? 'ASC' : 'DESC');

    $stmt = Flight::db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public function getAllEmployesForSelect()
  {
    $sql = "SELECT id_employe, CONCAT(nom, ' ', prenom) AS nom_complet 
            FROM employe 
            ORDER BY nom";
    $stmt = Flight::db()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}