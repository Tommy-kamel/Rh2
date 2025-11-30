<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des congés</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/calendrier.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="calendar"></i>
                    Calendrier des congés
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

                <!-- Navigation du calendrier -->
                <div class="calendar-navigation">
                    <?php
                    $mois_precedent = $mois - 1;
                    $annee_precedente = $annee;
                    if ($mois_precedent < 1) {
                        $mois_precedent = 12;
                        $annee_precedente--;
                    }
                    
                    $mois_suivant = $mois + 1;
                    $annee_suivante = $annee;
                    if ($mois_suivant > 12) {
                        $mois_suivant = 1;
                        $annee_suivante++;
                    }
                    
                    $mois_noms = [
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    ];
                    ?>
                    
                    <a href="/calendrier?mois=<?= $mois_precedent ?>&annee=<?= $annee_precedente ?>" 
                       class="btn btn-secondary">
                        <i data-feather="chevron-left"></i>
                        Mois précédent
                    </a>
                    
                    <div class="calendar-selectors">
                        <select id="mois-select" class="form-select" onchange="navigateCalendar()">
                            <?php foreach ($mois_noms as $num => $nom): ?>
                                <option value="<?= $num ?>" <?= $num == $mois ? 'selected' : '' ?>><?= $nom ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select id="annee-select" class="form-select" onchange="navigateCalendar()">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 5; $y++): ?>
                                <option value="<?= $y ?>" <?= $y == $annee ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <a href="/calendrier?mois=<?= $mois_suivant ?>&annee=<?= $annee_suivante ?>" 
                       class="btn btn-secondary">
                        Mois suivant
                        <i data-feather="chevron-right"></i>
                    </a>
                </div>
                
                <script>
                function navigateCalendar() {
                    const mois = document.getElementById('mois-select').value;
                    const annee = document.getElementById('annee-select').value;
                    window.location.href = '/calendrier?mois=' + mois + '&annee=' + annee;
                }
                </script>

                <!-- Légende -->
                <div class="calendar-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #28a745;"></span>
                        <span>Congé validé</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #ffc107;"></span>
                        <span>En attente</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #dc3545;"></span>
                        <span>Refusé</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #17a2b8;"></span>
                        <span>Validé département</span>
                    </div>
                </div>

                <!-- Calendrier -->
                <div class="calendar-container">
                    <div class="calendar-grid">
                        <!-- En-têtes des jours -->
                        <div class="calendar-header">Lundi</div>
                        <div class="calendar-header">Mardi</div>
                        <div class="calendar-header">Mercredi</div>
                        <div class="calendar-header">Jeudi</div>
                        <div class="calendar-header">Vendredi</div>
                        <div class="calendar-header">Samedi</div>
                        <div class="calendar-header">Dimanche</div>
                        
                        <?php
                        // Calculer le premier jour du mois et le nombre de jours
                        $premier_jour = mktime(0, 0, 0, $mois, 1, $annee);
                        $nb_jours = date('t', $premier_jour);
                        $jour_semaine = date('N', $premier_jour); // 1 (lundi) à 7 (dimanche)
                        
                        // Organiser les congés par date
                        $conges_par_date = [];
                        foreach ($conges as $conge) {
                            $date_debut = new DateTime($conge['date_debut']);
                            $date_fin = new DateTime($conge['date_fin']);
                            $interval = new DateInterval('P1D');
                            $periode = new DatePeriod($date_debut, $interval, $date_fin->modify('+1 day'));
                            
                            foreach ($periode as $date) {
                                $date_str = $date->format('Y-m-d');
                                if (!isset($conges_par_date[$date_str])) {
                                    $conges_par_date[$date_str] = [];
                                }
                                $conges_par_date[$date_str][] = $conge;
                            }
                        }
                        
                        // Cases vides avant le 1er jour
                        for ($i = 1; $i < $jour_semaine; $i++) {
                            echo '<div class="calendar-day empty"></div>';
                        }
                        
                        // Afficher chaque jour du mois
                        for ($jour = 1; $jour <= $nb_jours; $jour++) {
                            $date = sprintf('%04d-%02d-%02d', $annee, $mois, $jour);
                            $conges_du_jour = $conges_par_date[$date] ?? [];
                            
                            // Déterminer si c'est aujourd'hui
                            $est_aujourdhui = ($date == date('Y-m-d')) ? 'today' : '';
                            
                            echo '<div class="calendar-day ' . $est_aujourdhui . '" data-date="' . $date . '">';
                            echo '<div class="day-number">' . $jour . '</div>';
                            
                            if (!empty($conges_du_jour)) {
                                $total_conges = count($conges_du_jour);
                                $max_display = 2; // Afficher maximum 2 congés
                                
                                // Badge de compteur en haut à droite si plus de 2 congés
                                if ($total_conges > $max_display) {
                                    echo '<span class="conge-badge">+' . ($total_conges - $max_display) . '</span>';
                                }
                                
                                echo '<div class="day-events">';
                                
                                // Afficher les premiers congés
                                for ($i = 0; $i < min($max_display, $total_conges); $i++) {
                                    $conge = $conges_du_jour[$i];
                                    
                                    // Déterminer la couleur selon le statut
                                    $couleur = '#6c757d'; // Par défaut gris
                                    if ($conge['status'] == 21) {
                                        $couleur = '#28a745'; // Validé RH - vert
                                    } elseif ($conge['status'] == 11) {
                                        $couleur = '#17a2b8'; // Validé département - bleu
                                    } elseif ($conge['status'] == 1) {
                                        $couleur = '#ffc107'; // En attente - jaune
                                    } elseif ($conge['status'] == 0) {
                                        $couleur = '#dc3545'; // Refusé - rouge
                                    }
                                    
                                        // Préparer les données pour le modal
                                        $date_debut_dt = new DateTime($conge['date_debut']);
                                        $date_fin_dt = new DateTime($conge['date_fin']);
                                        $duree = $date_debut_dt->diff($date_fin_dt)->days + 1;

                                        $conge_data = json_encode([
                                            'nom' => $conge['nom'] . ' ' . $conge['prenom'],
                                            'departement' => $conge['nom_departement'],
                                            'type' => $conge['type_conge'],
                                            'date_debut' => date('d/m/Y', strtotime($conge['date_debut'])),
                                            'date_fin' => date('d/m/Y', strtotime($conge['date_fin'])),
                                            'duree' => $duree,
                                            'motif' => $conge['motif'] ?? '',
                                            'status' => $conge['status'],
                                            'couleur' => $couleur
                                        ]);
                                    echo '<div class="event" onclick=\'showCongeModal(' . htmlspecialchars($conge_data, ENT_QUOTES) . ')\'>';
                                    echo '<span class="status-dot" style="background-color: ' . $couleur . ';"></span>';
                                    echo '<span class="event-name">' . htmlspecialchars(substr($conge['nom'], 0, 1) . '. ' . $conge['prenom']) . '</span>';
                                    echo '</div>';
                                }
                                
                                echo '</div>';
                                
                                // Tooltip avec tous les congés
                                if ($total_conges > $max_display) {
                                    echo '<div class="tooltip-content">';
                                    echo '<h6>' . date('d/m/Y', strtotime($date)) . ' - ' . $total_conges . ' congé' . ($total_conges > 1 ? 's' : '') . '</h6>';
                                    
                                    foreach ($conges_du_jour as $conge) {
                                        // Déterminer la couleur selon le statut
                                        $couleur = '#6c757d';
                                        if ($conge['status'] == 21) {
                                            $couleur = '#28a745';
                                        } elseif ($conge['status'] == 11) {
                                            $couleur = '#17a2b8';
                                        } elseif ($conge['status'] == 1) {
                                            $couleur = '#ffc107';
                                        } elseif ($conge['status'] == 0) {
                                            $couleur = '#dc3545';
                                        }
                                        
                                        // Calculer la durée
                                        $date_debut_dt = new DateTime($conge['date_debut']);
                                        $date_fin_dt = new DateTime($conge['date_fin']);
                                        $duree = $date_debut_dt->diff($date_fin_dt)->days + 1;
                                        
                                        // Préparer les données pour le modal
                                        $conge_data_tooltip = json_encode([
                                            'nom' => $conge['nom'] . ' ' . $conge['prenom'],
                                            'departement' => $conge['nom_departement'],
                                            'type' => $conge['type_conge'],
                                            'date_debut' => date('d/m/Y', strtotime($conge['date_debut'])),
                                            'date_fin' => date('d/m/Y', strtotime($conge['date_fin'])),
                                            'duree' => $duree,
                                            'motif' => $conge['motif'] ?? '',
                                            'status' => $conge['status'],
                                            'couleur' => $couleur
                                        ]);
                                        
                                        echo '<div class="tooltip-event" onclick=\'showCongeModal(' . htmlspecialchars($conge_data_tooltip, ENT_QUOTES) . ')\'>';
                                        echo '<span class="status-dot" style="background-color: ' . $couleur . ';"></span>';
                                        echo '<div class="tooltip-event-content">';
                                        echo '<strong>' . htmlspecialchars($conge['nom'] . ' ' . $conge['prenom']) . '</strong><br>';
                                        echo '<small>' . htmlspecialchars($conge['type_conge']) . ' - ' . htmlspecialchars($conge['nom_departement']) . '</small>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                    
                                    echo '</div>';
                                }
                            }
                            
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Liste des congés du mois -->
                <section class="section mt-4">
                    <h3 class="section-title">
                        <i data-feather="list"></i>
                        Liste des congés du mois
                    </h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Employé</th>
                                    <th>Département</th>
                                    <th>Type</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($conges)): ?>
                                    <?php foreach ($conges as $conge): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($conge['nom'] . ' ' . $conge['prenom']) ?></td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    <?= htmlspecialchars($conge['nom_departement']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary text-white">
                                                    <?= htmlspecialchars($conge['type_conge']) ?>
                                                </span>
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
                                            <td>
                                                <?php
                                                $statut = $conge['status'];
                                                $badge_class = 'secondary';
                                                $statut_text = 'En attente';
                                                if ($statut == 0) {
                                                    $badge_class = 'danger';
                                                    $statut_text = 'Refusé';
                                                } elseif ($statut == 11) {
                                                    $badge_class = 'info';
                                                    $statut_text = 'Validé département';
                                                } elseif ($statut == 21) {
                                                    $badge_class = 'success';
                                                    $statut_text = 'Validé RH';
                                                }
                                                ?>
                                                <span class="badge bg-<?= $badge_class ?> text-white">
                                                    <?= $statut_text ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Aucun congé ce mois-ci
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
    
    <!-- Modal pour les détails du congé -->
    <div class="modal fade" id="congeModal" tabindex="-1" aria-labelledby="congeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="congeModalLabel">
                        <i data-feather="info"></i>
                        Détails du congé
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="conge-detail-item">
                        <strong>Employé :</strong>
                        <span id="modal-nom"></span>
                    </div>
                    <div class="conge-detail-item">
                        <strong>Département :</strong>
                        <span id="modal-departement"></span>
                    </div>
                    <div class="conge-detail-item">
                        <strong>Type de congé :</strong>
                        <span id="modal-type"></span>
                    </div>
                    <div class="conge-detail-item">
                        <strong>Date de début :</strong>
                        <span id="modal-debut"></span>
                    </div>
                    <div class="conge-detail-item">
                        <strong>Date de fin :</strong>
                        <span id="modal-fin"></span>
                    </div>
                    <div class="conge-detail-item">
                        <strong>Durée :</strong>
                        <span id="modal-duree"></span>
                    </div>
                    <div class="conge-detail-item" id="modal-motif-container" style="display: none;">
                        <strong>Motif :</strong>
                        <span id="modal-motif"></span>
                    </div>
                    <div class="conge-detail-item">
                        <strong>Statut :</strong>
                        <span id="modal-status" class="badge"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
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
        
        // Fonction pour afficher le modal avec les détails du congé
        function showCongeModal(congeData) {
            document.getElementById('modal-nom').textContent = congeData.nom;
            document.getElementById('modal-departement').textContent = congeData.departement;
            document.getElementById('modal-type').textContent = congeData.type;
            document.getElementById('modal-debut').textContent = congeData.date_debut;
            document.getElementById('modal-fin').textContent = congeData.date_fin;
            document.getElementById('modal-duree').textContent = congeData.duree + ' jour(s)';
            
            // Afficher le motif si présent
            if (congeData.motif && congeData.motif.trim() !== '') {
                document.getElementById('modal-motif').textContent = congeData.motif;
                document.getElementById('modal-motif-container').style.display = 'block';
            } else {
                document.getElementById('modal-motif-container').style.display = 'none';
            }
            
            // Déterminer le badge de statut
            const statusBadge = document.getElementById('modal-status');
            let badgeClass = 'bg-secondary';
            let statusText = 'Inconnu';
            
            if (congeData.status == 0) {
                badgeClass = 'bg-danger';
                statusText = 'Refusé';
            } else if (congeData.status == 1) {
                badgeClass = 'bg-warning text-dark';
                statusText = 'En attente';
            } else if (congeData.status == 11) {
                badgeClass = 'bg-info';
                statusText = 'Validé département';
            } else if (congeData.status == 21) {
                badgeClass = 'bg-success';
                statusText = 'Validé RH';
            }
            
            statusBadge.className = 'badge ' + badgeClass;
            statusBadge.textContent = statusText;
            
            // Ouvrir le modal avec Bootstrap
            const modalElement = document.getElementById('congeModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            
            // Rafraîchir les icônes Feather
            setTimeout(() => feather.replace(), 100);
        }
        
        // Gestion du tooltip au survol des jours avec plusieurs congés
        document.addEventListener('DOMContentLoaded', function() {
            const calendarDays = document.querySelectorAll('.calendar-day');
            
            calendarDays.forEach(day => {
                const tooltip = day.querySelector('.tooltip-content');
                if (tooltip) {
                    let hideTimeout;
                    
                    // Afficher au survol du jour
                    day.addEventListener('mouseenter', function() {
                        clearTimeout(hideTimeout);
                        tooltip.classList.add('show');
                    });
                    
                    // Masquer avec délai au survol du jour
                    day.addEventListener('mouseleave', function(e) {
                        // Vérifier si on survole le tooltip
                        if (!tooltip.contains(e.relatedTarget)) {
                            hideTimeout = setTimeout(() => {
                                tooltip.classList.remove('show');
                            }, 200);
                        }
                    });
                    
                    // Garder visible au survol du tooltip
                    tooltip.addEventListener('mouseenter', function() {
                        clearTimeout(hideTimeout);
                        tooltip.classList.add('show');
                    });
                    
                    // Masquer quand on quitte le tooltip
                    tooltip.addEventListener('mouseleave', function() {
                        hideTimeout = setTimeout(() => {
                            tooltip.classList.remove('show');
                        }, 200);
                    });
                }
            });
        });
    </script>
</body>
</html>
