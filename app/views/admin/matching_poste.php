<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matching des postes</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #4895ef;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --success-color: #4ade80;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --text-primary: #334155;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --card-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #f8fafc 100%);
            color: var(--text-primary);
            line-height: 1.7;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            margin-left: 250px;
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding: 30px 0;
            border-bottom: 2px solid var(--border-color);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 0;
            font-weight: 700;
            font-size: 32px;
            color: var(--dark-color);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-title i {
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: end;
            gap: 8px;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .user-role {
            font-size: 0.85rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light)) !important;
            border: none;
            padding: 6px 16px;
            border-radius: 20px;
        }

        .content {
            padding: 0;
        }

        h2 {
            margin-bottom: 30px;
            color: var(--dark-color);
            font-weight: 700;
            font-size: 28px;
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            /* border-left: 6px solid var(--primary-color); */
        }

        /* Section informations du poste */
        .post-info-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 35px;
            margin-bottom: 40px;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .post-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        }

        .post-title {
            font-weight: 700;
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .competences-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .competence-card {
            background: var(--light-color);
            border-radius: 12px;
            padding: 20px;
            /* border-left: 4px solid var(--primary-color); */
            transition: all 0.3s ease;
        }

        .competence-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.15);
        }

        .competence-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .competence-level {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Recherche */
        .search-container {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .search-box {
            width: 100%;
            max-width: 500px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid var(--border-color);
            padding: 14px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--light-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            background: white;
        }

        /* Tableau des candidats */
        .table-responsive {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 50px;
            border: 1px solid var(--border-color);
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 20px 16px;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .table th:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
        }

        .table th .sort-icon {
            margin-left: 8px;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .table th:hover .sort-icon {
            opacity: 1;
            transform: scale(1.2);
        }

        .table th.sorted-asc .sort-icon,
        .table th.sorted-desc .sort-icon {
            opacity: 1;
        }

        .table th.sorted-asc .sort-icon::after {
            content: "↑";
        }

        .table th.sorted-desc .sort-icon::after {
            content: "↓";
        }

        .table td {
            padding: 18px 16px;
            vertical-align: middle;
            font-size: 0.95rem;
            border-color: var(--border-color);
            transition: all 0.3s ease;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, #f0f7ff 0%, #f8faff 100%);
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1);
        }

        /* Styles spécifiques pour les colonnes */
        .employee-name {
            font-weight: 600;
            color: var(--primary-color);
        }

        .employee-name a {
            color: inherit;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .employee-name a:hover {
            color: var(--secondary-color);
            transform: translateX(5px);
        }

        .score-cell {
            font-weight: 700;
            font-size: 1.1rem;
            text-align: center;
        }

        .score-high {
            color: var(--success-color);
        }

        .score-medium {
            color: var(--warning-color);
        }

        .score-low {
            color: var(--danger-color);
        }

        .gaps-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .gap-item {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            /* border-left: 3px solid var(--danger-color); */
        }

        .no-gaps {
            background: rgba(74, 222, 128, 0.1);
            color: var(--success-color);
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            /* border-left: 3px solid var(--success-color); */
        }

        /* Badges pour département et poste */
        .department-badge {
            background: linear-gradient(135deg, #8b5cf6, #a855f7);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .position-badge {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                padding: 30px;
                margin-left: 0;
            }
            
            .competences-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }
            
            .main-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .user-info {
                align-items: center;
            }
            
            .post-info-card {
                padding: 25px;
            }
            
            .table-responsive {
                margin-bottom: 30px;
            }
            
            .table th,
            .table td {
                padding: 12px 8px;
            }
        }

        /* Animation pour les scores */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .score-cell {
            animation: pulse 2s ease-in-out infinite;
        }

        /* Effet de surbrillance pour la ligne sélectionnée */
        .selected-row {
            background: linear-gradient(135deg, #e0f2fe, #f0f9ff) !important;
            /* border-left: 6px solid var(--primary-color); */
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
        }
    </style>
</head>

<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="users"></i>
                    Matching des postes
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge">Ressources Humaines</span>
                </div>
            </header>

            <div class="content">
                <h2>Matching pour le poste : <?= htmlspecialchars($poste['nom'] ?? '—') ?></h2>

                <!-- Section informations du poste -->
                <div class="post-info-card">
                    <div class="post-title">
                        <i data-feather="briefcase" width="28" height="28"></i>
                        Compétences requises pour ce poste
                    </div>
                    <div class="competences-grid">
                        <?php foreach ($required as $r): ?>
                        <div class="competence-card">
                            <div class="competence-name"><?= htmlspecialchars($r['nom_competence']) ?></div>
                            <span class="competence-level">Niveau <?= (int)$r['niveau_requis'] ?> requis</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recherche -->
                <div class="search-container">
                    <div class="search-box">
                            <a href="/matching/postes">retour</a>
                        <input type="text" id="searchCandidate" class="form-control" 
                               placeholder="🔍 Rechercher un employé (nom, poste, département)...">
                    </div>
                </div>

                <!-- Tableau des candidats -->
                <div class="table-responsive">
                    <table class="table" id="candidatesTable">
                        <thead>
                            <tr>
                                <th data-sort="employe">Employé <span class="sort-icon"></span></th>
                                <th data-sort="poste">Poste <span class="sort-icon"></span></th>
                                <th data-sort="departement">Département <span class="sort-icon"></span></th>
                                <th data-sort="score">Score <span class="sort-icon"></span></th>
                                <th>Gaps de compétences</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $idx => $r): 
                                $scoreClass = '';
                                if ($r['score'] >= 80) $scoreClass = 'score-high';
                                elseif ($r['score'] >= 60) $scoreClass = 'score-medium';
                                else $scoreClass = 'score-low';
                            ?>
                            <tr data-idx="<?= $idx ?>">
                                <td>
                                    <div class="employee-name">
                                        <a href="/formations/employee/<?= $r['employe']['id_employe'] ?>?id_poste=<?= $poste['id_poste'] ?>">
                                            <i data-feather="user" width="16" height="16"></i>
                                            <?= htmlspecialchars($r['employe']['prenom'].' '.$r['employe']['nom']) ?>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="position-badge">
                                        <?= htmlspecialchars($r['employe']['nom_poste'] ?? 'Non défini') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="department-badge">
                                        <?= htmlspecialchars($r['employe']['nom_departement'] ?? 'Non défini') ?>
                                    </span>
                                </td>
                                <td class="score-cell <?= $scoreClass ?>">
                                    <?= $r['score'] ?>%
                                </td>
                                <td>
                                    <?php if (count($r['gaps']) === 0): ?>
                                        <div class="no-gaps">
                                            <i data-feather="check-circle" width="14" height="14"></i>
                                            Aucun gap
                                        </div>
                                    <?php else: ?>
                                        <div class="gaps-list">
                                            <?php foreach ($r['gaps'] as $g): ?>
                                                <div class="gap-item">
                                                    <i data-feather="alert-triangle" width="12" height="12"></i>
                                                    <?= htmlspecialchars($g['nom_competence']) ?> (manque <?= $g['manque'] ?> niveau)
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
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

        // Tri des colonnes
        function setupTableSorting() {
            document.querySelectorAll('#candidatesTable th[data-sort]').forEach(header => {
                header.addEventListener('click', () => {
                    const table = header.closest('table');
                    const tbody = table.querySelector('tbody');
                    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
                    const sortKey = header.getAttribute('data-sort');
                    
                    // Réinitialiser les autres en-têtes
                    document.querySelectorAll('#candidatesTable th').forEach(th => {
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
                    }
                });
            });
        }

        function sortTable(tbody, columnIndex, direction, sortKey) {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const cellA = a.cells[columnIndex];
                const cellB = b.cells[columnIndex];
                
                let valueA, valueB;

                if (sortKey === 'score') {
                    // Pour les scores, extraire le nombre du pourcentage
                    valueA = parseFloat(cellA.textContent) || 0;
                    valueB = parseFloat(cellB.textContent) || 0;
                } else {
                    // Pour les autres colonnes, utiliser le texte
                    valueA = cellA.textContent.trim().toLowerCase();
                    valueB = cellB.textContent.trim().toLowerCase();
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

        // Initialiser le tri
        setupTableSorting();

        // Recherche en temps réel
        document.getElementById('searchCandidate').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#candidatesTable tbody tr').forEach(tr => {
                const txt = tr.textContent.toLowerCase();
                tr.style.display = txt.includes(q) ? '' : 'none';
            });
        });

        // Sélection de ligne au clic
        document.querySelectorAll('#candidatesTable tbody tr').forEach(tr => {
            tr.addEventListener('click', function() {
                // Retirer la sélection précédente
                document.querySelectorAll('#candidatesTable tbody tr').forEach(row => {
                    row.classList.remove('selected-row');
                });
                // Ajouter la sélection à la ligne cliquée
                this.classList.add('selected-row');
            });
        });

        // Animation d'apparition pour les cartes de compétences
        document.querySelectorAll('.competence-card').forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });
    </script>

    <script src="/assets/js/rh-dashboard.js"></script>
</body>
</html>