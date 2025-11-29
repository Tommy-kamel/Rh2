<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs d'Audit - RH2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/audit-logs.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content audit-page">
        <div class="audit-container">
            <!-- Header -->
            <div class="audit-header">
                <h1><i data-feather="shield"></i> Logs d'Audit</h1>
            </div>

            <!-- Stats Cards -->
            <div class="audit-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i data-feather="activity"></i>
                    </div>
                    <h3>Total Actions</h3>
                    <p><?php echo number_format($total ?? 0); ?></p>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i data-feather="check-circle"></i>
                    </div>
                    <h3>Aujourd'hui</h3>
                    <p><?php 
                        $today = 0;
                        foreach($logs as $log) {
                            if(date('Y-m-d', strtotime($log['timestamp'])) == date('Y-m-d')) {
                                $today++;
                            }
                        }
                        echo $today;
                    ?></p>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i data-feather="users"></i>
                    </div>
                    <h3>Utilisateurs</h3>
                    <p><?php echo count($users ?? []); ?></p>
                </div>
                <div class="stat-card danger">
                    <div class="stat-icon">
                        <i data-feather="database"></i>
                    </div>
                    <h3>Tables Surveillées</h3>
                    <p><?php 
                        $tables = array_unique(array_column($logs, 'table_name'));
                        echo count(array_filter($tables));
                    ?></p>
                </div>
            </div>

            <!-- Filtres -->
            <div class="filters-card">
                <h5 class="card-title">
                    <i data-feather="filter"></i>
                    Filtres de recherche
                </h5>
                <form method="GET" action="/admin/audit-logs" class="row g-3">
                    <div class="col-md-3">
                        <label for="user_id" class="form-label">
                            <i data-feather="user" style="width: 16px; height: 16px;"></i>
                            Utilisateur
                        </label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">Tous les utilisateurs</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id_user']; ?>" <?php echo (isset($filters['user_id']) && $filters['user_id'] == $user['id_user']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['nom_utilisateur']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="action" class="form-label">
                            <i data-feather="zap" style="width: 16px; height: 16px;"></i>
                            Action
                        </label>
                        <input type="text" name="action" id="action" class="form-control" value="<?php echo htmlspecialchars($filters['action'] ?? ''); ?>" placeholder="Ex: CREATE, UPDATE...">
                    </div>
                    <div class="col-md-3">
                        <label for="table_name" class="form-label">
                            <i data-feather="database" style="width: 16px; height: 16px;"></i>
                            Table
                        </label>
                        <select name="table_name" id="table_name" class="form-select">
                            <option value="">Toutes les tables</option>
                            <option value="employe" <?php echo (isset($filters['table_name']) && $filters['table_name'] == 'employe') ? 'selected' : ''; ?>>Employé</option>
                            <option value="contrat" <?php echo (isset($filters['table_name']) && $filters['table_name'] == 'contrat') ? 'selected' : ''; ?>>Contrat</option>
                            <option value="conge" <?php echo (isset($filters['table_name']) && $filters['table_name'] == 'conge') ? 'selected' : ''; ?>>Congé</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_debut" class="form-label">
                            <i data-feather="calendar" style="width: 16px; height: 16px;"></i>
                            Date début
                        </label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?php echo htmlspecialchars($filters['date_debut'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_fin" class="form-label">
                            <i data-feather="calendar" style="width: 16px; height: 16px;"></i>
                            Date fin
                        </label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?php echo htmlspecialchars($filters['date_fin'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-filter">
                            <i data-feather="search" style="width: 16px; height: 16px;"></i>
                            Filtrer
                        </button>
                        <a href="/admin/audit-logs" class="btn btn-reset">
                            <i data-feather="x" style="width: 16px; height: 16px;"></i>
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Liste des logs -->
            <div class="logs-table-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i data-feather="list"></i>
                        Historique des actions
                        <span class="badge-count"><?php echo number_format($total ?? 0); ?> logs</span>
                    </h5>
                </div>
                <div class="table-responsive">
                    <?php if (empty($logs)): ?>
                        <div class="empty-state">
                            <i data-feather="inbox"></i>
                            <h3>Aucun log trouvé</h3>
                            <p>Il n'y a aucune activité correspondant à vos critères de recherche.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><i data-feather="clock" style="width: 16px; height: 16px;"></i> Date/Heure</th>
                                    <th><i data-feather="user" style="width: 16px; height: 16px;"></i> Utilisateur</th>
                                    <th><i data-feather="activity" style="width: 16px; height: 16px;"></i> Action</th>
                                    <th><i data-feather="database" style="width: 16px; height: 16px;"></i> Table</th>
                                    <th><i data-feather="hash" style="width: 16px; height: 16px;"></i> ID</th>
                                    <th><i data-feather="globe" style="width: 16px; height: 16px;"></i> IP</th>
                                    <th><i data-feather="more-vertical" style="width: 16px; height: 16px;"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): 
                                    $action_type = strtolower(explode('_', $log['action'])[0]);
                                    $badge_class = 'badge-action ';
                                    if (strpos($log['action'], 'CREATE') !== false) $badge_class .= 'badge-create';
                                    elseif (strpos($log['action'], 'UPDATE') !== false) $badge_class .= 'badge-update';
                                    elseif (strpos($log['action'], 'DELETE') !== false) $badge_class .= 'badge-delete';
                                    elseif (strpos($log['action'], 'LOGIN') !== false) $badge_class .= 'badge-login';
                                    elseif (strpos($log['action'], 'VIEW') !== false) $badge_class .= 'badge-view';
                                    else $badge_class .= 'badge-create';
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($log['timestamp']))); ?></strong><br>
                                            <small style="color: var(--audit-muted);"><?php echo htmlspecialchars(date('H:i:s', strtotime($log['timestamp']))); ?></small>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                                    <?php echo strtoupper(substr($log['nom_utilisateur'] ?? 'A', 0, 1)); ?>
                                                </div>
                                                <span><?php echo htmlspecialchars($log['nom_utilisateur'] ?? 'Anonyme'); ?></span>
                                            </div>
                                        </td>
                                        <td><span class="<?php echo $badge_class; ?>"><?php echo htmlspecialchars($log['action']); ?></span></td>
                                        <td><?php echo htmlspecialchars($log['table_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($log['record_id'] ?? '-'); ?></td>
                                        <td><code style="background: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8125rem;"><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></code></td>
                                        <td>
                                            <button class="btn btn-details" onclick="showLogDetails(<?php echo $log['id_audit']; ?>)">
                                                <i data-feather="eye" style="width: 16px; height: 16px;"></i>
                                                Détails
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Pagination des logs">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&<?php echo http_build_query($filters); ?>">
                                        <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                                        Précédent
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php 
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            for ($i = $start; $i <= $end; $i++): 
                            ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&<?php echo http_build_query($filters); ?>">
                                        Suivant
                                        <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal pour les détails du log -->
    <div class="modal fade" id="logDetailsModal" tabindex="-1" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logDetailsModalLabel">Détails du log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body" id="logDetailsContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialiser Feather Icons
        feather.replace();

        function showLogDetails(logId) {
            fetch(`/admin/audit-logs/${logId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Erreur: ' + data.error);
                        return;
                    }

                    let content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i data-feather="info" style="width: 16px; height: 16px;"></i> Informations générales</h6>
                                <p><strong>Date/Heure:</strong> ${new Date(data.timestamp).toLocaleString('fr-FR')}</p>
                                <p><strong>Utilisateur:</strong> ${data.user_id ? 'ID: ' + data.user_id : 'Anonyme'}</p>
                                <p><strong>Action:</strong> <span class="badge-action badge-create">${data.action}</span></p>
                                <p><strong>Table:</strong> ${data.table_name || '-'}</p>
                                <p><strong>ID Enregistrement:</strong> ${data.record_id || '-'}</p>
                                <p><strong>Adresse IP:</strong> <code style="background: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 4px;">${data.ip_address || '-'}</code></p>
                            </div>
                            <div class="col-md-6">
                                <h6><i data-feather="code" style="width: 16px; height: 16px;"></i> Valeurs</h6>
                                ${data.old_values ? `<p><strong>Anciennes valeurs:</strong></p><pre>${JSON.stringify(data.old_values, null, 2)}</pre>` : ''}
                                ${data.new_values ? `<p><strong>Nouvelles valeurs:</strong></p><pre>${JSON.stringify(data.new_values, null, 2)}</pre>` : ''}
                                ${data.user_agent ? `<p><strong>User-Agent:</strong></p><small style="color: var(--audit-muted); display: block; margin-top: 0.5rem;">${data.user_agent}</small>` : ''}
                            </div>
                        </div>
                    `;

                    document.getElementById('logDetailsContent').innerHTML = content;
                    const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
                    modal.show();
                    // Réinitialiser les icônes Feather après insertion du HTML
                    setTimeout(() => feather.replace(), 100);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des détails');
                });
        }
    </script>
</body>
</html>