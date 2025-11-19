<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des congés</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="calendar"></i>
                    Historique des congés
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Bouton retour -->
                <div class="mb-4">
                    <a href="<?= ($_SESSION['id_departement'] ?? null) === 1 ? '/rh/dashboard' : '/dashboard' ?>" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i>
                        Retour au tableau de bord
                    </a>
                </div>

                <!-- Messages de succès/erreur -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i data-feather="check-circle"></i>
                        <?= htmlspecialchars($success_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i data-feather="alert-circle"></i>
                        <?= htmlspecialchars($error_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filtres et recherche -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-feather="filter"></i>
                            Filtres
                        </h2>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="searchTable" class="form-label">Rechercher</label>
                            <input type="text" id="searchTable" class="form-control" placeholder="Nom, département, poste...">
                        </div>
                        <div class="col-md-3">
                            <label for="filterDepartement" class="form-label">Département</label>
                            <select id="filterDepartement" class="form-select">
                                <option value="">Tous les départements</option>
                                <?php if (!empty($historique_conges)): ?>
                                    <?php 
                                    $departements = array_unique(array_column($historique_conges, 'nom_departement'));
                                    foreach ($departements as $dept): 
                                    ?>
                                        <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterType" class="form-label">Type de congé</label>
                            <select id="filterType" class="form-select">
                                <option value="">Tous les types</option>
                                <?php if (!empty($historique_conges)): ?>
                                    <?php 
                                    $types = array_unique(array_column($historique_conges, 'type'));
                                    foreach ($types as $type): 
                                    ?>
                                        <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button id="btnResetFilters" class="btn btn-secondary w-100">
                                <i data-feather="refresh-cw"></i>
                                Réinitialiser
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Liste des congés historiques -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-feather="list"></i>
                            Liste complète
                        </h2>
                        <div class="section-actions">
                            <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                <span id="totalRows">0</span> congé(s)
                            </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="congesTable">
                            <thead>
                                <tr>
                                    <th>Département</th>
                                    <th>Employé</th>
                                    <th>Poste</th>
                                    <th>Type</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Durée</th>
                                    <th>Date demande</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($historique_conges)): ?>
                                    <?php foreach ($historique_conges as $conge): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-info"><?= htmlspecialchars($conge['nom_departement']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($conge['nom'] . ' ' . $conge['prenom']) ?></td>
                                            <td><?= htmlspecialchars($conge['nom_poste'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge badge-primary"><?= htmlspecialchars($conge['type']) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($conge['date_debut'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($conge['date_fin'])) ?></td>
                                            <td>
                                                <?php 
                                                $date_debut = new DateTime($conge['date_debut']);
                                                $date_fin = new DateTime($conge['date_fin']);
                                                $duree = $date_debut->diff($date_fin)->days + 1;
                                                echo $duree . ' jour' . ($duree > 1 ? 's' : '');
                                                ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($conge['date_demande'])) ?></td>
                                            <td>
                                                <?php
                                                $status = $conge['status'];
                                                $statusText = '';
                                                $statusClass = '';
                                                switch ($status) {
                                                    case 21:
                                                        $statusText = 'Validé';
                                                        $statusClass = 'badge-success';
                                                        break;
                                                    case 31:
                                                        $statusText = 'Refusé';
                                                        $statusClass = 'badge-danger';
                                                        break;
                                                    default:
                                                        $statusText = 'Inconnu';
                                                        $statusClass = 'badge-secondary';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            Aucun congé dans l'historique
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            <span>Affichage <span id="currentStart">1</span> à <span id="currentEnd">10</span> sur <span id="totalRowsInfo">0</span> entrées</span>
                        </div>
                        <div class="pagination" id="pagination">
                            <!-- Pagination buttons will be generated by JavaScript -->
                        </div>
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
                // Fermer tous les autres sous-menus
                document.querySelectorAll('.has-submenu').forEach(item => {
                    if (item !== parent) {
                        item.classList.remove('open');
                    }
                });
                // Toggle le sous-menu actuel
                parent.classList.toggle('open');
            });
        });
    </script>
    <script src="/assets/js/conges-attente.js"></script>
</body>
</html>
