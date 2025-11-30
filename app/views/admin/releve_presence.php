<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Présence - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .statut-present { color: #28a745; font-weight: bold; }
        .statut-absent { color: #dc3545; font-weight: bold; }
        .statut-non-pointe { color: #ffc107; font-weight: bold; }
        .heures-sup { color: #007bff; font-weight: 500; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-present { color: #28a745; }
        .stat-absent { color: #dc3545; }
        .stat-non-pointe { color: #ffc107; }
        .stat-heures-sup { color: #007bff; }
        .date-filter {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .badge-retard {
            background-color: #fd7e14;
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

                <h2 class="mb-4">Relevé du <?= date('d/m/Y', strtotime($date_selectionnee)) ?></h2>

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
                        <div class="text-muted">Avec heures supp</div>
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
                                    <td><strong><?= htmlspecialchars($ligne['employe']) ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($ligne['date'])) ?></td>
                                    <td><?= $ligne['arrivee'] ?></td>
                                    <td><?= $ligne['depart'] ?></td>
                                    <td><?= $ligne['duree'] ?></td>
                                    <td>
                                        <?php if ($ligne['retard'] !== '—' && $ligne['retard'] !== '0 min'): ?>
                                            <span class="badge-retard"><?= $ligne['retard'] ?></span>
                                        <?php else: ?>
                                            <?= $ligne['retard'] ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="heures-sup"><?= $ligne['heures_sup'] ?></td>
                                    <td class="
                                        <?php if ($ligne['statut'] === 'Présent'): ?>statut-present
                                        <?php elseif ($ligne['statut'] === 'Absent'): ?>statut-absent
                                        <?php else: ?>statut-non-pointe
                                        <?php endif; ?>
                                    ">
                                        <?= $ligne['statut'] ?>
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