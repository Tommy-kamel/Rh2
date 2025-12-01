<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartographie des Compétences | Dashboard RH</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #6c8bc7;
            --accent-color: #ff7e5f;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --light-bg: #f8fafc;
            --bg: #d8d8d8ff;
            --dark-text: #2d3748;
            --light-text: #718096;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --gradient-primary: linear-gradient(135deg, #2c5aa0 0%, #6c8bc7 100%);
            --gradient-accent: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--dark-text);
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 0;
        }

        .main-header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--dark-text);
        }

        .page-title i {
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-details {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-text);
        }

        .user-role {
            background: var(--gradient-primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .content {
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-weight: 600;
            color: var(--dark-text);
            margin: 0;
            font-size: 1.5rem;
        }

        .export-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: white;
            text-decoration: none;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            /* border-left: 4px solid var(--primary-color); */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-card h3 {
            font-size: 0.95rem;
            color: var(--light-text);
            margin-bottom: 10px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-trend {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .trend-up {
            color: var(--success-color);
        }

        .trend-down {
            color: #f44336;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
        }

        .table-header {
            background: var(--gradient-primary);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 8px 15px 8px 35px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 0.9rem;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        .table {
            margin: 0;
            width: 100%;
        }

        .table thead th {
            background-color: #f8fafc;
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: var(--dark-text);
            padding: 15px 20px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 15px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover td {
            background-color: #f8fafc;
        }

        .skill-link {
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s ease;
        }

        .skill-link:hover {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .skill-link i {
            font-size: 0.9rem;
        }

        .employee-count {
            display: inline-block;
            background: #e8f4ff;
            color: var(--primary-color);
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .skill-level {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .level-bar {
            flex: 1;
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .level-fill {
            height: 100%;
            border-radius: 4px;
            background: var(--gradient-primary);
        }

        .level-text {
            min-width: 40px;
            text-align: right;
            font-weight: 600;
            color: var(--dark-text);
        }

        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
            margin-bottom: 40px;

        }

        .chart-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .chart-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-header h3 {
            margin: 0;
            font-weight: 600;
            color: var(--dark-text);
        }

        .chart-legend {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        .chart-body {
            padding: 20px;
            height: 300px;
            position: relative;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .charts-container {
                grid-template-columns: 1fr;
            }
            
            .main-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-info {
                align-self: flex-start;
            }
            
            .table-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }

        @media (max-width: 576px) {
            .page-title {
                font-size: 1.5rem;
            }
            
            .chart-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }

        /* Animation for page load */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .stat-card, .table-container, .chart-card {
            opacity: 0;
        }

        .chart-wrapper{
            background-color: #ffffffff;
            color: black;
            padding: 7%;
            box-shadow: inset #ecf3ffff 0px 0px 2px 2px;
            text-align: center;
            justify-content: center;
            border-radius: 5%;
            /* height: 500px; */
            width: auto;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .table-container { animation-delay: 0.4s; }
        .chart-card:nth-child(1) { animation-delay: 0.5s; }
        .chart-card:nth-child(2) { animation-delay: 0.6s; }
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
                    <div class="user-avatar">
                        <?= substr($_SESSION['nom_utilisateur'] ?? 'RH', 0, 1) ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?= $_SESSION['nom_utilisateur'] ?? 'Responsable RH' ?></div>
                        <div class="user-role">Ressources Humaines</div>
                    </div>
                </div>
            </header>

            <div class="content">
                <div class="page-header">
                    <h2>Vue d'ensemble des compétences</h2>
                    <a href="/competences/exportPDF" class="export-btn">
                        <i data-feather="download"></i>
                        Exporter le rapport
                    </a>
                </div>

                <!-- Cartes de statistiques -->
                <div class="dashboard-grid">
                    <?php
                    $totalCompetences = count($competences);
                    $totalEmployes = array_sum(array_column($competences, 'nb_employes'));
                    $moyenneGenerale = $totalCompetences > 0 ? 
                        array_sum(array_column($competences, 'niveau_moyen')) / $totalCompetences : 0;
                    ?>
                    <div class="stat-card fade-in">
                        <h3>Compétences recensées</h3>
                        <div class="stat-value"><?= $totalCompetences ?></div>
                        <div class="stat-trend trend-up">
                            <i data-feather="trending-up"></i>
                            <span>+3 cette année</span>
                        </div>
                    </div>
                    
                    <div class="stat-card fade-in">
                        <h3>Employés concernés</h3>
                        <div class="stat-value"><?= $totalEmployes ?></div>
                        <div class="stat-trend trend-up">
                            <i data-feather="users"></i>
                            <span>Couverture complète</span>
                        </div>
                    </div>
                    
                    <div class="stat-card fade-in">
                        <h3>Niveau moyen général</h3>
                        <div class="stat-value"><?= number_format($moyenneGenerale, 1) ?>/5</div>
                        <div class="stat-trend trend-up">
                            <i data-feather="activity"></i>
                            <span>+0.3 vs dernier trimestre</span>
                        </div>
                    </div>
                </div>

                <!-- Tableau des compétences -->
                <div class="table-container fade-in">
                    <div class="table-header">
                        <h3>Détail des compétences</h3>
                        <div class="table-actions">
                            <div class="search-box">
                                <i data-feather="search"></i>
                                <input type="text" id="searchInput" placeholder="Rechercher une compétence...">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="skillsTable">
                            <thead>
                                <tr>
                                    <th>Compétence</th>
                                    <th>Nombre d'employés</th>
                                    <th>Niveau moyen</th>
                                    <th>Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($competences as $c): 
                                    $niveauPourcentage = ($c['niveau_moyen'] / 5) * 100;
                                ?>
                                <tr>
                                    <td>
                                        <a href="/competences/cartographie/<?= $c['id_competence'] ?>" class="skill-link">
                                            <i data-feather="chevron-right"></i>
                                            <?= $c['nom_competence'] ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="employee-count"><?= $c['nb_employes'] ?></span>
                                    </td>
                                    <td>
                                        <div class="skill-level">
                                            <div class="level-bar">
                                                <div class="level-fill" style="width: <?= $niveauPourcentage ?>%"></div>
                                            </div>
                                            <div class="level-text"><?= number_format($c['niveau_moyen'], 1) ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="distribution">
                                            <div class="distribution-icons">
                                                <?php 
                                                $stars = round($c['niveau_moyen']);
                                                for ($i = 1; $i <= 5; $i++): 
                                                    if ($i <= $stars) {
                                                        echo '<i data-feather="star" style="width: 14px; height: 14px; color: #FFB74D;"></i>';
                                                    } else {
                                                        echo '<i data-feather="star" style="width: 14px; height: 14px; color: #E0E0E0;"></i>';
                                                    }
                                                endfor; 
                                                ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="charts-container">
                    <div class="chart-wrapper">
                        <h3>
                            <span class="chart-icon">📊</span>
                            Répartition des employés par compétence
                        </h3>
                        <canvas id="chartEmployes"></canvas>
                    </div>
                    
                    <div class="chart-wrapper">
                        <h3>
                            <span class="chart-icon">📈</span>
                            Niveau de maîtrise par compétence
                        </h3>
                        <canvas id="chartNiveaux"></canvas>
                    </div>

                    <div class="chart-wrapper">
                        <h3>
                            <span class="chart-icon">🎯</span>
                            Distribution des niveaux
                        </h3>
                        <canvas id="chartDistribution"></canvas>
                    </div>

                    <div class="chart-wrapper">
                        <h3>
                            <span class="chart-icon">💡</span>
                            Top 5 compétences
                        </h3>
                        <canvas id="chartTop5"></canvas>
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

        // Palette de couleurs professionnelle
        const colors = {
            primary: '#6366f1',
            secondary: '#8b5cf6',
            accent: '#ec4899',
            success: '#10b981',
            warning: '#f59e0b',
            info: '#06b6d4',
            gradient: ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#06b6d4']
        };

        // Configuration commune
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: {
                        font: { family: 'Outfit', size: 12 },
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        };

        // Graphique 1: Nombre d'employés (Bar horizontal)
        new Chart(document.getElementById('chartEmployes'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "Nombre d'employés",
                    data: nbEmployes,
                    backgroundColor: colors.gradient,
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Graphique 2: Niveau moyen (Radar)
        new Chart(document.getElementById('chartNiveaux'), {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Niveau moyen',
                    data: niveauxMoyens,
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: colors.primary,
                    borderWidth: 2,
                    pointBackgroundColor: colors.primary,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: colors.primary,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            font: { size: 11 }
                        },
                        grid: { color: '#e2e8f0' }
                    }
                }
            }
        });

        // Graphique 3: Distribution des niveaux (Doughnut)
        const excellent = niveauxMoyens.filter(n => n >= 4).length;
        const bon = niveauxMoyens.filter(n => n >= 3 && n < 4).length;
        const aDevelopper = niveauxMoyens.filter(n => n < 3).length;

        new Chart(document.getElementById('chartDistribution'), {
            type: 'doughnut',
            data: {
                labels: ['Excellent (≥4)', 'Bon (3-4)', 'À développer (<3)'],
                datasets: [{
                    data: [excellent, bon, aDevelopper],
                    backgroundColor: [colors.success, colors.info, colors.warning],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                ...commonOptions,
                cutout: '65%',
                plugins: {
                    ...commonOptions.plugins,
                    legend: { position: 'bottom' }
                }
            }
        });

        // Graphique 4: Top 5 compétences (Bar)
        const sortedData = labels.map((label, i) => ({
            label,
            value: nbEmployes[i]
        })).sort((a, b) => b.value - a.value).slice(0, 5);

        new Chart(document.getElementById('chartTop5'), {
            type: 'bar',
            data: {
                labels: sortedData.map(d => d.label),
                datasets: [{
                    label: 'Employés',
                    data: sortedData.map(d => d.value),
                    backgroundColor: [
                        colors.primary,
                        colors.secondary,
                        colors.accent,
                        colors.info,
                        colors.success
                    ],
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
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