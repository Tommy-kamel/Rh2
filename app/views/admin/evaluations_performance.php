<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluations de Performance - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .score-high { color: #28a745; font-weight: bold; }
        .score-medium { color: #ffc107; font-weight: bold; }
        .score-low { color: #dc3545; font-weight: bold; }
        .progress-bar-custom {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .critere-score {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="bar-chart-2"></i>
                    Évaluations de Performance
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Filtres et actions -->
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label for="periode" class="form-label"><strong>Période d'évaluation :</strong></label>
                            <select class="form-select" id="periode" name="periode">
                                <option value="mensuelle" <?= $periode_selectionnee === 'mensuelle' ? 'selected' : '' ?>>Mensuelle</option>
                                <option value="trimestrielle" <?= $periode_selectionnee === 'trimestrielle' ? 'selected' : '' ?>>Trimestrielle</option>
                                <option value="annuelle" <?= $periode_selectionnee === 'annuelle' ? 'selected' : '' ?>>Annuelle</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-primary" onclick="calculerEvaluations()">
                                    <i data-feather="refresh-cw"></i> Calculer les évaluations
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="genererRapportGlobal()">
                                    <i data-feather="file-text"></i> Rapport global
                                </button>
                                <a href="/scoring/rapports" class="btn btn-outline-secondary">
                                    <i data-feather="archive"></i> Historique
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques globales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-primary">
                                <?= count($evaluations) ?>
                            </div>
                            <div class="text-muted">Employés évalués</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-success">
                                <?= $stats['moyenne_globale'] ?>
                            </div>
                            <div class="text-muted">Score moyen</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-warning">
                                <?= $stats['nombre_excellent'] ?>
                            </div>
                            <div class="text-muted">Performances excellentes</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-info">
                                <?= $stats['nombre_amelioration'] ?>
                            </div>
                            <div class="text-muted">À améliorer</div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des évaluations -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Employé</th>
                                <th>Poste</th>
                                <th>Département</th>
                                <th>Score Global</th>
                                <th>Assiduité</th>
                                <th>Objectifs</th>
                                <th>Qualité</th>
                                <th>Comportement</th>
                                <th>Productivité</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evaluations as $eval): 
                                $scores_detail = json_decode($eval['scores_detail'], true);
                            ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($eval['prenom'] . ' ' . $eval['nom']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($eval['poste']) ?></td>
                                    <td><?= htmlspecialchars($eval['nom_departement']) ?></td>
                                    <td>
                                        <span class="<?= $model->getClasseScore($eval['score_global']) ?>">
                                            <?= number_format($eval['score_global'], 2) ?>/100  <!-- Format correct -->
                                        </span>
                                        <div class="progress-bar-custom mt-1">
                                            <div class="progress-fill <?= $model->getClasseProgress($eval['score_global']) ?>" 
                                                style="width: <?= $eval['score_global'] ?>%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="critere-score <?= $model->getClasseScore($scores_detail['assiduite']['score'] ?? 0) ?>">
                                            <?= $scores_detail['assiduite']['score'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="critere-score <?= $model->getClasseScore($scores_detail['objectifs']['score'] ?? 0) ?>">
                                            <?= $scores_detail['objectifs']['score'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="critere-score <?= $model->getClasseScore($scores_detail['qualite']['score'] ?? 0) ?>">
                                            <?= $scores_detail['qualite']['score'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="critere-score <?= $model->getClasseScore($scores_detail['comportement']['score'] ?? 0) ?>">
                                            <?= $scores_detail['comportement']['score'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="critere-score <?= $model->getClasseScore($scores_detail['productivite']['score'] ?? 0) ?>">
                                            <?= $scores_detail['productivite']['score'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="genererRapportIndividuel(<?= $eval['id_employe'] ?>)">
                                            <i data-feather="file-text"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="voirDetails(<?= $eval['id_employe'] ?>)">
                                            <i data-feather="eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        feather.replace();
        
        function calculerEvaluations() {
            const periode = document.getElementById('periode').value;
            
            fetch('/scoring/calculer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ periode: periode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors du calcul des évaluations');
            });
        }

        function genererRapportIndividuel(idEmploye) {
            const periode = document.getElementById('periode').value;
            
            fetch(`/scoring/rapport?id_employe=${idEmploye}&periode=${periode}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    afficherRapport(data.rapport);
                } else {
                    alert('Erreur: ' + data.message);
                }
            });
        }

        function afficherRapport(rapport) {
            // Ouvrir une modal ou nouvelle fenêtre avec le rapport
            const url = `/scoring/rapport-pdf?id_employe=${rapport.employe.id_employe}&periode=${rapport.periode}`;
            window.open(url, '_blank');
        }

        function voirDetails(idEmploye) {
            window.location.href = `/scoring/details/${idEmploye}`;
        }

        // Gestion du changement de période
        document.getElementById('periode').addEventListener('change', function() {
            const periode = this.value;
            window.location.href = `/scoring/evaluations?periode=${periode}`;
        });

        // Gestion du menu déroulant
        document.querySelectorAll('.has-submenu > .menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                const parent = link.parentElement;
                if (!parent.classList.contains('active')) {
                    e.preventDefault();
                    parent.classList.toggle('open');
                }
            });
        });
    </script>
</body>
</html>