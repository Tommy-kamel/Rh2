<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes de congés en attente</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                    Demandes de congés en attente
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
                                <?php if (!empty($conges_en_attente)): ?>
                                    <?php 
                                    $departements = array_unique(array_column($conges_en_attente, 'nom_departement'));
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
                                <?php if (!empty($conges_en_attente)): ?>
                                    <?php 
                                    $types = array_unique(array_column($conges_en_attente, 'type'));
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

                <!-- Liste des demandes -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-feather="list"></i>
                            Liste complète
                        </h2>
                        <div class="section-actions" style="display: flex; gap: 1rem; align-items: center;">
                            <?php if (!empty($conges_en_attente)): ?>
                            <button type="button" onclick="getAllSuggestions()" class="btn-gemini" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border: none; border-radius: 24px; background: linear-gradient(135deg, #4285f4 0%, #9b72cb 50%, #d96570 100%); color: white; font-weight: 500; cursor: pointer; box-shadow: 0 2px 8px rgba(66, 133, 244, 0.3); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(66, 133, 244, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(66, 133, 244, 0.3)';">
                                <img src="/assets/images/gemini-color.svg" alt="Gemini" style="width: 20px; height: 20px; filter: brightness(0) invert(1);">
                                Suggérer avec IA
                            </button>
                            <?php endif; ?>
                            <!-- <span style="font-size: 1.5rem; font-weight: 600; color: #4285f4;">
                                <span id="totalRows">0</span>
                            </span> -->
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
                                                <div style="display: flex; gap: 8px; align-items: center;">
                                                    <form method="POST" action="/admin/conges/valider" style="display: inline;">
                                                        <input type="hidden" name="id_conge" value="<?= $conge['id_conge'] ?>">
                                                        <button type="submit" title="Valider" onclick="return confirm('Confirmer la validation de cette demande de congé ?')" style="color: #28a745; text-decoration: none; background: none; border: none; cursor: pointer; padding: 0;">
                                                            <i data-feather="check-circle" style="width: 20px; height: 20px;"></i>
                                                        </button>
                                                    </form>
                                                    <a href="<?= ($_SESSION['id_departement'] ?? null) === 1 ? '/rh/conges/refuser/' : '/admin/conges/refuser/' ?><?= $conge['id_conge'] ?>" 
                                                       title="Refuser" onclick="return confirm('Confirmer le refus de cette demande de congé ?')" style="color: #dc3545; text-decoration: none;">
                                                        <i data-feather="x-circle" style="width: 20px; height: 20px;"></i>
                                                    </a>
                                                    <a href="<?= ($_SESSION['id_departement'] ?? null) === 1 ? '/rh/conges/details/' : '/admin/conges/details/' ?><?= $conge['id_conge'] ?>" 
                                                       title="Voir détails" style="color: #17a2b8; text-decoration: none;">
                                                        <i data-feather="eye" style="width: 20px; height: 20px;"></i>
                                                    </a>
                                                    <a href="<?= ($_SESSION['id_departement'] ?? null) === 1 ? '/rh/conges/modifier/' : '/admin/conges/modifier/' ?><?= $conge['id_conge'] ?>" 
                                                       title="Modifier" style="color: #ffc107; text-decoration: none;">
                                                        <i data-feather="edit" style="width: 20px; height: 20px;"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
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

    <!-- Modal pour les suggestions IA -->
    <div class="modal fade" id="suggestionModal" tabindex="-1" aria-labelledby="suggestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="suggestionModalLabel" style="display: flex; align-items: center; gap: 0.5rem;">
                        <img src="/assets/images/gemini-color.svg" alt="Gemini" style="width: 24px; height: 24px;">
                        Suggestions IA pour l'optimisation des congés
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="suggestionContent">
                        <!-- Le contenu de la suggestion sera inséré ici -->
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

        // Fonction pour obtenir les suggestions IA pour tous les congés
        function getAllSuggestions() {
            const suggestionContent = document.getElementById('suggestionContent');
            suggestionContent.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border" role="status" style="width: 2rem; height: 2rem; border-width: 0.2rem; color: #4285f4;">
                        <span class="visually-hidden">Analyse en cours...</span>
                    </div>
                    <p class="mt-3" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem; color: #5f6368; font-weight: 400;">Analyse en cours...</p>
                </div>
            `;
            
            // Afficher le modal
            const modal = new bootstrap.Modal(document.getElementById('suggestionModal'));
            modal.show();
            
            // Prendre le premier congé comme référence (le système analysera tous les congés)
            const firstCongeId = <?= !empty($conges_en_attente) ? $conges_en_attente[0]['id_conge'] : 0 ?>;
            
            fetch(`/admin/conges/suggestions/${firstCongeId}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.error) {
                        suggestionContent.innerHTML = `
                            <div style="padding: 1.5rem; background: #fef7f7; border-radius: 12px; border: 1px solid #f5c6cb; font-family: 'Outfit', sans-serif;">
                                <p style="color: #721c24; margin: 0; font-weight: 500;">Erreur: ${data.error}</p>
                            </div>
                        `;
                    } else if (!data.suggestion) {
                        suggestionContent.innerHTML = `
                            <div style="padding: 1.5rem; background: #fff3cd; border-radius: 12px; border: 1px solid #ffc107; font-family: 'Outfit', sans-serif;">
                                <p style="color: #664d03; margin: 0; font-weight: 500;">Aucune suggestion reçue. Données: ${JSON.stringify(data)}</p>
                            </div>
                        `;
                    } else {
                        // Afficher la réponse avec style élégant
                        const text = data.suggestion;
                        console.log('Texte brut:', text);
                        let formattedHtml = '<div style="font-family: \'Outfit\', sans-serif; line-height: 1.8; color: #202124;">';
                        
                        // Séparer par doubles sauts de ligne pour identifier les blocs
                        const blocks = text.split('\n\n').filter(b => b.trim() !== '');
                        console.log('Blocs:', blocks);
                        
                        blocks.forEach(block => {
                            const lines = block.split('\n').map(l => l.trim()).filter(l => l !== '');
                            
                            if (lines.length === 0) return;
                            
                            // Première ligne = employé (nom + département)
                            const employeeLine = lines[0];
                            // Deuxième ligne = période
                            const periodeLine = lines.length > 1 ? lines[1] : '';
                            // Troisième ligne = action
                            const actionLine = lines.length > 2 ? lines[2] : '';
                            // Quatrième ligne = justification
                            const justificationLine = lines.length > 3 ? lines[3] : '';
                            
                            let actionColor, actionBg, actionIcon, actionText;
                            
                            if (actionLine.includes('VALIDER')) {
                                actionColor = '#0f5132';
                                actionBg = '#d1e7dd';
                                actionIcon = '✓';
                                actionText = 'Valider';
                            } else if (actionLine.includes('REFUSER')) {
                                actionColor = '#842029';
                                actionBg = '#f8d7da';
                                actionIcon = '✗';
                                actionText = 'Refuser';
                            } else if (actionLine.includes('NOUVELLE') || actionLine.includes('PROPOSER')) {
                                actionColor = '#664d03';
                                actionBg = '#fff3cd';
                                actionIcon = '⚠';
                                actionText = 'Proposer nouvelle date';
                            } else {
                                actionColor = '#084298';
                                actionBg = '#cfe2ff';
                                actionIcon = 'ℹ';
                                actionText = 'Information';
                            }
                            
                            const reason = justificationLine.replace(/^Justification\s*:\s*/i, '');
                            
                            formattedHtml += `
                                <div style="margin-bottom: 1.25rem; padding: 1.25rem; background: #ffffff; border: 1px solid #e8eaed; border-radius: 12px; transition: all 0.2s ease;" onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'; this.style.borderColor='#dadce0';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e8eaed';">
                                    <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 0.75rem;">
                                        <div style="flex: 1;">
                                            <p style="margin: 0 0 0.25rem 0; font-size: 0.95rem; color: #202124; font-weight: 600; line-height: 1.4;">${employeeLine}</p>
                                            <p style="margin: 0; font-size: 0.85rem; color: #5f6368; font-weight: 400;">${periodeLine}</p>
                                        </div>
                                        <div style="padding: 0.375rem 0.875rem; background: ${actionBg}; border-radius: 20px; font-size: 0.8rem; font-weight: 600; color: ${actionColor}; white-space: nowrap;">
                                            ${actionIcon} ${actionText}
                                        </div>
                                    </div>
                                    <p style="margin: 0; font-size: 0.875rem; color: #5f6368; line-height: 1.6; font-weight: 400;">${reason}</p>
                                </div>
                            `;
                        });
                        
                        formattedHtml += '</div>';
                        suggestionContent.innerHTML = formattedHtml;
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    suggestionContent.innerHTML = `
                        <div style="padding: 1.5rem; background: #fef7f7; border-radius: 12px; border: 1px solid #f5c6cb; font-family: 'Outfit', sans-serif;">
                            <p style="color: #721c24; margin: 0; font-weight: 500;">Erreur de connexion: ${error.message}</p>
                        </div>
                    `;
                });
        }
    </script>
    <script src="/assets/js/conges-attente.js"></script>
</body>
</html>
