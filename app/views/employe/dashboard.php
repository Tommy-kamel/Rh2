<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - Employé</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .spinning {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
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
                        <a href="#" class="action-card" data-bs-toggle="modal" data-bs-target="#demandeCongeModal">
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
                
                <!-- Mes derniers pointages -->
                
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

        // Gestion du modal de demande de congé
        document.getElementById('demandeCongeModal').addEventListener('shown.bs.modal', function () {
            // Recharger les icônes Feather dans le modal
            feather.replace();
        });

        // Validation côté client avant soumission
        document.getElementById('demandeCongeForm').addEventListener('submit', function(e) {
            const typeConge = document.getElementById('type_conge').value;
            const duree = document.getElementById('duree').value;
            const dateDebut = document.getElementById('date_debut').value;

            if (!typeConge || !duree || !dateDebut) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }

            // Le formulaire sera soumis normalement (pas d'AJAX)
            return true;
        });
    </script>

    <!-- Modal Demande de Congé -->
    <div class="modal fade" id="demandeCongeModal" tabindex="-1" aria-labelledby="demandeCongeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demandeCongeModalLabel">
                        <i data-feather="calendar" class="me-2"></i>
                        Demander un congé
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form id="demandeCongeForm" method="POST" action="/employe/conges/demander">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type_conge" class="form-label">Type de congé</label>
                            <select class="form-select" id="type_conge" name="type_conge" required>
                                <option value="">Choisir un type de congé</option>
                                <?php if (!empty($type_conges)): ?>
                                    <?php foreach ($type_conges as $type): ?>
                                        <option value="<?= $type['id_type_conge'] ?>">
                                            <?= htmlspecialchars($type['type']) ?>
                                            <?php if ($type['pourcentage_salaire'] < 100): ?>
                                                (<?= $type['pourcentage_salaire'] ?>% du salaire)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="duree" class="form-label">Durée (en jours)</label>
                            <input type="number" class="form-control" id="duree" name="duree" min="1" max="30" required>
                            <div class="form-text">Nombre de jours de congé demandé</div>
                        </div>
                        <div class="mb-3">
                            <label for="date_debut" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                            <div class="form-text">Date à laquelle commence le congé</div>
                        </div>
                        <div class="mb-3">
                            <label for="raison" class="form-label">Raison (optionnel)</label>
                            <textarea class="form-control" id="raison" name="raison" rows="3" placeholder="Expliquez brièvement la raison de votre demande de congé..."></textarea>
                            <div class="form-text">Champ optionnel - maximum 500 caractères</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="send" class="me-2"></i>
                            Soumettre la demande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../chatbot.php'; ?>

    <script>
        // Gestion des notifications du navigateur
        let lastNotificationId = 0;

        // Demander la permission pour les notifications
        function requestNotificationPermission() {
            if ('Notification' in window) {
                Notification.requestPermission().then(function(permission) {
                    if (permission === 'granted') {
                        console.log('Permission pour les notifications accordée');
                    } else {
                        console.log('Permission pour les notifications refusée');
                    }
                });
            }
        }

        // Vérifier les nouvelles notifications
        function checkNotifications() {
            fetch('/employe/notifications/latest')
                .then(response => response.json())
                .then(data => {
                    if (data.notification && data.notification.id_notification !== lastNotificationId) {
                        const notification = data.notification;
                        showNotification(notification.titre, notification.message);
                        lastNotificationId = notification.id_notification;
                        // Marquer comme lue après affichage
                        markAsRead(notification.id_notification);
                    }
                })
                .catch(error => console.error('Erreur lors de la vérification des notifications:', error));
        }

        // Marquer une notification comme lue
        function markAsRead(id_notification) {
            fetch('/employe/notifications/mark-read/' + id_notification, {
                method: 'POST'
            }).catch(error => console.error('Erreur lors du marquage comme lu:', error));
        }

        // Afficher une notification du navigateur
        function showNotification(title, body) {
            if (Notification.permission === 'granted') {
                // Utiliser une icône Feather (bell) en SVG data URL
                const bellIcon = 'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#4c6ef5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                `);
                
                new Notification(title, {
                    body: body,
                    icon: bellIcon,
                    tag: 'rh-notification' // Pour éviter les doublons
                });
            }
        }

        // Initialiser au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            requestNotificationPermission();
            checkNotifications(); // Vérifier immédiatement
            setInterval(checkNotifications, 30000); // Vérifier toutes les 30 secondes
        });
    </script>

</body>
</html>
