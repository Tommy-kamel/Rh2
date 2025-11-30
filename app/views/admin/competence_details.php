<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la compétence</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }
        
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
            cursor: pointer;
            position: relative;
        }

        .table th:hover {
            background-color: #e9ecef;
        }

        .table th .sort-icon {
            margin-left: 5px;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .table th:hover .sort-icon {
            opacity: 1;
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

        .search-container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-box {
            flex: 1;
            max-width: 400px;
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .search-box {
                max-width: none;
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
                    Détails de la compétence
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge badge-primary">Ressources Humaines</span>
                </div>
            </header>

            <div class="content">
                <h2>Détails de la compétence : <?= $competence['nom_competence'] ?></h2>
                <p><?= $competence['description'] ?></p>

                <!-- Barre de recherche et bouton retour -->
                <div class="search-container">
                    <a href="/competences/cartographie" class="btn btn-outline-secondary btn-back">
                        <i data-feather="arrow-left"></i> Retour
                    </a>
                    <div class="search-box">
                        <input type="text" id="search" class="form-control" placeholder="Rechercher un employé...">
                    </div>
                </div>

                <!-- Tableau avec l'apparence du premier code -->
                <div class="table-responsive">
                    <table class="table" id="employeesTable">
                        <thead class="table-light">
                            <tr>
                                <th data-sort="employe">
                                    Employé <span class="sort-icon"></span>
                                </th>
                                <th data-sort="email">
                                    Email <span class="sort-icon"></span>
                                </th>
                                <th data-sort="telephone">
                                    Téléphone <span class="sort-icon"></span>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $e): ?>
                                <tr>
                                    <td data-sort-value="<?= strtolower($e['prenom'] . ' ' . $e['nom']) ?>">
                                        <?= $e['prenom'] . ' ' . $e['nom'] ?>
                                    </td>
                                    <td data-sort-value="<?= strtolower($e['email']) ?>">
                                        <?= $e['email'] ?>
                                    </td>
                                    <td data-sort-value="<?= $e['telephone'] ?>">
                                        <?= $e['telephone'] ?>
                                    </td>
                                    <td data-sort-value="<?= strtolower($e['nom_poste']) ?>">
                                        <?= $e['nom_poste'] ?>
                                    </td>
                                    <td data-sort-value="<?= $e['nom_departement'] ?>">
                                        <?= $e['nom_departement'] ?>
                                    </td>
                                    <td data-sort-value="<?= $e['niveau_actuel'] ?>">
                                        <?= $e['niveau_actuel'] ?>
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
                    // Retour à l'ordre original (optionnel)
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
            // Cette fonction pourrait restaurer l'ordre original si nécessaire
            // Pour l'instant, on laisse simplement le tableau dans son état actuel
        }
    </script>

    <script src="/assets/js/rh-dashboard.js"></script>
</body>

</html>