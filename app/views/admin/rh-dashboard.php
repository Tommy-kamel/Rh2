<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord RH - Ressources Humaines</title>
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
                    <i data-feather="home"></i>
                    Tableau de bord RH - Vue Globale
                </h1>
                <div style="display: flex; align-items: center; gap: 2rem;">
                    <div class="notification-icon" style="position: relative;">
                        <i data-feather="bell" style="width: 28px; height: 28px; color: #495057; cursor: pointer; stroke-width: 2;"></i>
                        <span class="notification-badge" style="position: absolute; top: -8px; right: -8px; background-color: #dc3545; color: white; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            <?= $conges_attente ?? 0 ?>
                        </span>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                        <span class="user-role badge badge-primary">Ressources Humaines</span>
                    </div>
                </div>
            </header>
            
            <div class="content-wrapper">
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

                <!-- Statistiques globales de tous les départements -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i data-feather="users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $total_employes ?? 0 ?></h3>
                            <p>Total Employés actifs</p>
                            <small class="text-muted">Tous départements confondus</small>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $conges_attente ?? 0 ?></h3>
                            <p>Congés en attente</p>
                            <small class="text-muted">Validation globale</small>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i data-feather="alert-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $absences_aujourd_hui ?? 0 ?></h3>
                            <p>Absences aujourd'hui</p>
                            <small class="text-muted">Tous départements</small>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i data-feather="check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $presents_aujourd_hui ?? 0 ?></h3>
                            <p>Présents aujourd'hui</p>
                            <small class="text-muted">Pointage effectué</small>
                        </div>
                    </div>
                </div>
                
                <!-- Demandes de congés en attente - Tous départements -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-feather="calendar"></i>
                            Demandes de congés en attente - Tous départements
                        </h2>
                        <div class="section-actions">
                            <input type="text" id="searchTable" class="form-control" placeholder="Rechercher..." style="width: 250px; display: inline-block; margin-right: 10px;">
                            <a href="/rh/conges/attente" class="btn btn-link">Voir tout</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="congesTable">
                            <thead>
                                <tr>
                                    <th>Département</th>
                                    <th>Employé</th>
                                    <th>Type</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Durée</th>
                                    <th>Date demande</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($conges_en_attente)): ?>
                                    <?php foreach ($conges_en_attente as $conge): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-info"><?= htmlspecialchars($conge['nom_departement']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($conge['nom'] . ' ' . $conge['prenom']) ?></td>
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
                                                <div style="display: flex; gap: 8px; align-items: center;">
                                                    <form method="POST" action="/admin/conges/valider" style="display: inline;">
                                                        <input type="hidden" name="id_conge" value="<?= $conge['id_conge'] ?>">
                                                        <button type="submit" title="Valider" style="color: #28a745; text-decoration: none; background: none; border: none; cursor: pointer; padding: 0;">
                                                            <i data-feather="check-circle" style="width: 20px; height: 20px;"></i>
                                                        </button>
                                                    </form>
                                                    <a href="/rh/conges/refuser/<?= $conge['id_conge'] ?>" 
                                                       title="Refuser" style="color: #dc3545; text-decoration: none;">
                                                        <i data-feather="x-circle" style="width: 20px; height: 20px;"></i>
                                                    </a>
                                                    <a href="/rh/conges/details/<?= $conge['id_conge'] ?>" 
                                                       title="Voir détails" style="color: #17a2b8; text-decoration: none;">
                                                        <i data-feather="eye" style="width: 20px; height: 20px;"></i>
                                                    </a>
                                                    <a href="/rh/conges/modifier/<?= $conge['id_conge'] ?>" 
                                                       title="Modifier" style="color: #ffc107; text-decoration: none;">
                                                        <i data-feather="edit" style="width: 20px; height: 20px;"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            Aucune demande en attente
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            <span>Affichage <span id="currentStart">1</span> à <span id="currentEnd">10</span> sur <span id="totalRows">0</span> entrées</span>
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
