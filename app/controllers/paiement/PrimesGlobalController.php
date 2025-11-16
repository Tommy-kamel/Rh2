<?php

namespace app\controllers\paiement;

use app\models\paiement\DetailPaiementModel;
use Flight;

class PrimesGlobalController
{
    public static function index()
    {
        $model = new DetailPaiementModel();

        // Paramètres
        $employe_id = Flight::request()->query['employe'] ?? null;
        $search = Flight::request()->query['search'] ?? '';
        $order = Flight::request()->query['order'] ?? 'DESC';

        $primes = $model->getAllPrimesGlobal($employe_id, $search, $order);
        $employes = $model->getAllEmployesForSelect();

        Flight::render('paiement/primes_global', [
            'primes' => $primes,
            'employes' => $employes,
            'selected_employe' => $employe_id,
            'search' => $search,
            'order' => $order
        ]);
    }
}