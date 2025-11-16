<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques RH - <?= $is_rh ? 'Vue Globale' : 'Mon Département' ?></title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-container {
            padding: 2rem;
        }
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            /* border-left: 4px solid #007bff; */
        }
        .summary-card.gender { border-left-color: #6f42c1; }
        .summary-card.age { border-left-color: #fd7e14; }
        .summary-card.dept { border-left-color: #20c997; }
        .summary-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin: 0.5rem 0;
        }
        .summary-card p {
            color: #666;
            margin: 0;
            font-size: 0.9rem;
        }
        .summary-card small {
            color: #999;
            font-size: 0.8rem;
        }
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .chart-card h4 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .table-card h4 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            font-weight: 600;
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
                    Statistiques des Effectifs
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge bg-primary text-white"><?= $user_type ?? '' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Résumé des effectifs -->
                <div class="stats-summary">
                    <div class="summary-card">
                        <i data-feather="users" style="color: #007bff;"></i>
                        <h3><?= $resume['total_employes'] ?? 0 ?></h3>
                        <p>Total Employés</p>
                        <small>Contrats actifs</small>
                    </div>
                    
                    <div class="summary-card gender">
                        <i data-feather="user" style="color: #6f42c1;"></i>
                        <h3><?= $resume['total_hommes'] ?? 0 ?></h3>
                        <p>Hommes</p>
                        <small><?= $resume['total_employes'] > 0 ? round(($resume['total_hommes'] / $resume['total_employes']) * 100, 1) : 0 ?>%</small>
                    </div>
                    
                    <div class="summary-card gender">
                        <i data-feather="user" style="color: #6f42c1;"></i>
                        <h3><?= $resume['total_femmes'] ?? 0 ?></h3>
                        <p>Femmes</p>
                        <small><?= $resume['total_employes'] > 0 ? round(($resume['total_femmes'] / $resume['total_employes']) * 100, 1) : 0 ?>%</small>
                    </div>
                    
                    <div class="summary-card age">
                        <i data-feather="calendar" style="color: #fd7e14;"></i>
                        <h3><?= $resume['age_moyen'] ?? 0 ?></h3>
                        <p>Âge Moyen</p>
                        <small>Années</small>
                    </div>
                    
                    <?php if ($is_rh): ?>
                    <div class="summary-card dept">
                        <i data-feather="briefcase" style="color: #20c997;"></i>
                        <h3><?= $resume['total_departements'] ?? 0 ?></h3>
                        <p>Départements</p>
                        <small>Services actifs</small>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Graphiques -->
                <div class="chart-grid">
                    <!-- Répartition par genre -->
                    <div class="chart-card">
                        <h4>
                            <i data-feather="pie-chart"></i>
                            Répartition par Genre
                        </h4>
                        <div class="chart-container">
                            <canvas id="chartGenre"></canvas>
                        </div>
                    </div>

                    <!-- Répartition par âge -->
                    <div class="chart-card">
                        <h4>
                            <i data-feather="bar-chart"></i>
                            Répartition par Âge
                        </h4>
                        <div class="chart-container">
                            <canvas id="chartAge"></canvas>
                        </div>
                    </div>

                    <!-- Répartition par type de contrat -->
                    <div class="chart-card">
                        <h4>
                            <i data-feather="file-text"></i>
                            Types de Contrat
                        </h4>
                        <div class="chart-container">
                            <canvas id="chartContrat"></canvas>
                        </div>
                    </div>

                    <?php if ($is_rh): ?>
                    <!-- Répartition par département (RH uniquement) -->
                    <div class="chart-card">
                        <h4>
                            <i data-feather="briefcase"></i>
                            Répartition par Département
                        </h4>
                        <div class="chart-container">
                            <canvas id="chartDepartement"></canvas>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tableau des postes -->
                <div class="table-card">
                    <h4>
                        <i data-feather="award"></i>
                        Top 10 des Postes les Plus Occupés
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Poste</th>
                                    <?php if ($is_rh): ?>
                                    <th>Département</th>
                                    <?php endif; ?>
                                    <th>Nombre d'Employés</th>
                                    <th>Pourcentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 1;
                                foreach ($effectifs_poste as $poste): 
                                    $pourcentage = $resume['total_employes'] > 0 ? round(($poste['nombre'] / $resume['total_employes']) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><strong><?= $rank++ ?></strong></td>
                                    <td><?= htmlspecialchars($poste['nom']) ?></td>
                                    <?php if ($is_rh): ?>
                                    <td><?= htmlspecialchars($poste['nom_departement'] ?? 'N/A') ?></td>
                                    <?php endif; ?>
                                    <td><span class="badge bg-primary"><?= $poste['nombre'] ?></span></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $pourcentage ?>%;" aria-valuenow="<?= $pourcentage ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?= $pourcentage ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Statistiques croisées département/genre -->
                <?php if (!empty($effectifs_dept_genre)): ?>
                <div class="table-card">
                    <h4>
                        <i data-feather="grid"></i>
                        Répartition Genre par Département
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Département</th>
                                    <th>Hommes</th>
                                    <th>Femmes</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $current_dept = null;
                                $dept_hommes = 0;
                                $dept_femmes = 0;
                                
                                foreach ($effectifs_dept_genre as $index => $row):
                                    if ($current_dept !== $row['nom_departement']) {
                                        if ($current_dept !== null) {
                                            // Afficher la ligne précédente
                                            echo "<tr>";
                                            echo "<td><strong>" . htmlspecialchars($current_dept) . "</strong></td>";
                                            echo "<td><span class='badge bg-info text-white'>" . $dept_hommes . "</span></td>";
                                            echo "<td><span class='badge bg-warning text-white'>" . $dept_femmes . "</span></td>";
                                            echo "<td><span class='badge bg-success text-white'>" . ($dept_hommes + $dept_femmes) . "</span></td>";
                                            echo "</tr>";
                                        }
                                        
                                        $current_dept = $row['nom_departement'];
                                        $dept_hommes = 0;
                                        $dept_femmes = 0;
                                    }
                                    
                                    if ($row['genre'] === 'Homme') {
                                        $dept_hommes = $row['nombre'];
                                    } else {
                                        $dept_femmes = $row['nombre'];
                                    }
                                    
                                    // Si c'est le dernier élément, afficher la ligne
                                    if ($index === count($effectifs_dept_genre) - 1) {
                                        echo "<tr>";
                                        echo "<td><strong>" . htmlspecialchars($current_dept) . "</strong></td>";
                                        echo "<td><span class='badge bg-info text-white'>" . $dept_hommes . "</span></td>";
                                        echo "<td><span class='badge bg-warning text-white'>" . $dept_femmes . "</span></td>";
                                        echo "<td><span class='badge bg-success text-white'>" . ($dept_hommes + $dept_femmes) . "</span></td>";
                                        echo "</tr>";
                                    }
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="/css/bootstrap.bundle.min.js"></script>
    <script>
        // Initialiser Feather Icons
        feather.replace();

        // Gestion du menu déroulant
        document.querySelectorAll('.has-submenu > .menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const parent = link.parentElement;
                // Fermer tous les autres sous-menus
                document.querySelectorAll('.has-submenu').forEach(item => {
                    if (item !== parent) {
                        item.classList.remove('open');
                    }
                });
                // Toggle le sous-menu actuel
                parent.classList.toggle('open');
            });
        });

        // Configuration des couleurs
        const colors = {
            primary: '#007bff',
            success: '#28a745',
            danger: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8',
            purple: '#6f42c1',
            orange: '#fd7e14',
            teal: '#20c997',
            pink: '#e83e8c'
        };

        const chartColors = [
            colors.primary,
            colors.success,
            colors.danger,
            colors.warning,
            colors.info,
            colors.purple,
            colors.orange,
            colors.teal,
            colors.pink
        ];

        // Graphique Genre (Pie Chart)
        const ctxGenre = document.getElementById('chartGenre').getContext('2d');
        new Chart(ctxGenre, {
            type: 'doughnut',
            data: {
                labels: <?= $chart_genre['labels'] ?>,
                datasets: [{
                    data: <?= $chart_genre['values'] ?>,
                    backgroundColor: [colors.primary, colors.pink],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Graphique Âge (Bar Chart)
        const ctxAge = document.getElementById('chartAge').getContext('2d');
        new Chart(ctxAge, {
            type: 'bar',
            data: {
                labels: <?= $chart_age['labels'] ?>,
                datasets: [{
                    label: 'Nombre d\'employés',
                    data: <?= $chart_age['values'] ?>,
                    backgroundColor: colors.orange,
                    borderColor: colors.orange,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Graphique Type de Contrat (Pie Chart)
        const ctxContrat = document.getElementById('chartContrat').getContext('2d');
        new Chart(ctxContrat, {
            type: 'pie',
            data: {
                labels: <?= $chart_contrat['labels'] ?>,
                datasets: [{
                    data: <?= $chart_contrat['values'] ?>,
                    backgroundColor: chartColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        <?php if ($is_rh): ?>
        // Graphique Département (Bar Chart horizontal)
        const ctxDept = document.getElementById('chartDepartement').getContext('2d');
        new Chart(ctxDept, {
            type: 'bar',
            data: {
                labels: <?= $chart_departement['labels'] ?>,
                datasets: [{
                    label: 'Nombre d\'employés',
                    data: <?= $chart_departement['values'] ?>,
                    backgroundColor: colors.teal,
                    borderColor: colors.teal,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
