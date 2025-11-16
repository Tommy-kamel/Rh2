<?php

namespace app\models\paiement;

use Flight;

class DetailPaiementModel
{

  public function getAbsencesRetards($id, $mois, $annee)
  {
    $sql = "SELECT 'Absence' AS type, date_absence AS date, 
                       IF(estdeductible, 'Déductible', 'Non déductible') AS detail
                FROM absence 
                WHERE id_employe = ? AND MONTH(date_absence) = ? AND YEAR(date_absence) = ?
                
                UNION ALL
                
                SELECT 'Retard' AS type, date_retard AS date, 
                       CONCAT(duree_retard, ' min') AS detail
                FROM retard 
                WHERE id_employe = ? AND MONTH(date_retard) = ? AND YEAR(date_retard) = ?
                
                ORDER BY date DESC";

    $stmt = Flight::db()->prepare($sql);
    $stmt->execute([$id, $mois, $annee, $id, $mois, $annee]);
    return $stmt->fetchAll();
  }

  public function getHeuresSup($id, $mois, $annee)
  {
    $sql = "SELECT 
                hse.date_heure_sup AS date,
                hse.nombre_minutes,
                hs.pourcentage_majoration,
                hs.type
            FROM heure_sup_employe hse
            JOIN heure_sup hs ON hse.id_heure_sup = hs.id_heure_sup
            WHERE hse.id_employe = ? 
              AND MONTH(hse.date_heure_sup) = ? 
              AND YEAR(hse.date_heure_sup) = ?
              AND hs.type != 'nuit'
            ORDER BY hse.date_heure_sup ASC";

    $stmt = Flight::db()->prepare($sql);
    $stmt->execute([$id, $mois, $annee]);
    $rows = $stmt->fetchAll();

    $result = [];
    $total_minutes_jour = 0;
    $current_date = null;

    foreach ($rows as $row) {
      $date = date('Y-m-d', strtotime($row['date']));

      if ($current_date !== $date) {
        $current_date = $date;
        $total_minutes_jour = 0;
      }

      $minutes = $row['nombre_minutes'];
      $reste = $minutes;

      // 30% pour les 8 premières heures (480 min)
      if ($total_minutes_jour < 480 && $row['pourcentage_majoration'] == 30) {
        $minutes_30 = min($reste, 480 - $total_minutes_jour);
        if ($minutes_30 > 0) {
          $result[] = [
            'date' => $date,
            'minutes' => $minutes_30,
            'majoration' => 30,
            'type' => $row['type']
          ];
          $total_minutes_jour += $minutes_30;
          $reste -= $minutes_30;
        }
      }

      // Tout le reste : 50%
      if ($reste > 0) {
        $result[] = [
          'date' => $date,
          'minutes' => $reste,
          'majoration' => 50,
          'type' => $row['type']
        ];
        $total_minutes_jour += $reste;
      }
    }

    // Trier par date DESC
    usort($result, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

    return $result;
  }

  public function getHeuresNuit($id, $mois, $annee)
  {
    $sql = "SELECT hse.date_heure_sup AS date, 
                       hse.nombre_minutes AS minutes
                FROM heure_sup_employe hse
                JOIN heure_sup hs ON hse.id_heure_sup = hs.id_heure_sup
                WHERE hse.id_employe = ? 
                  AND hs.type = 'nuit'
                  AND MONTH(hse.date_heure_sup) = ? 
                  AND YEAR(hse.date_heure_sup) = ?
                ORDER BY hse.date_heure_sup DESC";

    $stmt = Flight::db()->prepare($sql);
    $stmt->execute([$id, $mois, $annee]);
    return $stmt->fetchAll();
  }

  public function getPrimes($id, $mois, $annee)
  {
    $sql = "SELECT type, motif, montant_prime, date_prime
                FROM prime_divers 
                WHERE id_employe = ? 
                  AND MONTH(date_prime) = ? 
                  AND YEAR(date_prime) = ?
                ORDER BY date_prime DESC";

    $stmt = Flight::db()->prepare($sql);
    $stmt->execute([$id, $mois, $annee]);
    return $stmt->fetchAll();
  }

  // Liste des IRSA (mois par mois) pour un employé
  public function getHistoriqueIRSA($id_employe)
  {
    $sql = "SELECT 
                DATE_FORMAT(c.date_debut, '%Y-%m') AS mois,
                c.salaire AS salaire_base,
                fp.revenue_imposable,
                fp.total_irsa
            FROM fiche_paie fp
            JOIN contrat c ON fp.id_employe = c.id_employe
            WHERE fp.id_employe = ?
            ORDER BY fp.date_fiche DESC";

    $stmt = Flight::db()->prepare($sql);
    $stmt->execute([$id_employe]);
    return $stmt->fetchAll();
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
