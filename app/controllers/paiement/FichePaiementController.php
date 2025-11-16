<?php

namespace app\controllers\paiement;

use app\models\paiement\FichePaiementModel;
use Flight;

class FichePaiementController
{
    public static function fiche()
    {
        $model = new FichePaiementModel();
        $annee_mois = $_GET['mois'];
        $id_employe = $_GET['id_employe'];
        // $annee_mois = date('Y_m', strtotime($input)); 
        $data = $model->genererFichePaie($id_employe, $annee_mois);

        if (!$data) {
            echo "Fiche non disponible pour cet employé.";
            return;
        }

        Flight::render('paiement/fiche', ['data' => $data]);
    }

    public static function enregistrerFiche()
    {
        $model = new FichePaiementModel();
        $annee_mois = $_GET['mois'] ?? date('Y-m');
        $id_employe = $_GET['id_employe'] ?? null;

        if (!$id_employe || !$annee_mois) {
            Flight::notFound();
            return;
        }

        $data = $model->genererFichePaie($id_employe, $annee_mois);

        if (!$data) {
            echo "Fiche non disponible pour cet employé.";
            return;
        }

        // Sauvegarde si demandé
        // if (isset($_GET['save']) && $_GET['save'] === '1') {
            if ($model->sauvegarderFichePaie($data)) {
                Flight::redirect("/paiement/fiche?id_employe={$id_employe}&mois={$annee_mois}&saved=1");
            } else {
                $data['error'] = "Erreur lors de la sauvegarde.";
            }
        // }
        
        // echo "srgfv";

        // Flight::render('paiement/fiche', ['data' => $data]);
    }
}
