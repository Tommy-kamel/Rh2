<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - Employé</title>
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
                    Mon Espace
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['prenom'] ?? '' ?> <?= $_SESSION['nom'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_poste'] ?? 'Employé' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Statistiques rapides -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $solde_conges ?? 25 ?></h3>
                            <p>Jours de congé disponibles</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i data-feather="clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $heures_travaillees ?? 160 ?>h</h3>
                            <p>Heures ce mois</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i data-feather="alert-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $demandes_attente ?? 0 ?></h3>
                            <p>Demandes en attente</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i data-feather="file-text"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $nb_documents ?? 5 ?></h3>
                            <p>Documents disponibles</p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <section class="section">
                    <h2 class="section-title">
                        <i data-feather="zap"></i>
                        Actions rapides
                    </h2>
                    <div class="quick-actions">
                        <a href="/employe/pointage/pointer" class="action-card">
                            <i data-feather="log-in"></i>
                            <h3>Pointer</h3>
                            <p>Enregistrer mon arrivée/départ</p>
                        </a>
                        <a href="/employe/conges/demande" class="action-card">
                            <i data-feather="calendar"></i>
                            <h3>Demander un congé</h3>
                            <p>Faire une nouvelle demande</p>
                        </a>
                        <a href="/employe/bulletins" class="action-card">
                            <i data-feather="dollar-sign"></i>
                            <h3>Bulletins de paie</h3>
                            <p>Consulter mes bulletins</p>
                        </a>
                        <a href="/employe/profil" class="action-card">
                            <i data-feather="user"></i>
                            <h3>Mon profil</h3>
                            <p>Voir mes informations</p>
                        </a>
                    </div>
                </section>
                
                <!-- Mes dernières demandes -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-feather="list"></i>
                            Mes dernières demandes de congé
                        </h2>
                        <a href="/employe/conges/liste" class="btn btn-link">Voir tout</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dernieres_demandes)): ?>
                                    <?php foreach ($dernieres_demandes as $demande): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($demande['type']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($demande['date_debut'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($demande['date_fin'])) ?></td>
                                            <td><?= $demande['duree'] ?> jours</td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                $status_text = '';
                                                switch ($demande['status']) {
                                                    case 1:
                                                        $status_class = 'status-pending';
                                                        $status_text = 'En attente';
                                                        break;
                                                    case 11:
                                                        $status_class = 'status-approved';
                                                        $status_text = 'Validé Chef Dept';
                                                        break;
                                                    case 21:
                                                        $status_class = 'status-approved';
                                                        $status_text = 'Validé RH';
                                                        break;
                                                    default:
                                                        $status_class = 'status-rejected';
                                                        $status_text = 'Refusé';
                                                }
                                                ?>
                                                <span class="status-badge <?= $status_class ?>">
                                                    <?= $status_text ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Aucune demande de congé
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- Mes derniers pointages -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-feather="clock"></i>
                            Mes derniers pointages
                        </h2>
                        <a href="/employe/pointage/historique" class="btn btn-link">Voir tout</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure d'arrivée</th>
                                    <th>Heure de départ</th>
                                    <th>Durée</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($derniers_pointages)): ?>
                                    <?php foreach ($derniers_pointages as $pointage): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($pointage['date_heure_arrive'])) ?></td>
                                            <td><?= date('H:i', strtotime($pointage['date_heure_arrive'])) ?></td>
                                            <td>
                                                <?= $pointage['date_heure_depart'] ? date('H:i', strtotime($pointage['date_heure_depart'])) : '-' ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($pointage['date_heure_depart']) {
                                                    $debut = new DateTime($pointage['date_heure_arrive']);
                                                    $fin = new DateTime($pointage['date_heure_depart']);
                                                    $diff = $debut->diff($fin);
                                                    echo $diff->format('%Hh %Im');
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            Aucun pointage enregistré
                                        </td>
                                    </tr>
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
