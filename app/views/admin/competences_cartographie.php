<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartographie des Compétences</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .filters-container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
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

        .content {
            padding: 20px;
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

        @media (max-width: 768px) {
            .charts-container {
                flex-direction: column;
                align-items: center;
            }
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
                    Cartographie des Compétences
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge badge-primary">Ressources Humaines</span>
                </div>
            </header>

            <div class="content">
                <h2>Cartographie des Compétences</h2>

                <!-- Tableau avec l'apparence du premier code -->
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Compétence</th>
                                <th>Nombre d'employés</th>
                                <th>Niveau moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($competences as $c): ?>
                                <tr>
                                    <td>
                                        <a href="/competences/cartographie/<?= $c['id_competence'] ?>">
                                            <?= $c['nom_competence'] ?>
                                        </a>
                                    </td>
                                    <td><?= $c['nb_employes'] ?></td>
                                    <td><?= number_format($c['niveau_moyen'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                                <a href="/competences/exportPDF">exporter</a>
                <!-- Graphiques côte à côte -->
                <div class="charts-container">
                    <div class="chart-wrapper">
                        <h3>Nombre d'employés par compétence</h3>
                        <canvas id="chartEmployes"></canvas>
                    </div>
                    
                    <div class="chart-wrapper">
                        <h3>Niveau moyen par compétence</h3>
                        <canvas id="chartNiveaux"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const labels = <?= json_encode(array_column($competences, 'nom_competence')) ?>;
        const nbEmployes = <?= json_encode(array_column($competences, 'nb_employes')) ?>;
        const niveauxMoyens = <?= json_encode(array_column($competences, 'niveau_moyen')) ?>;

        // ------ GRAPHIQUE 1 : Nombre d'employés ------
        new Chart(document.getElementById('chartEmployes'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "Nombre d'employés",
                    data: nbEmployes,
                    backgroundColor: '#36A2EB'
                }]
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

        // ------ GRAPHIQUE 2 : Niveau moyen ------
        new Chart(document.getElementById('chartNiveaux'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Niveau moyen',
                    data: niveauxMoyens,
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    fill: true
                }]
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

        feather.replace();

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

    <script src="/assets/js/rh-dashboard.js"></script>
</body>

</html>