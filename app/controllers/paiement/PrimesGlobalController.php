<?php

namespace app\controllers\paiement;

use app\models\paiement\PrimeGlobalModel;
use Flight;
require 'C:\Users\Rojo\Documents\GitHub\Framework-Sprint\Rh2\public\assets\lib\dompdf\autoload.inc.php';
use Dompdf\Dompdf;

class PrimesGlobalController
{
    public static function index()
    {
        $model = new PrimeGlobalModel();

        // Paramètres
        $employe_id = Flight::request()->query['employe'] ?? null;
        $search = Flight::request()->query['search'] ?? '';
        $order = Flight::request()->query['order'] ?? 'DESC';
        $success = Flight::request()->query['success'] ?? null;

        // Vérifier si c'est une demande d'export PDF
        $export_pdf = Flight::request()->query['export'] ?? false;
        
        $primes = $model->getAllPrimesGlobal($employe_id, $search, $order);
        $employes = $model->getAllEmployesForSelect();
        $employes_actifs = $model->getAllEmployesActifs();

        // // Si export PDF, générer le PDF
        // if ($export_pdf === 'pdf') {
        //     self::generatePDF($primes, $employe_id, $search, $order);
        //     return;
        // }

        Flight::render('paiement/primes_global', [
            'primes' => $primes,
            'employes' => $employes,
            'employes_actifs' => $employes_actifs,
            'selected_employe' => $employe_id,
            'search' => $search,
            'order' => $order,
            'success' => $success
        ]);
    }

    public static function ajouterPrime()
    {
        if (Flight::request()->method !== 'POST') {
            Flight::redirect('/primes-global?error=method_not_allowed');
            return;
        }

        $model = new PrimeGlobalModel();
        $data = Flight::request()->data->getData();

        // Validation des données
        $errors = self::validerDonneesPrime($data);
        
        if (!empty($errors)) {
            Flight::redirect('/primes-global?error=' . urlencode(implode(', ', $errors)));
            return;
        }

        // Vérifier si la prime existe déjà
        if ($model->primeExiste($data['id_employe'], $data['type_prime'], $data['motif'], $data['date_prime'], $data['montant_prime'])) {
            Flight::redirect('/primes-global?error=prime_existe_deja');
            return;
        }

        // Ajouter la prime
        try {
            $success = $model->ajouterPrime($data);
            
            if ($success) {
                Flight::redirect('/primes-global?success=prime_ajoutee');
            } else {
                Flight::redirect('/primes-global?error=erreur_ajout');
            }
        } catch (\Exception $e) {
            Flight::redirect('/primes-global?error=erreur_systeme');
        }
    }

    private static function validerDonneesPrime($data)
    {
        $errors = [];

        // Validation de l'employé
        if (empty($data['id_employe'])) {
            $errors[] = "L'employé est requis";
        }

        // Validation du type
        if (empty($data['type_prime']) || !in_array($data['type_prime'], ['rendement', 'divers'])) {
            $errors[] = "Le type de prime est invalide";
        }

        // Validation du motif
        if (empty($data['motif'])) {
            $errors[] = "Le motif est requis";
        } elseif (strlen($data['motif']) > 255) {
            $errors[] = "Le motif ne doit pas dépasser 255 caractères";
        }

        // Validation du montant
        if (empty($data['montant_prime']) || !is_numeric($data['montant_prime']) || $data['montant_prime'] <= 0) {
            $errors[] = "Le montant doit être un nombre positif";
        } elseif ($data['montant_prime'] > 10000000) {
            $errors[] = "Le montant est trop élevé";
        }

        // Validation de la date
        if (empty($data['date_prime'])) {
            $errors[] = "La date est requise";
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $data['date_prime']);
            if (!$date || $date->format('Y-m-d') !== $data['date_prime']) {
                $errors[] = "La date est invalide";
            } elseif ($date > new \DateTime()) {
                $errors[] = "La date ne peut pas être dans le futur";
            }
        }

        return $errors;
    }

    public static function fichePDF()
    {
        $model = new PrimeGlobalModel();

        // Paramètres
        $employe_id = Flight::request()->query['employe'] ?? null;
        $search = Flight::request()->query['search'] ?? '';
        $order = Flight::request()->query['order'] ?? 'DESC';

        $primes = $model->getAllPrimesGlobal($employe_id, $search, $order);
        $employes = $model->getAllEmployesForSelect();

        // Capturer le rendu HTML de la vue existante
        ob_start();
        include __DIR__ . '/../../views/paiement/primes_global.php';
        $html = ob_get_clean();

        // Supprime tout ce qui a la classe "no-pdf"
        // $html = preg_replace('#<[^>]*class="[^"]*no-pdf[^"]*"[^>]*>.*?</[^>]+>#is', '', $html);

        // Instancier Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // Papier A4 portrait
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        // Afficher dans le navigateur
        $dompdf->stream("Prime.pdf", ["Attachment" => false]);
    }
}