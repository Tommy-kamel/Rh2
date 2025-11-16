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

</body>
</html>
