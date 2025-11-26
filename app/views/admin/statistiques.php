<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques RH - <?= $is_rh ? 'Vue Globale' : 'Mon Département' ?></title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --color-primary: #4c6ef5;
            --color-secondary: #5f6f92;
            --color-success: #1cc88a;
            --color-info: #36b9cc;
            --color-warning: #f6c23e;
            --color-danger: #f56565;
            --color-violet: #805ad5;
            --color-orange: #f97316;
            --color-teal: #20c997;
            --surface-base: #ffffff;
            --surface-muted: #f4f5fb;
            --shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.08);
            --shadow-hover: 0 22px 45px rgba(15, 23, 42, 0.12);
        }

        body {
            background: var(--surface-muted);
            /* font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; */
            font-family: 'Outfit', sans-serif;
            color: var(--color-secondary);
        }

        .main-content {
            background: transparent;
        }

        .content-wrapper {
            padding: 2.5rem 2rem;
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .summary-card {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            background: var(--surface-base);
            border-radius: 22px;
            padding: 1.75rem;
            box-shadow: var(--shadow-soft);
            border: none;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .summary-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
        }

        .card-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--surface-base);
            flex-shrink: 0;
        }

        .icon-blue { background: linear-gradient(135deg, #5a8dee, #4c6ef5); }
        .icon-violet { background: linear-gradient(135deg, #9d5cff, #6f42c1); }
        .icon-orange { background: linear-gradient(135deg, #ff9a62, #f97316); }
        .icon-red { background: linear-gradient(135deg, #ff7a85, #f56565); }
        .icon-teal { background: linear-gradient(135deg, #3ed0bc, #20c997); }

        .card-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .card-label {
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #a0a5ba;
            font-weight: 700;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            line-height: 1.1;
        }

        .card-value small {
            font-size: 0.5em;
            margin-left: 0.2rem;
            font-weight: 600;
        }

        .card-subvalue {
            font-size: 0.85rem;
            color: #b0b4c3;
        }

        .turnover-section {
            margin-bottom: 2.5rem;
        }

        .turnover-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .turnover-filter {
            background: var(--surface-base);
            border-radius: 22px;
            padding: 2rem;
            box-shadow: var(--shadow-soft);
        }

        .turnover-filter h4 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
        }

        .turnover-filter .form-label {
            font-weight: 600;
            color: var(--color-secondary);
        }

        .turnover-filter .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.65rem 1.25rem;
        }

        .turnover-stats {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .turnover-kpi {
            background: linear-gradient(135deg, #ff9a62, #f56565);
            border-radius: 22px;
            padding: 2rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .turnover-kpi::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.15);
            top: -30px;
            right: -40px;
            transform: rotate(35deg);
        }

        .turnover-kpi span {
            display: inline-block;
        }

        .turnover-kpi .kpi-label {
            font-size: 0.8rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 700;
            opacity: 0.9;
        }

        .turnover-kpi .kpi-value {
            font-size: 3rem;
            font-weight: 800;
            margin: 0.75rem 0 0.25rem;
            line-height: 1;
        }

        .turnover-kpi .kpi-helper {
            font-size: 0.95rem;
            opacity: 0.85;
        }

        .turnover-details {
            background: var(--surface-base);
            border-radius: 22px;
            padding: 1.75rem;
            box-shadow: var(--shadow-soft);
        }

        .turnover-details h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .detail-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #a0a5ba;
            font-weight: 700;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
            gap: 1.75rem;
            margin-bottom: 2.5rem;
        }

        .chart-card,
        .table-card {
            background: var(--surface-base);
            border-radius: 22px;
            padding: 2rem;
            box-shadow: var(--shadow-soft);
            border: none;
            margin-bottom: 2rem;
        }

        .chart-card h4,
        .table-card h4 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.75rem;
        }

        .chart-container {
            position: relative;
            height: 340px;
            width: 100%;
        }

        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
        }

        table.table {
            margin-bottom: 0;
            color: var(--color-secondary);
        }

        table.table thead {
            background: #f8f9ff;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        table.table tbody tr:hover {
            background: rgba(76, 110, 245, 0.05);
        }

        .progress {
            height: 10px;
            border-radius: 999px;
            background-color: #edf0ff;
        }

        .progress-bar {
            border-radius: 999px;
        }

        .badge {
            border-radius: 10px;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
        }

        @media (max-width: 767px) {
            .content-wrapper {
                padding: 1.5rem 1rem;
            }

            .summary-card {
                padding: 1.25rem;
            }

            .card-value {
                font-size: 1.6rem;
            }

            .turnover-grid {
                grid-template-columns: 1fr;
            }

            .chart-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 260px;
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
                    Statistiques des Effectifs
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge bg-primary text-white"><?= $user_type ?? '' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <?php
                    $turnoverRate = $taux_turnover['taux_turnover'] ?? 0;
                    $turnoverStart = $date_debut ?? date('Y-01-01');
                    $turnoverEnd = $date_fin ?? date('Y-12-31');
                    $turnoverInterpretation = 'Données insuffisantes';
                    
                    $absenteismeRate = $taux_absenteisme['taux_absenteisme'] ?? 0;
                    $absenteismeStart = $date_debut ?? date('Y-01-01');
                    $absenteismeEnd = $date_fin ?? date('Y-12-31');
                    $absenteismeInterpretation = 'Données insuffisantes';
                    
                    $ancienneteMoyenne = $anciennete_moyenne['anciennete_moyenne'] ?? 0;
                    
                    $totalEmployes = $resume['total_employes'] ?? 0;
                    $totalHommes = $resume['total_hommes'] ?? 0;
                    $totalFemmes = $resume['total_femmes'] ?? 0;
                    $ageMoyen = $resume['age_moyen'] ?? 0;
                    $totalDepartements = $resume['total_departements'] ?? 0;
                    $pourcentageHommes = $totalEmployes > 0 ? round(($totalHommes / $totalEmployes) * 100, 1) : 0;
                    $pourcentageFemmes = $totalEmployes > 0 ? round(($totalFemmes / $totalEmployes) * 100, 1) : 0;

                    if (($taux_turnover['nb_mois_calcul'] ?? 0) > 0) {
                        if ($turnoverRate < 5) {
                            $turnoverInterpretation = 'Très faible (excellent)';
                        } elseif ($turnoverRate < 10) {
                            $turnoverInterpretation = 'Faible (bon)';
                        } elseif ($turnoverRate < 15) {
                            $turnoverInterpretation = 'Moyen';
                        } elseif ($turnoverRate < 20) {
                            $turnoverInterpretation = 'Élevé (attention)';
                        } else {
                            $turnoverInterpretation = 'Très élevé (critique)';
                        }
                    }
                    
                    if (($taux_absenteisme['jours_periode'] ?? 0) > 0) {
                        if ($absenteismeRate < 2) {
                            $absenteismeInterpretation = 'Très faible (excellent)';
                        } elseif ($absenteismeRate < 5) {
                            $absenteismeInterpretation = 'Faible (bon)';
                        } elseif ($absenteismeRate < 8) {
                            $absenteismeInterpretation = 'Moyen';
                        } elseif ($absenteismeRate < 12) {
                            $absenteismeInterpretation = 'Élevé (attention)';
                        } else {
                            $absenteismeInterpretation = 'Très élevé (critique)';
                        }
                    }
                ?>
                <!-- Résumé des effectifs -->
                <div class="stats-summary">
                    <div class="summary-card">
                        <div class="card-icon icon-blue">
                            <i data-feather="users" width="26" height="26"></i>
                        </div>
                        <div class="card-details">
                            <span class="card-label">Total employés</span>
                            <span class="card-value"><?= number_format($totalEmployes, 0, ',', ' ') ?></span>
                            <span class="card-subvalue">Contrats actifs</span>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="card-icon icon-violet">
                            <i data-feather="user" width="26" height="26"></i>
                        </div>
                        <div class="card-details">
                            <span class="card-label">Hommes</span>
                            <span class="card-value"><?= number_format($totalHommes, 0, ',', ' ') ?></span>
                            <span class="card-subvalue"><?= $pourcentageHommes ?> % de l'effectif</span>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="card-icon icon-violet">
                            <i data-feather="user" width="26" height="26"></i>
                        </div>
                        <div class="card-details">
                            <span class="card-label">Femmes</span>
                            <span class="card-value"><?= number_format($totalFemmes, 0, ',', ' ') ?></span>
                            <span class="card-subvalue"><?= $pourcentageFemmes ?> % de l'effectif</span>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="card-icon icon-orange">
                            <i data-feather="calendar" width="26" height="26"></i>
                        </div>
                        <div class="card-details">
                            <span class="card-label">Âge moyen</span>
                            <span class="card-value"><?= round($ageMoyen, 1) ?><small>ans</small></span>
                            <span class="card-subvalue">Basé sur l'ensemble des employés</span>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="card-icon icon-red">
                            <i data-feather="trending-down" width="26" height="26"></i>
                        </div>
                        <div class="card-details">
                            <span class="card-label">Turnover</span>
                            <span class="card-value"><?= number_format($turnoverRate, 2, ',', ' ') ?><small>%</small></span>
                            <span class="card-subvalue">
                                <?= date('d/m/Y', strtotime($turnoverStart)) ?> – <?= date('d/m/Y', strtotime($turnoverEnd)) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="card-icon icon-orange">
                            <i data-feather="alert-circle" width="26" height="26"></i>
                        </div>
                        <div class="card-details">
                            <span class="card-label">Absentéisme</span>
                            <span class="card-value"><?= number_format($absenteismeRate, 2, ',', ' ') ?><small>%</small></span>
                            <span class="card-subvalue">
                                <?= date('d/m/Y', strtotime($absenteismeStart)) ?> – <?= date('d/m/Y', strtotime($absenteismeEnd)) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="card-icon icon-teal">
                            <i data-feather="clock" width="26" height="26"></i>
                        </div>
                        <div class="card-details">
                            <span class="card-label">Ancienneté moyenne</span>
                            <span class="card-value"><?= number_format($ancienneteMoyenne, 1, ',', ' ') ?><small>ans</small></span>
                            <span class="card-subvalue">Basé sur les contrats actifs</span>
                        </div>
                    </div>
                    
                    <?php if ($is_rh): ?>
                    <div class="summary-card">
                        <div class="card-icon icon-teal">
                            <i data-feather="briefcase" width="26" height="26"></i>
                        </div>
                        <div class="card-details">
                            <span class="card-label">Départements</span>
                            <span class="card-value"><?= number_format($totalDepartements, 0, ',', ' ') ?></span>
                            <span class="card-subvalue">Services actifs</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Taux de turnover -->
                <div class="turnover-section">
                    <div class="turnover-grid">
                        <div class="turnover-filter">
                            <h4>
                                <i data-feather="filter"></i>
                                Filtrer le taux de turnover et absentéisme
                            </h4>
                            <form method="GET" action="/statistiques" class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="date_debut" class="form-label">Date de début</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut"
                                           value="<?= $date_debut ?? date('Y-01-01') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="date_fin" class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin"
                                           value="<?= $date_fin ?? date('Y-12-31') ?>" required>
                                </div>
                                <div class="col-md-4 col-12 d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i data-feather="search"></i>
                                        Appliquer
                                    </button>
                                    <a href="/statistiques" class="btn btn-outline-secondary flex-grow-1">
                                        <i data-feather="refresh-cw"></i>
                                        Réinitialiser
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="turnover-stats">
                            <div class="turnover-kpi">
                                <span class="kpi-label">Taux de turnover</span>
                                <span class="kpi-value"><?= number_format($turnoverRate, 2, ',', ' ') ?>%</span>
                                <span class="kpi-helper"><?= $turnoverInterpretation ?></span>
                                <span class="kpi-helper"><?= date('d/m/Y', strtotime($turnoverStart)) ?> – <?= date('d/m/Y', strtotime($turnoverEnd)) ?></span>
                            </div>
                            <div class="turnover-details">
                                <h5>
                                    <i data-feather="info"></i>
                                    Résumé de la période
                                </h5>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Période analysée</span>
                                        <span class="detail-value"><?= date('d/m/Y', strtotime($turnoverStart)) ?> – <?= date('d/m/Y', strtotime($turnoverEnd)) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Départs</span>
                                        <span class="detail-value"><?= number_format($taux_turnover['departs'] ?? 0, 0, ',', ' ') ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Effectif moyen</span>
                                        <span class="detail-value"><?= number_format($taux_turnover['effectifs_moyens'] ?? 0, 0, ',', ' ') ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Mois calculés</span>
                                        <span class="detail-value"><?= $taux_turnover['nb_mois_calcul'] ?? 0 ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Interprétation</span>
                                        <span class="detail-value"><?= $turnoverInterpretation ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            primary: '#4c6ef5',
            success: '#1cc88a',
            danger: '#f56565',
            warning: '#f6c23e',
            info: '#36b9cc',
            violet: '#805ad5',
            orange: '#f97316',
            teal: '#20c997',
            pink: '#ff6bcb'
        };

        const chartColors = [
            '#4c6ef5',
            '#1cc88a',
            '#f56565',
            '#f6c23e',
            '#36b9cc',
            '#805ad5',
            '#f97316',
            '#20c997',
            '#ff6bcb'
        ];

        // Graphique Genre (Doughnut)
        const ctxGenre = document.getElementById('chartGenre').getContext('2d');
        new Chart(ctxGenre, {
            type: 'doughnut',
            data: {
                labels: <?= $chart_genre['labels'] ?>,
                datasets: [{
                    data: <?= $chart_genre['values'] ?>,
                    backgroundColor: [colors.primary, colors.pink],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: "'Nunito', sans-serif",
                                size: 12
                            }
                        }
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
                    borderRadius: 5,
                    barThickness: 20
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
                        grid: {
                            borderDash: [2, 2],
                            drawBorder: false,
                            color: '#e3e6f0'
                        },
                        ticks: {
                            stepSize: 1,
                            padding: 10,
                            font: {
                                family: "'Nunito', sans-serif"
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: "'Nunito', sans-serif"
                            }
                        }
                    }
                }
            }
        });

        // Graphique Type de Contrat (Doughnut)
        const ctxContrat = document.getElementById('chartContrat').getContext('2d');
        new Chart(ctxContrat, {
            type: 'doughnut',
            data: {
                labels: <?= $chart_contrat['labels'] ?>,
                datasets: [{
                    data: <?= $chart_contrat['values'] ?>,
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: "'Nunito', sans-serif",
                                size: 12
                            }
                        }
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
                    borderRadius: 5,
                    barThickness: 15
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
                        grid: {
                            borderDash: [2, 2],
                            drawBorder: false,
                            color: '#e3e6f0'
                        },
                        ticks: {
                            stepSize: 1,
                            padding: 10,
                            font: {
                                family: "'Nunito', sans-serif"
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: "'Nunito', sans-serif"
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
    
    <script>
        // Validation du formulaire de filtre de date
        document.querySelector('form[action="/statistiques"]').addEventListener('submit', function(e) {
            const dateDebut = new Date(document.getElementById('date_debut').value);
            const dateFin = new Date(document.getElementById('date_fin').value);
            
            if (dateFin <= dateDebut) {
                e.preventDefault();
                alert('La date de fin doit être postérieure à la date de début.');
                return false;
            }
            
            // Vérifier que la période n'est pas trop longue (max 5 ans)
            const diffTime = Math.abs(dateFin - dateDebut);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const maxDays = 5 * 365; // 5 ans
            
            if (diffDays > maxDays) {
                e.preventDefault();
                alert('La période ne peut pas dépasser 5 ans.');
                return false;
            }
        });
        
        // Initialiser Feather Icons pour les nouveaux éléments
        feather.replace();
    </script>
</body>
</html>
