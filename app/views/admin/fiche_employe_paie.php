<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Employé - Paie - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
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
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.85rem;
        }
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .employe-info {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
        }
        .employee-selector {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .badge-majoration {
            background-color: #6f42c1;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-duree {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .export-actions {
            display: flex;
            gap: 10px;
            align-items: center;
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
                    Fiche Employé - Données Paie
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Sélection employé et période -->
                <div class="employee-selector">
                    <h2 class="section-title">
                        <i data-feather="search"></i>
                        Sélection employé et période
                    </h2>
                    <form method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Employé</label>
                                <select class="form-select" name="id_employe">
                                    <?php foreach ($employes as $emp): ?>
                                        <option value="<?= $emp['id_employe'] ?>" 
                                            <?= $id_employe_selectionne == $emp['id_employe'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom'] . ' - ' . $emp['poste']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Mois</label>
                                <select class="form-select" name="mois">
                                    <?php for($i=1; $i<=12; $i++): ?>
                                        <option value="<?= sprintf('%02d', $i) ?>" <?= $mois_selectionne == sprintf('%02d', $i) ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0,0,0,$i,1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Année</label>
                                <select class="form-select" name="annee">
                                    <?php for($i=date('Y')-1; $i<=date('Y')+1; $i++): ?>
                                        <option value="<?= $i ?>" <?= $annee_selectionnee == $i ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i data-feather="filter"></i> Afficher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if ($ficheEmploye): ?>
                <?php 
                    function formaterDuree($minutes) {
                        if ($minutes < 60) return "{$minutes} min";
                        $heures = floor($minutes / 60);
                        $minutes_restantes = $minutes % 60;
                        return $minutes_restantes > 0 ? "{$heures}h {$minutes_restantes}min" : "{$heures}h";
                    }
                    
                    $totalHeuresSup = array_sum(array_column($ficheEmploye['heures_supplementaires'], 'nombre_minutes'));
                    $totalRetards = array_sum(array_column($ficheEmploye['retards'], 'duree_retard'));
                ?>
                
                <!-- Informations employé -->
                <div class="employe-info">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="mb-3"><?= htmlspecialchars($ficheEmploye['employe']['prenom'] . ' ' . $ficheEmploye['employe']['nom']) ?></h2>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Poste :</strong><br>
                                    <?= htmlspecialchars($ficheEmploye['employe']['poste']) ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Département :</strong><br>
                                    <?= htmlspecialchars($ficheEmploye['employe']['departement'] ?? 'Non spécifié') ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Salaire base :</strong><br>
                                    <?= number_format($ficheEmploye['employe']['salaire'], 2, ',', ' ') ?> €
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="text-muted">Période</div>
                            <h4><?= date('F Y', mktime(0,0,0,$mois_selectionne,1,$annee_selectionnee)) ?></h4>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif du mois -->
                <div class="section">
                    <h2 class="section-title">
                        <i data-feather="bar-chart-2"></i>
                        Récapitulatif du mois
                    </h2>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number text-primary"><?= formaterDuree($ficheEmploye['heures_travaillees']) ?></div>
                            <div class="stat-label">Heures travaillées</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-warning"><?= formaterDuree($totalHeuresSup) ?></div>
                            <div class="stat-label">Heures supplémentaires</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-danger"><?= formaterDuree($totalRetards) ?></div>
                            <div class="stat-label">Retards</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-info"><?= count($ficheEmploye['absences']) ?></div>
                            <div class="stat-label">Jours d'absence</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-success"><?= count($ficheEmploye['pointages']) ?></div>
                            <div class="stat-label">Jours pointés</div>
                        </div>
                    </div>
                </div>

                <!-- Détail des heures supplémentaires -->
                <?php if (!empty($ficheEmploye['heures_supplementaires'])): ?>
                <div class="section">
                    <h2 class="section-title">
                        <i data-feather="clock"></i>
                        Heures supplémentaires
                    </h2>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Majoration</th>
                                    <th>Durée</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ficheEmploye['heures_supplementaires'] as $hs): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($hs['date_heure_sup'])) ?></td>
                                    <td><?= htmlspecialchars($hs['type']) ?></td>
                                    <td>
                                        <span class="badge-majoration"><?= $hs['pourcentage_majoration'] ?>%</span>
                                    </td>
                                    <td>
                                        <span class="badge-duree"><?= formaterDuree($hs['nombre_minutes']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Pointages du mois -->
                <div class="section">
                    <h2 class="section-title">
                        <i data-feather="calendar"></i>
                        Pointages du mois (<?= count($ficheEmploye['pointages']) ?> jours)
                    </h2>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Durée</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ficheEmploye['pointages'] as $pointage): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($pointage['date_heure_arrive'])) ?></td>
                                    <td><?= date('H:i', strtotime($pointage['date_heure_arrive'])) ?></td>
                                    <td><?= $pointage['date_heure_depart'] ? date('H:i', strtotime($pointage['date_heure_depart'])) : '—' ?></td>
                                    <td>
                                        <?php if ($pointage['duree_minutes'] && $pointage['duree_minutes'] > 0): ?>
                                            <span class="badge-duree"><?= formaterDuree($pointage['duree_minutes']) ?></span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Actions -->
                <div class="section">
                    <h2 class="section-title">
                        <i data-feather="download"></i>
                        Export
                    </h2>
                    
                    <div class="export-actions">
                        <a href="/paie/fiche/export?id_employe=<?= $id_employe_selectionne ?>&mois=<?= $mois_selectionne ?>&annee=<?= $annee_selectionnee ?>" 
                           class="btn btn-success">
                            <i data-feather="file-text"></i> Exporter la fiche en CSV
                        </a>
                        <a href="/paie/integration" class="btn btn-outline-primary">
                            <i data-feather="arrow-left"></i> Retour au relevé global
                        </a>
                    </div>
                </div>

                <?php else: ?>
                <div class="section text-center py-5">
                    <i data-feather="user-x" style="width: 64px; height: 64px; color: #6c757d; margin-bottom: 20px;"></i>
                    <h3>Aucun employé sélectionné</h3>
                    <p class="text-muted">Veuillez sélectionner un employé et une période pour afficher les données.</p>
                </div>
                <?php endif; ?>
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