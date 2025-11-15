<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="home"></i>
                    Tableau de bord
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
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

                <!-- Statistiques principales -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i data-feather="users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $total_employes ?? 0 ?></h3>
                            <p>Employés actifs</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $conges_attente ?? 0 ?></h3>
                            <p>Congés en attente</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i data-feather="alert-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $absences_aujourd_hui ?? 0 ?></h3>
                            <p>Absences aujourd'hui</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i data-feather="check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $presents_aujourd_hui ?? 0 ?></h3>
                            <p>Présents aujourd'hui</p>
                        </div>
                    </div>
                </div>
                
                <!-- Demandes de congés en attente -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-feather="calendar"></i>
                            Demandes de congés en attente
                        </h2>
                        <a href="/conges/attente" class="btn btn-link">Voir tout</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($conges_en_attente)): ?>
                                    <?php foreach ($conges_en_attente as $conge): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info text-dark"><?= htmlspecialchars($conge['nom_departement']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($conge['nom'] . ' ' . $conge['prenom']) ?></td>
                                            <td><?= htmlspecialchars($conge['nom_poste'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-primary text-white"><?= htmlspecialchars($conge['type'] ?? 'N/A') ?></span>
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
                                                        <button type="submit" title="Valider" onclick="return confirm('Confirmer la validation de cette demande de congé ?')" style="color: #28a745; text-decoration: none; background: none; border: none; cursor: pointer; padding: 0;">
                                                            <i data-feather="check-circle" style="width: 20px; height: 20px;"></i>
                                                        </button>
                                                    </form>
                                                    <a href="/admin/conges/refuser/<?= $conge['id_conge'] ?>" 
                                                       title="Refuser" onclick="return confirm('Confirmer le refus de cette demande de congé ?')" style="color: #dc3545; text-decoration: none;">
                                                        <i data-feather="x-circle" style="width: 20px; height: 20px;"></i>
                                                    </a>
                                                    <a href="/admin/conges/details/<?= $conge['id_conge'] ?>" 
                                                       title="Voir détails" style="color: #17a2b8; text-decoration: none;">
                                                        <i data-feather="eye" style="width: 20px; height: 20px;"></i>
                                                    </a>
                                                    <a href="/admin/conges/modifier/<?= $conge['id_conge'] ?>" 
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
                </section>
                
                <!-- Répartition par département -->
                <!-- <section class="section">
                    <h2 class="section-title">
                        <i data-feather="pie-chart"></i>
                        Répartition par département
                    </h2>
                    <div class="departments-grid">
                        <?php 
                        $departments = [
                            ['name' => 'Ressources Humaines', 'count' => 12, 'color' => 'blue'],
                            ['name' => 'Production', 'count' => 45, 'color' => 'green'],
                            ['name' => 'Achat et vente', 'count' => 18, 'color' => 'orange'],
                            ['name' => 'Gestion de stock', 'count' => 8, 'color' => 'purple'],
                            ['name' => 'Gestion d\'immobilisation', 'count' => 6, 'color' => 'red']
                        ];
                        foreach ($departments as $dept): 
                        ?>
                            <div class="department-card">
                                <div class="department-header">
                                    <h3><?= $dept['name'] ?></h3>
                                    <span class="department-count <?= $dept['color'] ?>"><?= $dept['count'] ?></span>
                                </div>
                                <div class="department-progress">
                                    <div class="progress-bar <?= $dept['color'] ?>" style="width: <?= ($dept['count']/50)*100 ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section> -->
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
</body>
</html>
