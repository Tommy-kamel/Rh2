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
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge badge-primary">Ressources Humaines</span>
                </div>
            </header>
            
            <div class="content-wrapper">
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
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-success" title="Valider" 
                                                            onclick="validerConge(<?= $conge['id_conge'] ?>)">
                                                        <i data-feather="check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Refuser"
                                                            onclick="refuserConge(<?= $conge['id_conge'] ?>)">
                                                        <i data-feather="x"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-info" title="Voir détails"
                                                            onclick="voirDetailsConge(<?= $conge['id_conge'] ?>)">
                                                        <i data-feather="eye"></i>
                                                    </button>
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
