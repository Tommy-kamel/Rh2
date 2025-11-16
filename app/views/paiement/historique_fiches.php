<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Historique des Fiches de Paie</title>

    <link rel="stylesheet" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css" />
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        main {
            padding: 20px;
        }

        table {
            font-size: 13px;
        }

        th {
            background: #f8f9fa;
        }

        .filter-box label {
            font-weight: 600;
            margin-right: 6px;
        }

        .filter-box input,
        .filter-box select {
            margin-right: 12px;
        }

        .table-wrapper {
            max-height: 500px;
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #ddd;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            border-bottom: 2px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            z-index: 2;
        }

        .table tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table tbody tr:hover {
            background-color: #e9e9e9;
        }

        .table tbody a {
            display: inline-block;
            padding: 4px 8px;
            text-decoration: none;
            border-radius: 4px;
            background: #6c757d;
            color: #fff;
            font-size: 12px;
        }

        .table tbody a:hover {
            opacity: 0.85;
        }
    </style>
</head>

<body>
    <div class="app-container d-flex">

        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content">
            <h2 class="mb-4">Historique des Fiches de Paie</h2>

            <form method="GET" class="filter-box mb-4 p-3 border rounded bg-light">
                <label>Employé :</label>
                <select name="employe" class="form-select d-inline-block w-auto">
                    <option value="">Tous</option>
                    <?php foreach ($employes as $e): ?>
                        <option value="<?= $e['id_employe'] ?>" <?= $selected_employe == $e['id_employe'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nom_complet']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Mois :</label>
                <input type="month" name="mois" value="<?= $selected_mois ?>" class="form-control d-inline-block w-auto" />

                <label>Recherche :</label>
                <input type="text" name="search" class="form-control d-inline-block w-auto" placeholder="Nom, ID fiche..." value="<?= htmlspecialchars($search) ?>" />

                <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
            </form>

            <div class="card shadow-sm">
                <div class="card-body p-0 table-responsive table-wrapper">
                    <table class="table table-bordered table-striped mb-0 table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ID Fiche</th>
                                <th>Employé</th>
                                <th>Mois</th>
                                <th>Salaire Brut</th>
                                <th>Absence</th>
                                <th>CNaPS</th>
                                <th>Retenue sanitaire</th>
                                <th>Revenue imposable</th>
                                <th>Net à Payer</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($fiches)): ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-3">Aucune fiche enregistrée.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($fiches as $f): ?>
                                    <?php list($mois, $annee) = explode('/', $f['mois_annee']); ?>
                                    <tr>
                                        <td><?= $f['id_fiche_paie'] ?></td>
                                        <td><?= strtoupper($f['nom']) . ' ' . ucfirst($f['prenom']) ?></td>
                                        <td><?= $annee ?>-<?= $mois ?></td>
                                        <td><?= number_format($f['salaire_brut'], 0, ',', ' ') ?>,00</td>
                                        <td><?= number_format($f['absence_mois']) ?></td>
                                        <td><?= number_format($f['cnaps'], 0, ',', ' ') ?>,00</td>
                                        <td><?= number_format($f['retenue_sanitaire'], 0, ',', ' ') ?>,00</td>
                                        <td><?= number_format($f['revenue_imposable'], 0, ',', ' ') ?>,00</td>
                                        <td><?= number_format($f['net_a_payer'], 0, ',', ' ') ?>,00</td>
                                        <td><?= date('d/m/Y', strtotime($f['date_fiche'])) ?></td>
                                        <td>
                                            <a href="/paiement/fiche?mois=<?= $annee ?>-<?= $mois ?>&id_employe=<?= $f['id_employe'] ?>" class="btn btn-secondary btn-sm">Voir</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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

    <script src="/assets/js/rh-dashboard.js"></script>
</body>

</html>