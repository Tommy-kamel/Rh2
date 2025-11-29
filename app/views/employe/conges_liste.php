<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes demandes de congé - Employé</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="list"></i>
                    Mes demandes de congé
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['prenom'] ?? '' ?> <?= $_SESSION['nom'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_poste'] ?? 'Employé' ?></span>
                </div>
            </header>


            <!-- Messages flash -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_message']['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <i data-feather="<?= $_SESSION['flash_message']['type'] === 'success' ? 'check-circle' : 'alert-circle' ?>" class="me-2"></i>
                    <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <div class="content-wrapper">
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-feather="calendar"></i>
                            Historique de mes demandes
                        </h2>
                        <a href="/employe/dashboard" class="btn btn-link">Retour au dashboard</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Date demande</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($demandes)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune demande de congé trouvée.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($demandes as $demande): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($demande['type']) ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($demande['date_demande']))) ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($demande['date_debut']))) ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($demande['date_fin']))) ?></td>
                                            <td><?= htmlspecialchars($demande['duree']) ?> jour(s)</td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $statusText = '';
                                                switch ($demande['status']) {
                                                    case 0:
                                                        $statusClass = 'danger';
                                                        $statusText = 'Refusé';
                                                        break;
                                                    case 1:
                                                        $statusClass = 'warning';
                                                        $statusText = 'En attente';
                                                        break;
                                                    case 11:
                                                        $statusClass = 'info';
                                                        $statusText = 'Validé par chef département';
                                                        break;
                                                    case 21:
                                                        $statusClass = 'success';
                                                        $statusText = 'Approuvé';
                                                        break;
                                                    default:
                                                        $statusClass = 'secondary';
                                                        $statusText = 'Statut inconnu';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        feather.replace();
        
        // Gestion du menu déroulant
        document.querySelectorAll('.has-submenu > .menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const parent = link.parentElement;
                const submenu = parent.querySelector('.submenu');
                // Fermer tous les autres sous-menus
                document.querySelectorAll('.submenu').forEach(sub => {
                    if (sub !== submenu) {
                        sub.classList.remove('show');
                        sub.parentElement.classList.remove('open');
                    }
                });
                // Toggle le sous-menu actuel
                submenu.classList.toggle('show');
                parent.classList.toggle('open');
            });
        });
    </script>

</body>
</html>