<?php

namespace app\controllers\admin;

use app\models\admin\MatchingModel;
use Flight;

class MatchingController
{

    private $model;

    public function __construct()
    {
        $this->model = new MatchingModel();
    }

    // Liste postes actuels + historique
    public function listePostes()
    {
        $postesActuels = $this->model->getPostesLibresActuels();
        $historique = $this->model->getHistoriquePostes();

        Flight::render('admin/matching_postes_complete', [
            'postesActuels' => $postesActuels,
            'historique' => $historique
        ]);
    }

    public function ajouterPoste()
    {
        $data = Flight::request()->data;

        // Ajouter le poste libre
        $this->model->insertPosteLibre([
            'id_poste' => $data['id_poste'],
            'id_departement' => $data['id_departement'],
            'date_publication' => $data['date_publication'],
            'date_expiration' => $data['date_expiration'],
            'description' => $data['description']
        ]);

        $posteLibreId = Flight::db()->lastInsertId(); // id du poste libre ajouté

        // Ajouter les compétences
        if (!empty($data['competence_id'])) {
            foreach ($data['competence_id'] as $index => $compId) {
                $niveau = $data['niveau_requis'][$index] ?? 1;
                $this->model->insertPosteCompetence($data['id_poste'], $compId, $niveau);
            }
        }

        Flight::redirect('/matching/postes');
    }

    // Modifier un poste libre
    public function modifierPoste($id)
    {
        $data = Flight::request()->data;

        $this->model->updatePosteLibre($id, [
            'id_poste' => $data['id_poste'],
            'id_departement' => $data['id_departement'],
            'date_publication' => $data['date_publication'],
            'date_expiration' => $data['date_expiration'],
            'description' => $data['description']
        ]);

        Flight::redirect('/matching/postes');
    }

    public function supprimerPoste($id)
    {
        // Fixe la date d’expiration à hier au lieu de delete
        $hier = date('Y-m-d', strtotime('-1 day'));

        $this->model->softDeletePosteLibre($id, $hier);

        Flight::redirect('/matching/postes');
    }


    public function poste($id)
    {
        $poste = $this->model->getPosteById($id);
        if (!$poste) {
            Flight::halt(404, 'Poste introuvable');
            return;
        }

        $required = $this->model->getRequiredCompetences($id);

        $employees = $this->model->getEmployeesWithCompetences();

        $results = $this->computeMatching($poste, $required, $employees);

        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        Flight::render('admin/matching_poste', [
            'poste' => $poste,
            'required' => $required,
            'results' => $results
        ]);
    }

    private function computeMatching($poste, $required, $employees)
    {
        $results = [];

        $nbReq = count($required);
        $weights = [];
        foreach ($required as $r) {
            $weights[$r['id_competence']] = 1;
        }

        foreach ($employees as $emp) {
            // emp['competences'] => [id_competence => niveau, ...]
            $empComps = $emp['competences'];

            $scoreTotal = 0;
            $maxTotal = 0;
            $gaps = [];
            $detailComparaison = [];

            foreach ($required as $req) {
                $cid = $req['id_competence'];
                $niveauReq = (int)$req['niveau_requis'];
                $poids = $weights[$cid];

                $maxTotal += $niveauReq * $poids;

                $niveauEmp = isset($empComps[$cid]) ? (int)$empComps[$cid] : 0;

                $ratio = $niveauReq > 0 ? min($niveauEmp / $niveauReq, 1.0) : 0;
                $contrib = $ratio * ($niveauReq * $poids);
                $scoreTotal += $contrib;

                $diff = $niveauEmp - $niveauReq;
                if ($diff < 0) {
                    $gaps[] = [
                        'id_competence' => $cid,
                        'nom_competence' => $req['nom_competence'],
                        'niveau_requis' => $niveauReq,
                        'niveau_employe' => $niveauEmp,
                        'manque' => abs($diff)
                    ];
                }

                $detailComparaison[] = [
                    'id_competence' => $cid,
                    'nom_competence' => $req['nom_competence'],
                    'niveau_requis' => $niveauReq,
                    'niveau_employe' => $niveauEmp,
                    'ratio' => round($ratio, 2)
                ];
            }

            $scorePct = $maxTotal > 0 ? round(($scoreTotal / $maxTotal) * 100, 2) : 0.0;

            $suggestions = [];
            foreach ($gaps as $g) {
                $suggestions[] = "Formation recommandée: '{$g['nom_competence']}' (manque {$g['manque']} niveau)";
            }

            $results[] = [
                'employe' => [
                    'id_employe' => $emp['id_employe'],
                    'nom' => $emp['nom'],
                    'prenom' => $emp['prenom'],
                    'email' => $emp['email'],
                    'telephone' => $emp['telephone'],
                    'photo' => $emp['photo'],
                    'nom_poste' => $emp['nom_poste'],
                    'nom_departement' => $emp['nom_departement']
                ],
                'score' => $scorePct,
                'gaps' => $gaps,
                'suggestions' => $suggestions,
                'detail' => $detailComparaison,
                'poste' => $poste
            ];
        }

        return $results;
    }
}
