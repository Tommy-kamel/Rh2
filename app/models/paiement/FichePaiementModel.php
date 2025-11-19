<?php

namespace app\models\paiement;

use Flight;
use DateTime;

class FichePaiementModel
{

    public function genererFichePaie($id_employe, $mois_annee)
    {
        $mois_annee = date('Y-m', strtotime($mois_annee)); 

        // 1. Récupérer employé + contrat
        $sql = "SELECT e.*, c.salaire, c.date_debut, p.nom AS fonction, d.nom_departement
                FROM employe e
                JOIN contrat c ON e.id_employe = c.id_employe
                JOIN poste p ON c.id_poste = p.id_poste
                JOIN departement d ON c.id_departement = d.id_departement
                WHERE e.id_employe = ? 
                  AND (c.date_fin IS NULL OR c.date_fin >= ?)
                LIMIT 1";

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe, "$mois_annee-01"]);
        $emp = $stmt->fetch();

        if (!$emp) return null;

        list($annee, $mois) = explode('-', $mois_annee);

        // 2. Calculs de base
        $salaire_base = $emp['salaire'];
        $taux_journalier = $salaire_base / 30;
        $taux_horaire = round($salaire_base / 173.33);
        $indice = round($taux_horaire / 1.334);

        // Ancienneté
        $embauche = new DateTime($emp['date_debut']);
        $aujourdhui = new DateTime("$mois_annee-01");
        $interval = $embauche->diff($aujourdhui);
        $anciennete = $interval->y . " an(s) " . $interval->m . " mois et " . $interval->d . " jour(s)";

        $annees_anciennete = $interval->y;
        $primes_anciennete = $this->getPrimeAnciennete($salaire_base, $annees_anciennete);

        // Taux heures sup
        $taux_hs_30 = round($taux_horaire * 1.3);
        $taux_hs_40 = round($taux_horaire * 1.4);
        $taux_hs_50 = round($taux_horaire * 1.5);
        $taux_hs_100 = round($taux_horaire * 2);
        $taux_nuit = round($taux_horaire * 0.3);

        // Récupérer les données du mois
        $absences_data = $this->getAbsence($mois, $annee, $id_employe);
        $absences_deductibles = $this->getAbsenceDeductible($absences_data);

        $retards_data = $this->getRetard($mois, $annee, $id_employe);
        $retards_deductibles = $this->getRetardDeductible($retards_data, $taux_journalier);

        $conges_data = $this->getConge($mois, $annee, $id_employe);
        $droit_conge = $this->getDroitConge($conges_data, $taux_journalier);

        $heures_sup_data = $this->getHeureSup($mois, $annee, $id_employe);
        $hs_30 = $this->getHeureSupMaj($heures_sup_data, 30, $taux_hs_30);
        $hs_40 = $this->getHeureSupMaj($heures_sup_data, 40, $taux_hs_40);
        $hs_50 = $this->getHeureSupMaj($heures_sup_data, 50, $taux_hs_50);
        $hs_100 = $this->getHeureSupMaj($heures_sup_data, 100, $taux_hs_100);
        $nuit = $this->getHeureNuit($heures_sup_data, $taux_nuit);

        $primes_data = $this->getPrime($mois, $annee, $id_employe);
        $primes_diverses = $this->getPrimeDivers($primes_data);
        $primes_rendement = $this->getPrimeRendement($primes_data);

        // Calculs finaux
        // $absences = ($absences_deductibles * $taux_journalier);
        $absences = ($absences_deductibles * $taux_horaire) + $retards_deductibles;
        $hs = $hs_30 + $hs_40 + $hs_50 + $hs_100 + $nuit;
        $primes = $primes_diverses + $primes_rendement + $primes_anciennete;

        $droit_preavis = $taux_journalier + $primes_rendement;
        $indemnite_licenciement = $taux_journalier;

        // 4. Salaire brut
        $salaire_brut = $salaire_base - $absences + $hs + $primes;

        // 5. CNaPS + Sanitaire (1% chacun)
        $min_cnaps = 350000 * 8 * 0.01;
        $cnaps = ($salaire_brut * 0.01) < $min_cnaps ? ($salaire_brut * 0.01) : $min_cnaps;
        $sanitaire = $salaire_brut * 0.01;

        // 6. Base imposable
        $base_imposable = $salaire_brut - $cnaps - $sanitaire;

        // 7. IRSA
        $tranche_irsa = $this->getTranchesIRSA($base_imposable);
        $irsa = $this->calculerIRSA($tranche_irsa);

        // 8. Total retenues
        $total_retenues = $cnaps + $sanitaire + $irsa;

        // 9. Net à payer
        $net_a_payer = $salaire_brut - $total_retenues;

        // 10. Retour des données formatées
        return [
            'nom' => strtoupper($emp['nom']),
            'prenom' => ucfirst($emp['prenom']),
            'matricule' => 'EMP 00' . $id_employe,
            'classification' => 'HC',
            'fonction' => $emp['fonction'],
            'salaire_base' => $salaire_base,
            'cnaps' => $emp['numero_cnaps'],
            'taux_journalier' => $taux_journalier,
            'taux_horaire' => $taux_horaire,
            'date_embauche' => $emp['date_debut'],
            'anciennete' => $anciennete,
            'indice' => $indice,
            'mois' => "01/$mois/$annee au " . date('t', strtotime("$annee-$mois-01")) . "/$mois/$annee",
            'salaire_brut' => $salaire_brut,
            'absences' => $absences,
            'taux_hs_30' => $taux_hs_30,
            'taux_hs_40' => $taux_hs_40,
            'taux_hs_50' => $taux_hs_50,
            'taux_hs_100' => $taux_hs_100,
            'taux_nuit' => $taux_nuit,
            'hs_30' => $hs_30,
            'hs_40' => $hs_40,
            'hs_50' => $hs_50,
            'hs_100' => $hs_100,
            'nuit' => $nuit,
            'droit_conge' => $droit_conge,
            'droit_preavi' => $droit_preavis,
            'indemnite_liciencement' => $indemnite_licenciement,
            'primes_diverses' => $primes_diverses,
            'primes_rendement' => $primes_rendement,
            'primes_anciennete' => $primes_anciennete,
            'rappels' => 0,
            'conges' => count($conges_data),
            'preavis' => 0,
            'licenciement' => 0,
            'retenue_cnaps' => $cnaps,
            'retenue_sanitaire' => $sanitaire,
            'irsa_tranches' => $tranche_irsa,
            'total_irsa' => $irsa,
            'total_retenues' => $total_retenues,
            'net_a_payer' => $net_a_payer,
            'montant_imposable' => $base_imposable,
            'mode_paiement' => 'Virement/chèque',
            'mois_annee' => $mois_annee,
            'id_employe' => $id_employe,
            // 'absences_deductibles' => $absences_deductibles . ' + ' . number_format($retards_deductibles, 0, ',', ' ') . '',
            'absences_deductibles' => $absences_deductibles,
        ];
    }

    public function sauvegarderFichePaie($data)
    {
        $sql = "INSERT INTO fiche_paie (
                date_fiche,
                id_employe,
                absence_mois,
                heure_sup,
                salaire_brut,
                cnaps,
                retenue_sanitaire,
                revenue_imposable,
                total_irsa,
                total_retenu,
                net_a_payer,
                net_du_mois
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            ) ON DUPLICATE KEY UPDATE
                absence_mois = VALUES(absence_mois),
                heure_sup = VALUES(heure_sup),
                salaire_brut = VALUES(salaire_brut),
                cnaps = VALUES(cnaps),
                retenue_sanitaire = VALUES(retenue_sanitaire),
                revenue_imposable = VALUES(revenue_imposable),
                total_irsa = VALUES(total_irsa),
                total_retenu = VALUES(total_retenu),
                net_a_payer = VALUES(net_a_payer),
                net_du_mois = VALUES(net_du_mois)";

        // $id_fiche = $data['id_employe'] . '_' . str_replace('-', '', $data['mois_annee']); // ex: 1_202511
        $date_fiche = $data['mois_annee'] . '-01';

        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            $date_fiche,
            $data['id_employe'],
            $data['absences_deductibles'], // nombre de jours d'absence déductible
            ($data['hs_30'] + $data['hs_40'] + $data['hs_50'] + $data['hs_100'] + $data['nuit']) / $data['taux_horaire'], // heures totales
            $data['salaire_brut'],
            $data['retenue_cnaps'],
            $data['retenue_sanitaire'],
            $data['montant_imposable'],
            $data['total_irsa'],
            $data['total_retenues'],
            $data['net_a_payer'],
            $data['net_a_payer'] // net_du_mois = net_a_payer
        ]);
    }

    private function getAbsence($mois, $annee, $id_employe)
    {
        $sql = "SELECT * FROM absence 
                WHERE id_employe = ? 
                AND MONTH(date_absence) = ? 
                AND YEAR(date_absence) = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe, $mois, $annee]);
        return $stmt->fetchAll();
    }

    private function getAbsenceDeductible($absences_data)
    {
        $total = 0;
        foreach ($absences_data as $absence) {
            if ($absence['estdeductible']) {
                $total++;
            }
        }
        return $total;
    }

    private function getRetard($mois, $annee, $id_employe)
    {
        $sql = "SELECT * FROM retard 
                WHERE id_employe = ? 
                AND MONTH(date_retard) = ? 
                AND YEAR(date_retard) = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe, $mois, $annee]);
        return $stmt->fetchAll();
    }

    private function getRetardDeductible($retards_data, $taux_journalier)
    {
        $total_minutes = 0;
        foreach ($retards_data as $retard) {
            $total_minutes += $retard['duree_retard'];
        }
        $jours = $total_minutes / (8 * 60);
        return $jours * $taux_journalier;
    }

    private function getConge($mois, $annee, $id_employe)
    {
        $sql = "SELECT c.*, tc.type, tc.pourcentage_salaire 
                FROM conge c
                JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge
                WHERE c.id_employe = ? 
                AND c.status IN (11, 21)
                AND ((MONTH(c.date_debut) = ? AND YEAR(c.date_debut) = ?)
                     OR (MONTH(c.date_fin) = ? AND YEAR(c.date_fin) = ?)
                     OR (c.date_debut <= ? AND c.date_fin >= ?))";

        $debut_mois = "$annee-$mois-01";
        $fin_mois = date('Y-m-t', strtotime($debut_mois));

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe, $mois, $annee, $mois, $annee, $fin_mois, $debut_mois]);
        return $stmt->fetchAll();
    }

    private function getDroitConge($conges_data, $taux_journalier)
    {
        $total = 0;
        foreach ($conges_data as $conge) {
            $debut = new DateTime($conge['date_debut']);
            $fin = new DateTime($conge['date_fin']);
            $nb_jours = $debut->diff($fin)->days + 1;
            $pourcentage = $conge['pourcentage_salaire'] / 100;
            $total += $nb_jours * $taux_journalier * $pourcentage;
        }
        return $total;
    }

    private function getHeureSup($mois, $annee, $id_employe)
    {
        $sql = "SELECT hse.*, hs.type, hs.pourcentage_majoration 
                FROM heure_sup_employe hse
                JOIN heure_sup hs ON hse.id_heure_sup = hs.id_heure_sup
                WHERE hse.id_employe = ? 
                AND MONTH(hse.date_heure_sup) = ? 
                AND YEAR(hse.date_heure_sup) = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe, $mois, $annee]);
        return $stmt->fetchAll();
    }

    private function getHeureSupMaj($heures_sup_data, $pourcentage, $taux)
    {
        $total_minutes = 0;

        foreach ($heures_sup_data as $hs) {
            if ($hs['type'] == 'nuit') continue;

            if ($pourcentage == 30 && $hs['type'] == 'imprevu') {
                $minutes = min($hs['nombre_minutes'], 8 * 60);
                $total_minutes += $minutes;
            } elseif ($pourcentage == 50 && $hs['type'] == 'imprevu') {
                if ($hs['nombre_minutes'] > 8 * 60) {
                    $minutes = min($hs['nombre_minutes'] - 8 * 60, 12 * 60);
                    $total_minutes += $minutes;
                }
            } elseif ($hs['pourcentage_majoration'] == $pourcentage) {
                $total_minutes += $hs['nombre_minutes'];
            }
        }

        $heures = $total_minutes / 60;
        return $heures * $taux;
    }

    private function getHeureNuit($heures_sup_data, $taux_nuit)
    {
        $total_minutes = 0;
        foreach ($heures_sup_data as $hs) {
            if ($hs['type'] == 'nuit') {
                $total_minutes += $hs['nombre_minutes'];
            }
        }
        $heures = $total_minutes / 60;
        return $heures * $taux_nuit;
    }

    private function getPrime($mois, $annee, $id_employe)
    {
        $sql = "SELECT * FROM prime_divers 
                WHERE id_employe = ? 
                AND MONTH(date_prime) = ? 
                AND YEAR(date_prime) = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe, $mois, $annee]);
        return $stmt->fetchAll();
    }

    private function getPrimeDivers($primes_data)
    {
        $total = 0;
        foreach ($primes_data as $prime) {
            if ($prime['type'] == 'diver') {
                $total += $prime['montant_prime'];
            }
        }
        return $total;
    }

    private function getPrimeRendement($primes_data)
    {
        $total = 0;
        foreach ($primes_data as $prime) {
            if ($prime['type'] == 'rendement') {
                $total += $prime['montant_prime'];
            }
        }
        return $total;
    }

    private function getPrimeAnciennete($salaire_base, $annees)
    {
        if ($annees < 3) return 0;

        $pourcentage = 2 + ($annees - 3);
        $pourcentage = min($pourcentage, 25);

        return $salaire_base * ($pourcentage / 100);
    }

    private function calculerIRSA($tranches)
    {
        $irsa = 0;
        foreach ($tranches as $t) {
            $irsa += $t[3];
        }
        return $irsa;
    }

    private function getTranchesIRSA($revenu)
    {
        $tranches = [
            ['INF 350 000', 0, 0, 0],
            ['DE 350 001 à 400 000', 5, 50000, 0],
            ['DE 400 001 à 500 000', 10, 100000, 0],
            ['DE 500 001 à 600 000', 15, 100000, 0],
            ['DE 600 001 à 4 000 000', 20, 100000, 0],
            ['PLUS DE 4 000 000', 25, null, 0]
        ];

        foreach ($tranches as $i => &$t) {
            $montant = 0;

            switch ($i) {
                case 0:
                    $montant = 0;
                    break;

                case 1:
                    if ($revenu > 350000 && $revenu <= 400000) {
                        $montant = $t[2] * ($t[1] / 100);
                    } elseif ($revenu > 400000) {
                        $montant = $t[2] * ($t[1] / 100);
                    }
                    break;

                case 2:
                    if ($revenu > 400000 && $revenu <= 500000) {
                        $montant = $t[2] * ($t[1] / 100);
                    } elseif ($revenu > 500000) {
                        $montant = $t[2] * ($t[1] / 100);
                    }
                    break;

                case 3:
                    if ($revenu > 500000 && $revenu <= 600000) {
                        $montant = $t[2] * ($t[1] / 100);
                    } elseif ($revenu > 600000) {
                        $montant = $t[2] * ($t[1] / 100);
                    }
                    break;

                case 4:
                    if ($revenu > 600000 && $revenu <= 4000000) {
                        $montant = $t[2] * ($t[1] / 100);
                    } elseif ($revenu > 4000000) {
                        $montant = $t[2] * ($t[1] / 100);
                    }
                    break;

                case 5:
                    if ($revenu > 4000000) {
                        $montant = ($revenu - 4000000) * ($t[1] / 100);
                    }
                    break;
            }

            $t[3] = $montant;
        }
        unset($t);

        return $tranches;
    }

    public function getHistoriqueFiches($employe_id = '', $mois = '', $search = '')
    {
        $sql = "SELECT 
                fp.*,
                e.nom, e.prenom,
                DATE_FORMAT(fp.date_fiche, '%m/%Y') AS mois_annee
            FROM fiche_paie fp
            JOIN employe e ON fp.id_employe = e.id_employe
            WHERE 1=1";

        $params = [];

        if ($employe_id) {
            $sql .= " AND fp.id_employe = ?";
            $params[] = $employe_id;
        }

        if ($mois) {
            $sql .= " AND DATE_FORMAT(fp.date_fiche, '%Y-%m') = ?";
            $params[] = $mois;
        }

        if ($search) {
            $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR fp.id_fiche_paie LIKE ?)";
            $like = "%$search%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY fp.date_fiche DESC";

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getFicheById($id_fiche_paie)
    {
        $sql = "SELECT * FROM fiche_paie WHERE id_fiche_paie = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_fiche_paie]);
        return $stmt->fetch();
    }

    public function getAllEmployesForSelect()
    {
        $sql = "SELECT id_employe, CONCAT(nom, ' ', prenom) AS nom_complet FROM employe ORDER BY nom";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Recrée la fiche complète à partir des données sauvegardées
    public function genererFichePaieDepuisSauvegarde($fiche)
    {
        $id_employe = $fiche['id_employe'];
        $mois_annee = date('Y-m', strtotime($fiche['date_fiche']));

        // Régénère la fiche complète
        $data = $this->genererFichePaie($id_employe, $mois_annee);

        if (!$data) return null;

        // Remplace les valeurs calculées par celles sauvegardées
        $data['salaire_brut'] = $fiche['salaire_brut'];
        $data['absences'] = $fiche['absence_mois'] * $data['taux_journalier'];
        $data['hs_total'] = $fiche['heure_sup'] * $data['taux_horaire'];
        $data['retenue_cnaps'] = $fiche['cnaps'];
        $data['retenue_sanitaire'] = $fiche['retenue_sanitaire'];
        $data['montant_imposable'] = $fiche['revenue_imposable'];
        $data['total_irsa'] = $fiche['total_irsa'];
        $data['total_retenues'] = $fiche['total_retenu'];
        $data['net_a_payer'] = $fiche['net_a_payer'];

        return $data;
    }
}
