<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la compétence | Dashboard RH</title>
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
            --dark-text: #2d3748;
            --light-text: #718096;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --gradient-primary: linear-gradient(135deg, #2c5aa0 0%, #6c8bc7 100%);
            --gradient-accent: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            --gradient-success: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--light-bg);
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

        /* En-tête de compétence */
        .competence-header {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
            /* border-left: 5px solid var(--primary-color); */
            position: relative;
            overflow: hidden;
        }

        .competence-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: var(--gradient-primary);
            opacity: 0.05;
            border-radius: 0 0 0 100%;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 1;
        }

        .competence-info h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--dark-text);
        }

        .competence-description {
            color: var(--light-text);
            font-size: 1rem;
            line-height: 1.6;
            max-width: 800px;
            margin-bottom: 0;
        }

        .competence-stats {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--light-text);
            margin-top: 5px;
        }

        /* Statistiques détaillées */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .stat-card h3 {
            font-size: 0.95rem;
            color: var(--light-text);
            margin-bottom: 10px;
            font-weight: 500;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stat-card .trend {
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

        /* Distribution des niveaux */
        .distribution-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
        }

        .distribution-container h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-text);
        }

        .distribution-bars {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .distribution-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .level-label {
            min-width: 80px;
            font-weight: 500;
        }

        .level-stars {
            display: flex;
            gap: 3px;
        }

        .level-stars i {
            width: 16px;
            height: 16px;
        }

        .level-bar-container {
            flex: 1;
            height: 10px;
            background: #f0f0f0;
            border-radius: 5px;
            overflow: hidden;
        }

        .level-bar {
            height: 100%;
            border-radius: 5px;
        }

        .level-count {
            min-width: 40px;
            text-align: right;
            font-weight: 600;
            color: var(--dark-text);
        }

        /* Contrôles de recherche et filtres */
        .controls-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .back-btn {
            background: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .back-btn:hover {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
        }

        /* Tableau */
        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 40px;
        }

        .table-header {
            background: var(--gradient-primary);
            color: white;
            padding: 20px;
        }

        .table-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }

        .table {
            margin: 0;
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table thead th {
            background-color: #f8fafc;
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: var(--dark-text);
            padding: 18px 20px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            position: relative;
            white-space: nowrap;
        }

        .table thead th:hover {
            background-color: #edf2f7;
        }

        .table thead th .sort-icon {
            margin-left: 8px;
            opacity: 0.5;
            transition: opacity 0.2s;
            font-size: 0.8rem;
        }

        .table thead th:hover .sort-icon {
            opacity: 1;
        }

        .table thead th.sorted-asc .sort-icon,
        .table thead th.sorted-desc .sort-icon {
            opacity: 1;
        }

        .table thead th.sorted-asc .sort-icon::after {
            content: "↑";
            color: var(--primary-color);
        }

        .table thead th.sorted-desc .sort-icon::after {
            content: "↓";
            color: var(--primary-color);
        }

        .table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover td {
            background-color: #f8fafc;
        }

        .employee-name {
            font-weight: 600;
            color: var(--dark-text);
        }

        .employee-email {
            color: var(--light-text);
            font-size: 0.9rem;
        }

        .department-badge {
            display: inline-block;
            background: #e8f4ff;
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .position-badge {
            display: inline-block;
            background: #f0f0f0;
            color: var(--dark-text);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .niveau-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .niveau-bar {
            flex: 1;
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .niveau-fill {
            height: 100%;
            border-radius: 4px;
        }

        .niveau-text {
            min-width: 30px;
            text-align: center;
            font-weight: 700;
            font-size: 1rem;
        }

        /* Graphique */
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
        }

        .chart-container h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-text);
        }

        .chart-wrapper {
            height: 300px;
            position: relative;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .competence-stats {
                justify-content: flex-start;
            }
            
            .controls-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .competence-stats {
                flex-wrap: wrap;
            }
            
            .stat-item {
                flex: 1;
                min-width: 120px;
            }
            
            .table-responsive {
                font-size: 0.85rem;
            }
            
            .table thead th,
            .table tbody td {
                padding: 12px 10px;
            }
        }

        @media (max-width: 576px) {
            .main-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-info {
                align-self: flex-start;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .competence-header, .stats-grid, .distribution-container,
        .controls-container, .table-container, .chart-container {
            opacity: 0;
        }

        .competence-header { animation-delay: 0.1s; }
        .stats-grid { animation-delay: 0.2s; }
        .distribution-container { animation-delay: 0.3s; }
        .controls-container { animation-delay: 0.4s; }
        .table-container { animation-delay: 0.5s; }
        .chart-container { animation-delay: 0.6s; }
    </style>
</head>

<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="bar-chart-2"></i>
                    Détails de la compétence
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
                <!-- En-tête de compétence -->
                <div class="competence-header fade-in">
                    <div class="header-content">
                        <div class="competence-info">
                            <h2><?= $competence['nom_competence'] ?></h2>
                            <p class="competence-description"><?= $competence['description'] ?></p>
                        </div>
                        <div class="competence-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?= count($employees) ?></div>
                                <div class="stat-label">Employés</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">
                                    <?= number_format(array_sum(array_column($employees, 'niveau_actuel')) / max(1, count($employees)), 1) ?>
                                </div>
                                <div class="stat-label">Niveau moyen</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">
                                    <?= max(array_column($employees, 'niveau_actuel') ?: [0]) ?>
                                </div>
                                <div class="stat-label">Niveau max</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques détaillées -->
                <?php
                // Calcul des statistiques
                $niveaux = array_column($employees, 'niveau_actuel');
                $moyenne = count($niveaux) > 0 ? array_sum($niveaux) / count($niveaux) : 0;
                $max = count($niveaux) > 0 ? max($niveaux) : 0;
                $min = count($niveaux) > 0 ? min($niveaux) : 0;
                $ecartType = 0;
                if (count($niveaux) > 0) {
                    $carres = array_map(fn($x) => pow($x - $moyenne, 2), $niveaux);
                    $ecartType = sqrt(array_sum($carres) / count($niveaux));
                }
                
                // Distribution par niveau
                $distribution = array_fill(1, 5, 0);
                foreach ($niveaux as $niveau) {
                    if ($niveau >= 1 && $niveau <= 5) {
                        $distribution[(int)$niveau]++;
                    }
                }
                
                // Par département
                $parDepartement = [];
                foreach ($employees as $e) {
                    $dept = $e['nom_departement'] ?? 'Non défini';
                    if (!isset($parDepartement[$dept])) {
                        $parDepartement[$dept] = 0;
                    }
                    $parDepartement[$dept]++;
                }
                $topDepartement = count($parDepartement) > 0 ? 
                    array_keys($parDepartement, max($parDepartement))[0] : 'Aucun';
                ?>

                <div class="stats-grid fade-in">
                    <div class="stat-card">
                        <h3>Écart-type</h3>
                        <div class="value"><?= number_format($ecartType, 2) ?></div>
                        <div class="trend">
                            <span>Homogénéité des compétences</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Écart min-max</h3>
                        <div class="value"><?= number_format($max - $min, 1) ?></div>
                        <div class="trend">
                            <span>Différence de niveaux</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Département le plus compétent</h3>
                        <div class="value"><?= $topDepartement ?></div>
                        <div class="trend">
                            <span><?= max($parDepartement) ?> employés</span>
                        </div>
                    </div>
                </div>

                <!-- Distribution des niveaux -->
                <div class="distribution-container fade-in">
                    <h3>Distribution des niveaux</h3>
                    <div class="distribution-bars">
                        <?php for ($i = 5; $i >= 1; $i--): 
                            $count = $distribution[$i] ?? 0;
                            $percentage = count($employees) > 0 ? ($count / count($employees)) * 100 : 0;
                            $color = match($i) {
                                5 => '#4caf50',
                                4 => '#8bc34a',
                                3 => '#ff9800',
                                2 => '#ff5722',
                                1 => '#f44336',
                                default => '#e0e0e0'
                            };
                        ?>
                        <div class="distribution-item">
                            <div class="level-label">Niveau <?= $i ?></div>
                            <div class="level-stars">
                                <?php for ($j = 1; $j <= 5; $j++): ?>
                                    <i data-feather="star" style="width: 16px; height: 16px; color: <?= $j <= $i ? '#FFB74D' : '#E0E0E0' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <div class="level-bar-container">
                                <div class="level-bar" style="width: <?= $percentage ?>%; background: <?= $color ?>;"></div>
                            </div>
                            <div class="level-count"><?= $count ?></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Contrôles -->
                <div class="controls-container fade-in">
                    <a href="/competences/cartographie" class="back-btn">
                        <i data-feather="arrow-left"></i> Retour à la cartographie
                    </a>
                    <div class="search-box">
                        <i data-feather="search"></i>
                        <input type="text" id="search" class="form-control" placeholder="Rechercher un employé, département ou poste...">
                    </div>
                </div>

                <!-- Tableau des employés -->
                <div class="table-container fade-in">
                    <div class="table-header">
                        <h3>Employés maîtrisant cette compétence (<?= count($employees) ?>)</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="employeesTable">
                            <thead>
                                <tr>
                                    <th data-sort="employe">
                                        Employé <span class="sort-icon"></span>
                                    </th>
                                    <th data-sort="poste">
                                        Poste <span class="sort-icon"></span>
                                    </th>
                                    <th data-sort="departement">
                                        Département <span class="sort-icon"></span>
                                    </th>
                                    <th data-sort="niveau">
                                        Niveau <span class="sort-icon"></span>
                                    </th>
                                    <th data-sort="contact">
                                        Contact <span class="sort-icon"></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $e): 
                                    $niveau = $e['niveau_actuel'];
                                    $niveauColor = match(true) {
                                        $niveau >= 4 => '#4caf50',
                                        $niveau >= 3 => '#ff9800',
                                        $niveau >= 2 => '#ff5722',
                                        default => '#f44336'
                                    };
                                ?>
                                <tr>
                                    <td data-sort-value="<?= strtolower($e['prenom'] . ' ' . $e['nom']) ?>">
                                        <div class="employee-name"><?= $e['prenom'] . ' ' . $e['nom'] ?></div>
                                        <div class="employee-email"><?= $e['email'] ?></div>
                                    </td>
                                    <td data-sort-value="<?= strtolower($e['nom_poste']) ?>">
                                        <span class="position-badge"><?= $e['nom_poste'] ?></span>
                                    </td>
                                    <td data-sort-value="<?= $e['nom_departement'] ?>">
                                        <span class="department-badge"><?= $e['nom_departement'] ?></span>
                                    </td>
                                    <td data-sort-value="<?= $e['niveau_actuel'] ?>">
                                        <div class="niveau-indicator">
                                            <div class="niveau-bar">
                                                <div class="niveau-fill" style="width: <?= ($niveau / 5) * 100 ?>%; background: <?= $niveauColor ?>;"></div>
                                            </div>
                                            <div class="niveau-text" style="color: <?= $niveauColor ?>;"><?= $niveau ?></div>
                                        </div>
                                    </td>
                                    <td data-sort-value="<?= $e['telephone'] ?>">
                                        <div><?= $e['telephone'] ?></div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Graphique de répartition par département -->
                <div class="chart-container fade-in">
                    <h3>Répartition par département</h3>
                    <div class="chart-wrapper">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation au chargement
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach(el => {
                el.style.animation = 'fadeIn 0.5s ease-out forwards';
            });
            
            // Initialiser Feather Icons
            feather.replace();
            
            // ---- Recherche dynamique ----
            document.getElementById('search').addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll("#employeesTable tbody tr");

                rows.forEach(row => {
                    const txt = row.textContent.toLowerCase();
                    row.style.display = txt.includes(filter) ? "" : "none";
                });
            });

            // ---- Tri des colonnes ----
            document.querySelectorAll('#employeesTable th[data-sort]').forEach(header => {
                header.addEventListener('click', () => {
                    const table = header.closest('table');
                    const tbody = table.querySelector('tbody');
                    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
                    const sortKey = header.getAttribute('data-sort');
                    
                    // Réinitialiser les autres en-têtes
                    document.querySelectorAll('#employeesTable th').forEach(th => {
                        if (th !== header) {
                            th.classList.remove('sorted-asc', 'sorted-desc');
                        }
                    });

                    // Basculer entre ascendant, descendant et aucun tri
                    if (!header.classList.contains('sorted-asc') && !header.classList.contains('sorted-desc')) {
                        header.classList.add('sorted-asc');
                        sortTable(tbody, columnIndex, 'asc', sortKey);
                    } else if (header.classList.contains('sorted-asc')) {
                        header.classList.remove('sorted-asc');
                        header.classList.add('sorted-desc');
                        sortTable(tbody, columnIndex, 'desc', sortKey);
                    } else {
                        header.classList.remove('sorted-desc');
                        resetTableOrder(tbody);
                    }
                });
            });

            function sortTable(tbody, columnIndex, direction, sortKey) {
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                rows.sort((a, b) => {
                    const cellA = a.cells[columnIndex];
                    const cellB = b.cells[columnIndex];
                    
                    let valueA = cellA.getAttribute('data-sort-value') || cellA.textContent.trim();
                    let valueB = cellB.getAttribute('data-sort-value') || cellB.textContent.trim();
                    
                    // Conversion numérique pour les colonnes de niveau
                    if (sortKey === 'niveau') {
                        valueA = parseFloat(valueA) || 0;
                        valueB = parseFloat(valueB) || 0;
                    }
                    
                    if (direction === 'asc') {
                        return valueA < valueB ? -1 : valueA > valueB ? 1 : 0;
                    } else {
                        return valueA > valueB ? -1 : valueA < valueB ? 1 : 0;
                    }
                });
                
                // Réorganiser les lignes
                rows.forEach(row => tbody.appendChild(row));
            }

            function resetTableOrder(tbody) {
                // Pourrait restaurer l'ordre original si nécessaire
            }

            // ---- Graphique de répartition par département ----
            <?php
            // Préparation des données pour le graphique
            $deptLabels = array_keys($parDepartement);
            $deptData = array_values($parDepartement);
            ?>
            
            const deptCtx = document.getElementById('departmentChart').getContext('2d');
            const deptColors = [
                '#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', 
                '#9966FF', '#FF9F40', '#C9CBCF', '#4CAF50',
                '#2196F3', '#FF5722'
            ];
            
            new Chart(deptCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($deptLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($deptData) ?>,
                        backgroundColor: deptColors,
                        borderColor: 'white',
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                font: {
                                    family: 'Outfit',
                                    size: 12
                                },
                                color: '#718096'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { family: 'Outfit', size: 13 },
                            bodyFont: { family: 'Outfit', size: 13 },
                            padding: 12,
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} employés (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
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
        });
    </script>

    <script src="/assets/js/rh-dashboard.js"></script>
</body>

</html>