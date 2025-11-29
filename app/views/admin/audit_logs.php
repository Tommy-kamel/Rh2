<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs d'Audit - RH2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Logs d'Audit</h1>

                    <!-- Filtres -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Filtres</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="/admin/audit-logs" class="row g-3">
                                <div class="col-md-3">
                                    <label for="user_id" class="form-label">Utilisateur</label>
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
                                    <label for="action" class="form-label">Action</label>
                                    <input type="text" name="action" id="action" class="form-control" value="<?php echo htmlspecialchars($filters['action'] ?? ''); ?>" placeholder="Ex: POST, UPDATE...">
                                </div>
                                <div class="col-md-3">
                                    <label for="table_name" class="form-label">Table</label>
                                    <select name="table_name" id="table_name" class="form-select">
                                        <option value="">Toutes les tables</option>
                                        <option value="employe" <?php echo (isset($filters['table_name']) && $filters['table_name'] == 'employe') ? 'selected' : ''; ?>>Employé</option>
                                        <option value="contrat" <?php echo (isset($filters['table_name']) && $filters['table_name'] == 'contrat') ? 'selected' : ''; ?>>Contrat</option>
                                        <option value="conge" <?php echo (isset($filters['table_name']) && $filters['table_name'] == 'conge') ? 'selected' : ''; ?>>Congé</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="date_debut" class="form-label">Date début</label>
                                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?php echo htmlspecialchars($filters['date_debut'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_fin" class="form-label">Date fin</label>
                                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?php echo htmlspecialchars($filters['date_fin'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                                    <a href="/admin/audit-logs" class="btn btn-secondary">Réinitialiser</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Liste des logs -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Historique des actions (<?php echo $total; ?> logs)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date/Heure</th>
                                            <th>Utilisateur</th>
                                            <th>Action</th>
                                            <th>Table</th>
                                            <th>ID Enregistrement</th>
                                            <th>Adresse IP</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($logs)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Aucun log trouvé</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($logs as $log): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($log['timestamp']))); ?></td>
                                                    <td><?php echo htmlspecialchars($log['nom_utilisateur'] ?? 'Anonyme'); ?></td>
                                                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                                                    <td><?php echo htmlspecialchars($log['table_name'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($log['record_id'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="showLogDetails(<?php echo $log['id_audit']; ?>)">
                                                            Détails
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Pagination des logs">
                                    <ul class="pagination justify-content-center">
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo http_build_query(array_merge($filters, ['page' => $i])); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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
                                <h6>Informations générales</h6>
                                <p><strong>Date/Heure:</strong> ${new Date(data.timestamp).toLocaleString('fr-FR')}</p>
                                <p><strong>Utilisateur:</strong> ${data.user_id ? 'ID: ' + data.user_id : 'Anonyme'}</p>
                                <p><strong>Action:</strong> ${data.action}</p>
                                <p><strong>Table:</strong> ${data.table_name || '-'}</p>
                                <p><strong>ID Enregistrement:</strong> ${data.record_id || '-'}</p>
                                <p><strong>Adresse IP:</strong> ${data.ip_address || '-'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Valeurs</h6>
                                ${data.old_values ? `<p><strong>Anciennes valeurs:</strong></p><pre>${JSON.stringify(data.old_values, null, 2)}</pre>` : ''}
                                ${data.new_values ? `<p><strong>Nouvelles valeurs:</strong></p><pre>${JSON.stringify(data.new_values, null, 2)}</pre>` : ''}
                                ${data.user_agent ? `<p><strong>User-Agent:</strong></p><small>${data.user_agent}</small>` : ''}
                            </div>
                        </div>
                    `;

                    document.getElementById('logDetailsContent').innerHTML = content;
                    new bootstrap.Modal(document.getElementById('logDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des détails');
                });
        }
    </script>
</body>
</html>