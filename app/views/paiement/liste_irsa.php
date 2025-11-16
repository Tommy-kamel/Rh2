<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Employés - Paiement</title>
    <!-- mêmes CSS -->
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
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="home"></i>
                    Tableau de bord RH - Vue Globale
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge badge-primary">Ressources Humaines</span>
                </div>
            </header>

            <div class="content p-4">
                <h2 class="mb-4">Liste des Employés - Paiement</h2>

                <form action="/paiement/fiche_irsa" method="get" class="mb-4">
                    <label class="form-label">Mois :</label>
                    <input type="date" name="mois" class="form-control w-25" required>

                    <table class="table table-bordered table-striped mt-3">
                        <thead>
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
                                            Voir Irsa
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
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