<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intégration avec la Paie - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .section { 
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
            border: 1px solid #e9ecef;
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .period-selector {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .export-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .badge-heures {
            background-color: #17a2b8;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-absences {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="dollar-sign"></i>
                    Intégration avec la Paie
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Sélection de la période -->
                <div class="period-selector">
                    <h2 class="section-title">
                        <i data-feather="calendar"></i>
                        Sélection de la période
                    </h2>
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label for="mois" class="form-label">Mois</label>
                            <select class="form-select" id="mois" name="mois">
                                <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>" <?= $moisSelectionne == sprintf('%02d', $i) ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0,0,0,$i,1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="annee" class="form-label">Année</label>
                            <select class="form-select" id="annee" name="annee">
                                <?php for($i=date('Y')-1; $i<=date('Y')+1; $i++): ?>
                                    <option value="<?= $i ?>" <?= $anneeSelectionnee == $i ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="filter"></i> Afficher les données
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Récapitulatif global -->
                <div class="section">
                    <h2 class="section-title">
                        <i data-feather="bar-chart-2"></i>
                        Récapitulatif global
                    </h2>
                    <?php
                    $totalEmployes = count($donneesPaie);
                    $totalHeures = array_sum(array_column($donneesPaie, 'total_minutes_travaillees'));
                    $totalHeuresSup = array_sum(array_column($donneesPaie, 'total_minutes_supp'));
                    $totalAbsences = array_sum(array_column($donneesPaie, 'total_absences'));
                    
                    // Fonction de formatage locale
                    function formaterDuree($minutes) {
                        if ($minutes < 60) return "{$minutes} min";
                        
                        $heures = floor($minutes / 60);
                        $minutes_restantes = $minutes % 60;
                        
                        return $minutes_restantes > 0 ? "{$heures}h {$minutes_restantes}min" : "{$heures}h";
                    }
                    ?>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number text-primary"><?= $totalEmployes ?></div>
                            <div class="stat-label">Employés</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-success"><?= formaterDuree($totalHeures) ?></div>
                            <div class="stat-label">Heures travaillées</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-warning"><?= formaterDuree($totalHeuresSup) ?></div>
                            <div class="stat-label">Heures supplémentaires</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-danger"><?= $totalAbsences ?></div>
                            <div class="stat-label">Absences</div>
                        </div>
                    </div>
                </div>

                <!-- Données détaillées par employé -->
                <div class="section">
                    <h2 class="section-title">
                        <i data-feather="users"></i>
                        Données par employé
                    </h2>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Employé</th>
                                    <th>Poste</th>
                                    <th>Heures travaillées</th>
                                    <th>Heures supplémentaires</th>
                                    <th>Retards</th>
                                    <th>Absences</th>
                                    <th>Salaire base</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donneesPaie as $employe): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($employe['poste']) ?></td>
                                    <td>
                                        <span class="badge-heures"><?= formaterDuree($employe['total_minutes_travaillees']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?= formaterDuree($employe['total_minutes_supp']) ?></span>
                                    </td>
                                    <td><?= formaterDuree($employe['total_minutes_retard']) ?></td>
                                    <td>
                                        <span class="badge-absences"><?= $employe['total_absences'] ?> jour(s)</span>
                                    </td>
                                    <td>
                                        <strong><?= number_format($employe['salaire'], 2, ',', ' ') ?> €</strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Actions d'export -->
                <div class="section">
                    <h2 class="section-title">
                        <i data-feather="download"></i>
                        Export vers la paie
                    </h2>
                    
                    <div class="export-actions">
                        <a href="/paie/export?mois=<?= $moisSelectionne ?>&annee=<?= $anneeSelectionnee ?>" 
                           class="btn btn-success">
                            <i data-feather="file-text"></i> Exporter en CSV
                        </a>
                        
                        <button class="btn btn-primary" onclick="alert('Fonctionnalité à implémenter')">
                            <i data-feather="link"></i> Transmettre au système de paie
                        </button>
                        
                        <a href="/paie/fiche" class="btn btn-outline-primary">
                            <i data-feather="user"></i> Voir les fiches individuelles
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
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
</body>
</html>