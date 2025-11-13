<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des congés</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/calendrier.css">
    <script src="https://unpkg.com/feather-icons"></script>
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
                    
                    <h2 class="calendar-title">
                        <?= $mois_noms[$mois] ?> <?= $annee ?>
                    </h2>
                    
                    <a href="/calendrier?mois=<?= $mois_suivant ?>&annee=<?= $annee_suivante ?>" 
                       class="btn btn-secondary">
                        Mois suivant
                        <i data-feather="chevron-right"></i>
                    </a>
                </div>

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
                                echo '<div class="day-events">';
                                foreach ($conges_du_jour as $conge) {
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
                                    
                                    echo '<div class="event" style="background-color: ' . $couleur . ';" 
                                          title="' . htmlspecialchars($conge['nom'] . ' ' . $conge['prenom'] . ' - ' . $conge['type_conge']) . '">';
                                    echo '<small>' . htmlspecialchars(substr($conge['nom'], 0, 1) . '. ' . $conge['prenom']) . '</small>';
                                    echo '</div>';
                                }
                                echo '</div>';
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
    
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        feather.replace();
    </script>
</body>
</html>
