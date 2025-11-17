<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Présence - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .table-responsive {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
        
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #2c3e50;
            padding: 15px 12px;
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-color: #f1f3f4;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .statut-present { 
            color: #28a745; 
            font-weight: 600;
            background: #f8fff9;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
        }
        
        .statut-absent { 
            color: #dc3545; 
            font-weight: 600;
            background: #fff8f8;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
        }
        
        .statut-non-pointe { 
            color: #ffc107; 
            font-weight: 600;
            background: #fffbf0;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
        }
        
        .heures-sup { 
            color: #007bff; 
            font-weight: 600;
            background: #f0f8ff;
            padding: 6px 10px;
            border-radius: 6px;
            display: inline-block;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            padding: 20px 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            text-align: center;
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-present { color: #28a745; }
        .stat-absent { color: #dc3545; }
        .stat-non-pointe { color: #ffc107; }
        .stat-heures-sup { color: #007bff; }
        
        .date-filter {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }
        
        .badge-retard {
            background: linear-gradient(135deg, #fd7e14, #e55a00);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .page-title-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .page-title-section h2 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .employe-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 8px 12px;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.1);
        }
        
        .btn-primary {
            background: #007bff;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .table-responsive {
                font-size: 0.9rem;
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
                    <i data-feather="file-text"></i>
                    Relevé de Présence
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Filtre par date -->
                <div class="date-filter">
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="date" class="col-form-label"><strong>Date :</strong></label>
                        </div>
                        <div class="col-auto">
                            <input type="date" class="form-control" id="date" name="date" value="<?= $date_selectionnee ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="filter"></i> Générer le relevé
                            </button>
                        </div>
                    </form>
                </div>

                <div class="page-title-section">
                    <h2>Relevé du <?= date('d/m/Y', strtotime($date_selectionnee)) ?></h2>
                </div>

                <!-- Statistiques -->
                <?php
                $total = count($releve);
                $presents = count(array_filter($releve, fn($l) => $l['statut'] === 'Présent'));
                $absents = count(array_filter($releve, fn($l) => $l['statut'] === 'Absent'));
                $nonPointes = count(array_filter($releve, fn($l) => $l['statut'] === 'Non pointé'));
                $avecHeuresSup = count(array_filter($releve, fn($l) => $l['heures_sup'] !== '—'));
                ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total ?></div>
                        <div class="text-muted">Total employés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number stat-present"><?= $presents ?></div>
                        <div class="text-muted">Présents</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number stat-absent"><?= $absents ?></div>
                        <div class="text-muted">Absents</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number stat-non-pointe"><?= $nonPointes ?></div>
                        <div class="text-muted">Non pointés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number stat-heures-sup"><?= $avecHeuresSup ?></div>
                        <div class="text-muted">Heures supp</div>
                    </div>
                </div>

                <!-- Tableau des présences -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Employé</th>
                                <th>Date</th>
                                <th>Arrivée</th>
                                <th>Départ</th>
                                <th>Durée</th>
                                <th>Retard</th>
                                <th>Heures Supp</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($releve as $ligne): ?>
                                <tr>
                                    <td><span class="employe-name"><?= htmlspecialchars($ligne['employe']) ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($ligne['date'])) ?></td>
                                    <td><?= $ligne['arrivee'] ?></td>
                                    <td><?= $ligne['depart'] ?></td>
                                    <td><?= $ligne['duree'] ?></td>
                                    <td>
                                        <?php if ($ligne['retard'] !== '—' && $ligne['retard'] !== '0 min'): ?>
                                            <span class="badge-retard"><?= $ligne['retard'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($ligne['heures_sup'] !== '—'): ?>
                                            <span class="heures-sup"><?= $ligne['heures_sup'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="
                                            <?php if ($ligne['statut'] === 'Présent'): ?>statut-present
                                            <?php elseif ($ligne['statut'] === 'Absent'): ?>statut-absent
                                            <?php else: ?>statut-non-pointe
                                            <?php endif; ?>
                                        ">
                                            <?= $ligne['statut'] ?>
                                        </span>
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