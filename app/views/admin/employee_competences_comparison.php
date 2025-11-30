<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des congés</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Outfit', sans-serif;
        }

        /* Layout principal */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: end;
        }

        .user-name {
            font-weight: 600;
        }

        .user-role {
            font-size: 0.85rem;
        }

        .content {
            padding: 0;
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 24px;
        }

        /* Tableau avec l'apparence du premier code */
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 12px 8px;
        }

        .table td {
            padding: 10px 8px;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        /* Rétrécir les colonnes pour éviter que la table soit trop large */
        table th:nth-child(1),
        table td:nth-child(1) {
            width: 40%;
        }

        /* Compétence */
        table th:nth-child(2),
        table td:nth-child(2) {
            width: 20%;
        }

        /* Niveau requis */
        table th:nth-child(3),
        table td:nth-child(3) {
            width: 20%;
        }

        /* Niveau employé */
        table th:nth-child(4),
        table td:nth-child(4) {
            width: 20%;
        }

        /* Texte centré pour les niveaux */
        table td:nth-child(2),
        table td:nth-child(3),
        table td:nth-child(4) {
            text-align: center;
        }

        /* Couleur pour le besoin de formation */
        table td:nth-child(4) {
            color: #e74c3c;
            font-weight: bold;
        }

        /* === DIAGRAMMES CÔTE À CÔTE === */
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 40px;
        }

        .chart-wrapper {
            flex: 1;
            min-width: 300px;
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .chart-wrapper h3 {
            text-align: center;
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .charts-container {
                flex-direction: column;
                align-items: center;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }

        /* Amélioration globale */
        canvas {
            max-width: 100%;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="user"></i>
                    Compétences de l'employé
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge badge-primary">Ressources Humaines</span>
                </div>
            </header>

            <div class="content">
                <h2>Compétences de <?= $employee['prenom'] . ' ' . $employee['nom'] ?></h2>
                <?php if ($id_poste): ?>
                    <p>Comparaison pour le poste : <?= $id_poste ?></p>
                <?php endif; ?>

                <!-- Tableau avec l'apparence du premier code -->
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Compétence requise</th>
                                <th>Niveau requis</th>
                                <th>Niveau de l'employé</th>
                                <th>Besoin de formation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comparison as $c): ?>
                                <tr>
                                    <td><?= $c['nom_competence'] ?></td>
                                    <td><?= $c['niveau_requis'] ?></td>
                                    <td><?= $c['niveau_employe'] ?? 'N/A' ?></td>
                                    <td><?= $c['besoin_formation'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Diagramme côte à côte -->
                <div class="charts-container">
                    <div class="chart-wrapper">
                        <h3>Comparaison avec le poste</h3>
                        <canvas id="barComparison"></canvas>
                    </div>
                    
                    <div class="chart-wrapper">
                        <h3>Toutes les compétences de l'employé</h3>
                        <canvas id="pieAllCompetences"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Diagramme barre : comparaison
        const labelsBar = <?= json_encode(array_column($comparison, 'nom_competence')) ?>;
        const niveauRequis = <?= json_encode(array_column($comparison, 'niveau_requis')) ?>;
        const niveauEmploye = <?= json_encode(array_map(function ($c) {
                                    return $c['niveau_employe'] ?? 0;
                                }, $comparison)) ?>;

        new Chart(document.getElementById('barComparison'), {
            type: 'bar',
            data: {
                labels: labelsBar,
                datasets: [{
                        label: 'Niveau requis',
                        data: niveauRequis,
                        backgroundColor: '#36A2EB'
                    },
                    {
                        label: 'Niveau employé',
                        data: niveauEmploye,
                        backgroundColor: '#FF6384'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Diagramme cercle : toutes les compétences de l'employé
        const labelsPie = <?= json_encode(array_column($allCompetences, 'nom_competence')) ?>;
        const dataPie = <?= json_encode(array_column($allCompetences, 'niveau')) ?>;

        new Chart(document.getElementById('pieAllCompetences'), {
            type: 'pie',
            data: {
                labels: labelsPie,
                datasets: [{
                    data: dataPie,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#FF6384'
                    ]
                }]
            }
        });

        feather.replace();
        document.querySelectorAll('.has-submenu > .menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const parent = link.parentElement;
                const submenu = parent.querySelector('.submenu');
                // Fermer tous les autres sous-menus
                document.querySelectorAll('.submenu').forEach(sub => {
                    if (sub !== submenu) {
                        sub.classList.remove('show');
                        sub.parentElement.classList.remove('open');
                    }
                });
                // Toggle le sous-menu actuel
                submenu.classList.toggle('show');
                parent.classList.toggle('open');
            });
        });

        const input = document.getElementById('searchInput');
        const rows = Array.from(document.querySelectorAll('#employeesTable tbody tr'));
        input.addEventListener('input', e => {
            const q = e.target.value.toLowerCase().trim();
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        });
    </script>
</body>
</html>