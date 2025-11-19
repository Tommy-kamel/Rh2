<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Employés - Paiement</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .filters-container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .filters-row {
            display: flex;
            align-items: end;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 0 0 auto;
            margin-bottom: 0;
        }

        .filter-group label {
            font-weight: 500;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .filter-control {
            height: 38px;
            font-size: 0.9rem;
        }

        .btn-filter {
            height: 38px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
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
        }

        .table td {
            padding: 10px 8px;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 4px 12px;
            font-size: 0.85rem;
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

        @media (max-width: 768px) {
            .filters-row {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                width: 100%;
            }

            .btn-filter {
                width: 100%;
                justify-content: center;
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
                    <i data-feather="home"></i>
                    Liste des employees
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge badge-primary">Ressources Humaines</span>
                </div>
            </header>

            <div class="content">
                <h2 class="mb-4">Liste des Employés - Paiement</h2>

                <!-- Filtres optimisés -->
                <div class="filters-container">
                    <form action="/paiement/fiche" method="get" class="filters-form">
                        <div class="filters-row">
                            <div class="filter-group">
                                <label for="mois" class="form-label">Mois :</label>
                                <input type="date" name="mois" id="mois" class="form-control filter-control" style="width: 160px;" required>
                            </div>

                            <div class="filter-group">
                                <label for="recherche" class="form-label">Recherche :</label>
                                <input type="text" name="recherche" id="recherche" class="form-control filter-control" style="width: 200px;"
                                    placeholder="Nom, prénom, matricule...">
                            </div>

                            <!-- <div class="filter-group">
                                <button type="submit" class="btn btn-primary btn-filter">
                                    <i data-feather="filter"></i> Appliquer
                                </button>
                            </div> -->
                        </div>
                        <hr style="margin: 50px;">
                        <!-- Tableau -->
                        <div class="table-responsive">
                            <table class="table ">
                                <thead class="table-light">
                                    <tr>
                                        <th>Matricule</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Fonction</th>
                                        <th>Salaire</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employes as $e): ?>
                                        <tr>
                                            <td><?= $e['id_employe'] ?>/TNR</td>
                                            <td><?= htmlspecialchars($e['nom']) ?></td>
                                            <td><?= htmlspecialchars($e['prenom']) ?></td>
                                            <td><?= htmlspecialchars($e['poste']) ?></td>
                                            <td><?= number_format($e['salaire'], 0, ',', ' ') ?></td>
                                            <td>
                                                <button type="submit" name="id_employe" value="<?= $e['id_employe'] ?>" class="btn btn-primary btn-sm">
                                                    Fiche
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
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

        // Recherche en JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('recherche');
            const tableRows = document.querySelectorAll('tbody tr');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    tableRows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let found = false;

                        cells.forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(searchTerm)) {
                                found = true;
                            }
                        });

                        row.style.display = found ? '' : 'none';
                    });
                });
            }
        });
    </script>

    <script src="/assets/js/rh-dashboard.js"></script>
</body>

</html>