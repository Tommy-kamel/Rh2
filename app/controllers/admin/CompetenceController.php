<?php

namespace app\controllers\admin;

use app\models\admin\CompetenceModel;
use Flight;

class CompetenceController
{

    private $model;

    public function __construct()
    {
        $this->model = new CompetenceModel();
    }

    public function cartographie()
    {
        $competencesStats = $this->model->getCompetenceStats();

        Flight::render('admin/competences_cartographie', [
            'competences' => $competencesStats
        ]);
    }

    public function exportCartographiePdf()
    {
        // 1️⃣ Récupérer les données depuis le modèle
        $competencesStats = $this->model->getCompetenceStats();

        // 2️⃣ Charger le contenu HTML de la vue
        // On utilise output() plutôt que render() pour récupérer le HTML sous forme de chaîne
        ob_start();
        Flight::render('admin/competences_cartographie', [
            'competences' => $competencesStats
        ]);
        $html = ob_get_clean();

        // 3️⃣ Initialiser Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);

        // 4️⃣ Configurer le format et l'orientation (A4 portrait ici)
        $dompdf->setPaper('A4', 'portrait');

        // 5️⃣ Générer le PDF
        $dompdf->render();

        // 6️⃣ Envoyer le PDF au navigateur pour téléchargement
        $dompdf->stream("competences_cartographie.pdf", [
            "Attachment" => true // true = téléchargement, false = affichage dans le navigateur
        ]);
    }

    public function exportPDF()
    {
        $competences = $this->model->getCompetenceStats();

        // Construire le HTML minimal (table + graphiques)
        ob_start(); ?>

        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <title>Cartographie des Compétences</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                }

                .table {
                    border-collapse: collapse;
                    width: 100%;
                    margin-bottom: 20px;
                }

                .table th,
                .table td {
                    border: 1px solid #ccc;
                    padding: 8px;
                    text-align: left;
                }

                .table th {
                    background-color: #f0f0f0;
                }

                .chart-wrapper {
                    margin-bottom: 20px;
                    text-align: center;
                }
            </style>
        </head>

        <body>
            <h2>Cartographie des Compétences</h2>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Compétence</th>
                            <th>Nombre d'employés</th>
                            <th>Niveau moyen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($competences as $c): ?>
                            <tr>
                                <td><?= $c['nom_competence'] ?></td>
                                <td><?= $c['nb_employes'] ?></td>
                                <td><?= number_format($c['niveau_moyen'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- <h3>Graphiques</h3> -->
            <!-- <p>⚠️ Les graphiques JavaScript ne peuvent pas être rendus par Dompdf. Il faudra convertir vos graphiques en images (PNG/Base64) pour les inclure.</p> -->
        </body>

        </html>

<?php
        $html = ob_get_clean();

        // Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("cartographie_competences.pdf", [
            "Attachment" => true
        ]);
    }


    public function details($id)
    {
        $competence = $this->model->getCompetenceById($id);
        $employees = $this->model->getEmployeesByCompetence($id);

        Flight::render('admin/competence_details', [
            'competence' => $competence,
            'employees'  => $employees
        ]);
    }

    public function dashboard()
    {
        $employees = $this->model->getEmployeesCompetencesSummary();
        $totalCompetences = $this->model->getTotalCompetences();
        $totalGaps = $this->model->getTotalGaps();

        Flight::render('admin/formations_dashboard', [
            'employees' => $employees,
            'totalCompetences' => $totalCompetences,
            'totalGaps' => $totalGaps
        ]);
    }

    public function employeeDetails($id)
    {
        $employee = $this->model->getEmployeeById($id);
        $id_poste = Flight::request()->query['id_poste'] ?? null;

        if ($id_poste) {
            $comparison = $this->model->getCompetencesComparisonForPoste($id, $id_poste);
        } else {
            $comparison = []; // ou afficher toutes les compétences si pas de poste
        }

        // Pour le pie chart : toutes les compétences de l'employé
        $allCompetences = $this->model->getAllCompetencesOfEmployee($id);

        Flight::render('admin/employee_competences_comparison', [
            'employee' => $employee,
            'comparison' => $comparison,
            'allCompetences' => $allCompetences,
            'id_poste' => $id_poste
        ]);
    }
}
