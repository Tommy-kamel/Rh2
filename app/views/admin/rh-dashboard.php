<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord RH - Ressources Humaines</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="/assets/js/rh-dashboard.js"></script>
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
                    <div class="notification-icon" style="position: relative;" onclick="toggleNotifications()">
                        <i data-feather="bell" style="width: 28px; height: 28px; color: #495057; cursor: pointer; stroke-width: 2;"></i>
                        <?php
                        $total_notifications = ($conges_attente ?? 0) + count($contrats_expirant ?? []);
                        if ($total_notifications > 0):
                        ?>
                        <span class="notification-badge" style="position: absolute; top: -8px; right: -8px; background-color: #dc3545; color: white; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            <?= $total_notifications ?>
                        </span>
                        <?php endif; ?>
                        
                        <!-- Dropdown des notifications -->
                        <div id="notifications-dropdown" class="notifications-dropdown" style="display: none;">
                            <div class="notifications-header">
                                <h6>Notifications</h6>
                            </div>
                            <div class="notifications-body">
                                <?php if (($conges_attente ?? 0) > 0): ?>
                                <div class="notification-item">
                                    <i data-feather="calendar" class="notification-icon-small"></i>
                                    <div class="notification-content">
                                        <div class="notification-title">Congés en attente</div>
                                        <div class="notification-text">Vous avez <?= $conges_attente ?> demande(s) de congé en attente de validation</div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($contrats_expirant)): ?>
                                <div class="notification-item">
                                    <i data-feather="alert-triangle" class="notification-icon-small"></i>
                                    <div class="notification-content">
                                        <div class="notification-title">Contrats expirant bientôt</div>
                                        <div class="notification-text">Vous avez <?= count($contrats_expirant) ?> contrat(s) qui se terminent dans moins d'un mois</div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($total_notifications == 0): ?>
                                <div class="notification-item">
                                    <i data-feather="check-circle" class="notification-icon-small"></i>
                                    <div class="notification-content">
                                        <div class="notification-title">Tout est à jour</div>
                                        <div class="notification-text">Aucune notification en attente</div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
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
                
                <!-- Accès rapides aux fonctionnalités RH -->
                <section class="section">
                    <h2 class="section-title">
                        <i data-feather="zap"></i>
                        Accès rapides
                    </h2>
                    <div class="quick-access-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                        <!-- Statistiques -->
                        <a href="/statistiques" class="access-card" style="text-decoration: none; color: inherit;">
                            <div class="card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i data-feather="bar-chart-2"></i>
                            </div>
                            <h3>Statistiques</h3>
                            <p>Voir les statistiques globales</p>
                        </a>
                        
                        <!-- Gestion des employés -->
                        <a href="/employes" class="access-card" style="text-decoration: none; color: inherit;">
                            <div class="card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i data-feather="users"></i>
                            </div>
                            <h3>Liste des employés</h3>
                            <p>Gérer les employés</p>
                        </a>
                        
                        <!-- Demandes de congés en attente -->
                        <a href="/rh/conges/attente" class="access-card" style="text-decoration: none; color: inherit;">
                            <div class="card-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                                <i data-feather="calendar"></i>
                            </div>
                            <h3>Demandes en attente</h3>
                            <p>Traiter les demandes de congés</p>
                        </a>
                        
                        <!-- Pointages Arrivée -->
                        <a href="/pointage/arrivee" class="access-card" style="text-decoration: none; color: inherit;">
                            <div class="card-icon" style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);">
                                <i data-feather="clock"></i>
                            </div>
                            <h3>Pointages Arrivée</h3>
                            <p>Consulter les arrivées</p>
                        </a>
                        
                        <!-- Relevé de présence -->
                        <a href="/releve-presence" class="access-card" style="text-decoration: none; color: inherit;">
                            <div class="card-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                                <i data-feather="alert-circle"></i>
                            </div>
                            <h3>Relevé de présence</h3>
                            <p>Absences et retards</p>
                        </a>
                        
                        <!-- Calcul de paie -->
                        <a href="/paiement/employes" class="access-card" style="text-decoration: none; color: inherit;">
                            <div class="card-icon" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
                                <i data-feather="dollar-sign"></i>
                            </div>
                            <h3>Calcul de paie</h3>
                            <p>Gérer les paies</p>
                        </a>
                    </div>
                </section>
                
                <style>
                    .quick-access-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                        gap: 1.5rem;
                        margin-top: 1.5rem;
                    }
                    
                    .access-card {
                        background: white;
                        border-radius: 12px;
                        padding: 2rem;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                        transition: all 0.3s ease;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        text-align: center;
                    }
                    
                    .access-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                    }
                    
                    .access-card .card-icon {
                        width: 70px;
                        height: 70px;
                        border-radius: 15px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-bottom: 1.25rem;
                    }
                    
                    .access-card .card-icon i {
                        width: 35px;
                        height: 35px;
                        color: white;
                        stroke-width: 2;
                    }
                    
                    .access-card h3 {
                        font-size: 1.125rem;
                        font-weight: 600;
                        color: #1e293b;
                        margin-bottom: 0.5rem;
                    }
                    
                    .access-card p {
                        font-size: 0.875rem;
                        color: #64748b;
                        margin: 0;
                    }
                </style>
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
