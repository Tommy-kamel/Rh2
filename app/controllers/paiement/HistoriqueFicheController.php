<?php

namespace app\controllers\paiement;

use app\models\paiement\FichePaiementModel;
use Flight;

class HistoriqueFicheController
{
    public static function index()
    {
        $model = new FichePaiementModel();

        $search = Flight::request()->query['search'] ?? '';
        $employe_id = Flight::request()->query['employe'] ?? '';
        $mois = Flight::request()->query['mois'] ?? '';

        $fiches = $model->getHistoriqueFiches($employe_id, $mois, $search);
        $employes = $model->getAllEmployesForSelect();

        Flight::render('paiement/historique_fiches', [
            'fiches' => $fiches,
            'employes' => $employes,
            'selected_employe' => $employe_id,
            'selected_mois' => $mois,
            'search' => $search
        ]);
    }

    public static function voir($id_fiche_paie)
    {
        $model = new FichePaiementModel();
        $fiche = $model->getFicheById($id_fiche_paie);

        if (!$fiche) {
            Flight::notFound();
            return;
        }

        // Recalculer la fiche complète à partir des données sauvegardées
        $data = $model->genererFichePaieDepuisSauvegarde($fiche);

        Flight::render('paiement/fiche', ['data' => $data]);
    }
}