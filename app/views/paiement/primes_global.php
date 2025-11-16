<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Primes - Tous les Employés</title>

    <!-- mêmes CSS que le dashboard RH -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
    <div class="app-container">

        <!-- même sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        <main class="main-content">
            <div class="content p-4">
                <h2 class="mb-4">Liste de toutes les primes</h2>

                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Employé :</label>
                        <select name="employe" class="form-select">
                            <option value="">Tous</option>
                            <?php foreach ($employes as $e): ?>
                                <option value="<?= $e['id_employe'] ?>" <?= $selected_employe == $e['id_employe'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['nom_complet']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Recherche :</label>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control"
                            placeholder="Nom, motif...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tri :</label>
                        <select name="order" class="form-select">
                            <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Récent → Ancien</option>
                            <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Ancien → Récent</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </form>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Motif</th>
                            <th>Montant</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($primes)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucune prime trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($primes as $p): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($p['date_prime'])) ?></td>
                                    <td><?= htmlspecialchars($p['employe_nom']) ?></td>
                                    <td><?= $p['type'] == 'rendement' ? 'Rendement' : 'Divers' ?></td>
                                    <td><?= htmlspecialchars($p['motif']) ?></td>
                                    <td><?= number_format($p['montant_prime'], 0, ',', ' ') ?>,00</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- <a href="/employes" class="btn btn-secondary mt-3">Retour à la liste</a> -->
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

    <script src="/assets/js/rh-dashboard.js"></script>
</body>

</html>