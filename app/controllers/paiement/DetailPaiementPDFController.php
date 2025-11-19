<?php

namespace app\controllers\paiement;

require 'C:\xampp\htdocs\S5\Mr Tovo\Rh2\public\assets\lib\dompdf\autoload.inc.php';

use Dompdf\Dompdf;
use Flight;
use app\models\paiement\DetailPaiementModel;
use app\models\paiement\FichePaiementModel;

class DetailPaiementPDFController
{
    public static function showPDF($id_employe, $type, $mois_annee)
    {
        // Validation basique
        if (!in_array($type, ['absences', 'heures-sup', 'heures-nuit', 'primes', 'irsa', 'primes-historique'])) {
            Flight::notFound();
            return;
        }

        list($annee, $mois) = explode('-', $mois_annee);

        $model = new DetailPaiementModel();

        $data = [
            'id_employe' => $id_employe,
            'mois' => $mois,
            'annee' => $annee,
            'type' => $type,
            'title' => ucwords(str_replace('-', ' ', $type)),
            'items' => []
        ];

        // Récupérer les données selon le type
        switch ($type) {
            case 'absences':
                $data['items'] = $model->getAbsencesRetards($id_employe, $mois, $annee);
                break;
            case 'heures-sup':
                $data['items'] = $model->getHeuresSup($id_employe, $mois, $annee);
                break;
            case 'heures-nuit':
                $data['items'] = $model->getHeuresNuit($id_employe, $mois, $annee);
                break;
            case 'primes':
                $data['items'] = $model->getPrimes($id_employe, $mois, $annee);
                break;
            case 'irsa':
                $model2 = new FichePaiementModel();
                $data = $model2->genererFichePaie($id_employe, $annee.$mois);
                break;
            case 'primes-historique':
                $data['items'] = $model->getAllPrimes($id_employe);
                break;
        }

        // Récupérer le nom de l'employé
        $sql = "SELECT nom, prenom FROM employe WHERE id_employe = ?";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe]);
        $emp = $stmt->fetch();

        $data['employe_nom'] = $emp ? strtoupper($emp['nom']) . ' ' . ucfirst($emp['prenom']) : 'Inconnu';

        // Chemin de la vue selon le type
        $view = "paiement/details/{$type}";

        // --- Capturer le rendu HTML de la vue ---
        ob_start();
        Flight::render($view, ['data' => $data, 'title' => $type]);
        $html = ob_get_clean();

        $html = preg_replace('#<[^>]*class="[^"]*no-pdf[^"]*"[^>]*>.*?</[^>]+>#is', '', $html);

        // --- Instancier Dompdf ---
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // Format papier
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        // Envoyer le PDF au navigateur
        $filename = "Fiche_{$type}_{$id_employe}_{$mois_annee}.pdf";
        $dompdf->stream($filename, ["Attachment" => false]);
    }
}
