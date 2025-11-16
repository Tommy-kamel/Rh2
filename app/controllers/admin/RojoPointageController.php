<?php

namespace app\controllers\admin;

use app\models\admin\RojoPointageModel;
use app\models\admin\RojoAutreModel;
use Flight;

class RojoPointageController
{
    private $modelPointage;
    private $modelAutre;

    public function __construct()
    {
        $this->modelPointage = new RojoPointageModel();
        $this->modelAutre = new RojoAutreModel();
    }

    // === POINTAGE ARRIVEE ===
    public function pagePointageArrivee()
    {
        $employes = $this->modelPointage->getEmployesAvecStatutPointage();
        Flight::render('admin/pointage_arrivee', ['employes' => $employes]);
    }

    public function traiterPointageIndividuel()
    {
        $data = Flight::request()->data;
        $id_employe = $data->id_employe;
        $date_heure_arrive = $data->date_heure_arrive;

        if (!$id_employe || !$date_heure_arrive) {
            Flight::json(['status' => 'error', 'message' => 'Données manquantes.']);
            return;
        }

        $resultat = $this->modelPointage->pointerArriveeAvecRetard($id_employe, $date_heure_arrive);
        Flight::json($resultat);
    }

    public function marquerAbsentManuellement()
    {
        $data = Flight::request()->data;
        $id_employe = $data->id_employe;

        if (!$id_employe) {
            Flight::json(['status' => 'error', 'message' => 'ID employé manquant.']);
            return;
        }

        $resultat = $this->modelAutre->marquerAbsentManuellement($id_employe);
        Flight::json($resultat);
    }

    // === POINTAGE DEPART ===
    public function pagePointageDepart()
    {
        $employes = $this->modelPointage->getEmployesArrivesSansDepart();
        Flight::render('admin/pointage_depart', ['employes' => $employes]);
    }

    public function traiterDepartIndividuel()
    {
        $data = Flight::request()->data;
        $id_employe = $data->id_employe;
        $date_heure_depart = $data->date_heure_depart;

        if (!$id_employe || !$date_heure_depart) {
            Flight::json(['status' => 'error', 'message' => 'Données manquantes.']);
            return;
        }

        $success = $this->modelPointage->enregistrerDepartAvecHeure($id_employe, $date_heure_depart);
        
        Flight::json([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Départ enregistré avec succès.' : 'Erreur lors de l\'enregistrement du départ.'
        ]);
    }

    // === ABSENCES ===
    public function listeAbsents()
    {
        $date = Flight::request()->query->date ?? date('Y-m-d');
        
        $absents = $this->modelAutre->getAbsentsPourDate($date);
        $absences = $this->modelAutre->getAbsencesPourDate($date);
        $nonPointes = $this->modelAutre->getEmployesNonPointes($date);
        
        Flight::render('liste_absents', [
            'absents' => $absents,
            'absences' => $absences,
            'nonPointes' => $nonPointes,
            'date_selectionnee' => $date
        ]);
    }

    public function marquerAbsent()
    {
        $data = Flight::request()->data;
        $id_employe = $data->id_employe;
        $date = $data->date;

        if (!$id_employe || !$date) {
            Flight::json(['status' => 'error', 'message' => 'Données manquantes']);
            return;
        }

        $success = $this->modelAutre->enregistrerAbsence($id_employe, $date);
        
        Flight::json([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Absence enregistrée' : 'Erreur'
        ]);
    }

    // === RELEVE DE PRESENCE ===
    public function relevePresence()
    {
        $date = Flight::request()->query->date ?? date('Y-m-d');
        $releve = $this->modelAutre->getRelevePresence($date);
        
        Flight::render('admin/releve_presence', [
            'releve' => $releve,
            'date_selectionnee' => $date
        ]);
    }

    
}