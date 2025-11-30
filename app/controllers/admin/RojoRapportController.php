<?php

namespace app\controllers\admin;

use app\models\admin\RojoRapportModel;
use Flight;

class RojoRapportController
{
    private $model;

    public function __construct()
    {
        $this->model = new RojoRapportModel();
    }

    public function pageRapports()
    {
        Flight::render('admin/rapports_performance');
    }

    public function voirRapportIndividuel()
    {
        $id_employe = Flight::request()->query->id_employe;
        $periode = Flight::request()->query->periode ?? 'mensuelle';
        
        if (!$id_employe) {
            Flight::redirect('/scoring/rapports?error=id_manquant');
            return;
        }

        $rapport = $this->model->genererRapportPerformance($id_employe, $periode);
        
        Flight::render('admin/rapport_individuel', [
            'rapport' => $rapport,
            'model' => $this->model
        ]);
    }

    public function exporterRapportPDF()
    {
        $id_employe = Flight::request()->query->id_employe;
        $type = Flight::request()->query->type;
        $periode = Flight::request()->query->periode ?? 'mensuelle';
        
        if ($type === 'individuel' && !$id_employe) {
            Flight::json(['status' => 'error', 'message' => 'ID employé manquant pour le rapport individuel.']);
            return;
        }

        // Simuler l'export PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="rapport_performance_individuel.pdf"');
        
        echo "PDF Export - Fonctionnalité à implémenter avec TCPDF ou Dompdf";
        exit;
    }
}