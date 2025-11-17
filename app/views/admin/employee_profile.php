<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche employé — Lecture seule</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --secondary-color: #2c3e50;
            --accent-color: #9b59b6;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
        }
        
        .btn-light {
            background-color: white;
            border: 1px solid #e0e0e0;
            color: var(--secondary-color);
            font-weight: 500;
            transition: var(--transition);
            border-radius: 8px;
            padding: 8px 16px;
        }
        
        .btn-light:hover {
            background-color: #f0f0f0;
            border-color: #d0d0d0;
            transform: translateY(-1px);
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            background: #fff;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            box-shadow: var(--card-shadow-hover);
        }
        
        h2 {
            color: var(--secondary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        h3, h5 {
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        .field-label {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .field-value {
            color: #555;
            font-size: 1rem;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 12px;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: var(--transition);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: var(--transition);
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
        }
        
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            background-color: var(--light-bg);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            border-bottom: 1px solid #eaeaea;
        }
        
        .list-unstyled li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
            transition: var(--transition);
        }
        
        .list-unstyled li:hover {
            background-color: #f9f9f9;
            padding-left: 0.5rem;
        }
        
        .list-unstyled li:last-child {
            border-bottom: none;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .employee-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .employee-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1.5rem;
            border: 3px solid var(--primary-color);
        }
        
        .employee-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--secondary-color);
        }
        
        .employee-id {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .section-title {
            position: relative;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .info-item {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .employee-header {
                flex-direction: column;
                text-align: center;
            }
            
            .employee-avatar {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
<?php $e = $employee ?? []; ?>
<div class="container py-4">
    <a href="/employes" class="btn btn-light mb-4">&larr; Retour à la liste</a>

    <div class="employee-header">
        <?php if (!empty($e['photo'])): ?>
            <img src="<?= htmlspecialchars($e['photo']) ?>" alt="Photo employé" class="employee-avatar">
        <?php else: ?>
            <div class="employee-avatar d-flex align-items-center justify-content-center bg-light text-muted">
                <i data-feather="user" style="width: 40px; height: 40px;"></i>
            </div>
        <?php endif; ?>
        <div>
            <h1 class="employee-name"><?= htmlspecialchars(($e['prenom'] ?? '') . ' ' . ($e['nom'] ?? '')) ?></h1>
            <div class="employee-id">ID: <?= htmlspecialchars($e['id_employe'] ?? '') ?></div>
            <div>
                <?php if (!empty($e['contrat_type'])): ?>
                    <span class="badge status-active">Contrat <?= htmlspecialchars($e['contrat_type'] ?? '') ?></span>
                <?php else: ?>
                    <span class="badge status-inactive">Sans contrat</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <h3 class="section-title">Informations personnelles</h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="field-label">Date de naissance</div>
                        <div class="field-value"><?= !empty($e['date_naissance']) ? date('d/m/Y', strtotime($e['date_naissance'])) : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="field-label">Lieu de naissance</div>
                        <div class="field-value"><?= htmlspecialchars($e['lieu_naissance'] ?? '-') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="field-label">Sexe</div>
                        <div class="field-value"><?= htmlspecialchars($e['sexe'] ?? '-') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="field-label">Email</div>
                        <div class="field-value"><?= htmlspecialchars($e['email'] ?? '-') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="field-label">Téléphone</div>
                        <div class="field-value"><?= htmlspecialchars($e['telephone'] ?? '-') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="field-label">Numéro CNAPS</div>
                        <div class="field-value"><?= htmlspecialchars($e['numero_cnaps'] ?? '-') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="field-label">CIN</div>
                        <div class="field-value"><?= htmlspecialchars($e['cin'] ?? '-') ?></div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="field-label">Adresse</div>
                    <div class="field-value"><?= nl2br(htmlspecialchars($e['adresse'] ?? '-')) ?></div>
                </div>
            </div>

            <div class="card">
                <h3 class="section-title">Contrat actuel</h3>

                <?php if (!empty($e['contrat_type']) || !empty($e['contrat_date_debut'])): ?>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="field-label">Type</div>
                            <div class="field-value"><?= htmlspecialchars($e['contrat_type'] ?? '-') ?></div>
                        </div>
                        <div class="info-item">
                            <div class="field-label">Début</div>
                            <div class="field-value"><?= !empty($e['contrat_date_debut']) ? date('d/m/Y', strtotime($e['contrat_date_debut'])) : '-' ?></div>
                        </div>
                        <div class="info-item">
                            <div class="field-label">Fin</div>
                            <div class="field-value"><?= !empty($e['contrat_date_fin']) ? date('d/m/Y', strtotime($e['contrat_date_fin'])) : '— en cours' ?></div>
                        </div>
                        <div class="info-item">
                            <div class="field-label">Salaire</div>
                            <div class="field-value"><?= isset($e['salaire']) ? number_format($e['salaire'],2,',',' ') . ' Ar' : '-' ?></div>
                        </div>
                        <div class="info-item">
                            <div class="field-label">Poste (contrat)</div>
                            <div class="field-value"><?= htmlspecialchars($e['nom_poste'] ?? '-') ?></div>
                        </div>
                        <div class="info-item">
                            <div class="field-label">Département</div>
                            <div class="field-value"><?= htmlspecialchars($e['nom_departement'] ?? '-') ?></div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <form method="POST" action="/employes/<?= $e['id_employe'] ?>/contrat/terminer" style="display:inline-block;">
                            <input type="hidden" name="id_contrat" value="<?= htmlspecialchars($e['id_contrat'] ?? '') ?>">
                            <button class="btn btn-danger" onclick="return confirm('Terminer le contrat ?')">Terminer contrat</button>
                        </form>

                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#renewContractModal">Renouveler</button>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Aucun contrat trouvé pour cet employé.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createContractModal">Créer un contrat</button>
                <?php endif; ?>
            </div>

<!-- Modal Create Contract -->
<div class="modal fade" id="createContractModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="/employes/<?= $e['id_employe'] ?>/contrat/creer">
      <div class="modal-header">
        <h5 class="modal-title">Créer contrat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Salaire</label>
          <input name="salaire" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Date début</label>
          <input type="date" name="date_debut" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Date fin (optionnel)</label>
          <input type="date" name="date_fin" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Type</label>
          <select name="type" class="form-select">
            <option>CDI</option>
            <option>CDD</option>
            <option>Essai</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Période d'essai (jours)</label>
          <input type="number" name="periode_essai_jours" class="form-control" min="0" value="0">
        </div>
        <div class="mb-3">
          <label class="form-label">Poste (id_poste)</label>
          <input name="id_poste" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Département (id_departement)</label>
          <input name="id_departement" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button class="btn btn-primary">Créer</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Renew Contract -->
<div class="modal fade" id="renewContractModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="/employes/<?= $e['id_employe'] ?>/contrat/renouveler">
      <div class="modal-header">
        <h5 class="modal-title">Renouveler contrat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_contrat" value="<?= htmlspecialchars($e['id_contrat'] ?? '') ?>">
        <div class="mb-3">
          <label class="form-label">Salaire</label>
          <input name="salaire" class="form-control" value="<?= htmlspecialchars($e['salaire'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Date début nouvelle période</label>
          <input type="date" name="date_debut" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Date fin (optionnel)</label>
          <input type="date" name="date_fin" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Type</label>
          <select name="type" class="form-select">
            <option>CDI</option>
            <option>CDD</option>
            <option>Essai</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Période d'essai (jours)</label>
          <input type="number" name="periode_essai_jours" class="form-control" min="0" value="0">
        </div>
        <div class="mb-3">
          <label class="form-label">Poste (id_poste)</label>
          <input name="id_poste" class="form-control" value="<?= htmlspecialchars($e['id_poste'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Département (id_departement)</label>
          <input name="id_departement" class="form-control" value="<?= htmlspecialchars($e['id_departement'] ?? '') ?>">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Renouveler</button>
      </div>
    </form>
  </div>
</div>

        </div>

        <aside class="col-md-4">
            <div class="card mb-4 text-center">
                <h5 class="section-title">Photo</h5>
                <?php if (!empty($e['photo'])): ?>
                    <img src="<?= htmlspecialchars($e['photo']) ?>" alt="Photo employé" class="photo-preview mb-3">
                <?php else: ?>
                    <div class="mb-3 text-muted d-flex justify-content-center">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 200px; height: 200px;">
                            <i data-feather="user" style="width: 80px; height: 80px;"></i>
                        </div>
                    </div>
                <?php endif; ?>
                <div>
                    <!-- Optionnel : lien vers action upload (si vous implémentez une page d'upload) -->
                    <!-- <a href="/employes/<?= $e['id_employe'] ?>/edit-photo" class="btn btn-sm btn-outline-secondary">Mettre à jour</a> -->
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="section-title">Poste & mobilité</h5>
                <?php if (!empty($postes)): ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($postes as $p): ?>
                            <li class="mb-2">
                                <div class="field-value">
                                    <strong><?= htmlspecialchars($p['id_poste']) ?> - <?= htmlspecialchars($p['nom_poste'] ?? '') ?></strong>
                                    <?php if (!empty($p['motif'])): ?> 
                                        <span class="badge bg-light text-dark ms-1"><?= htmlspecialchars($p['motif']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">
                                    <?= !empty($p['date_debut']) ? date('d/m/Y', strtotime($p['date_debut'])) : '' ?>
                                    <?= !empty($p['date_fin']) ? ' → ' . date('d/m/Y', strtotime($p['date_fin'])) : '' ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted mb-0">Aucun historique de poste.</p>
                <?php endif; ?>

                <div class="mt-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPosteModal">Ajouter historique poste</button>
                </div>
            </div>

            <!-- Modal Add Poste History -->
            <div class="modal fade" id="addPosteModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form class="modal-content" method="POST" action="/employes/<?= $e['id_employe'] ?>/poste/ajouter">
                  <div class="modal-header">
                    <h5 class="modal-title">Ajouter historique poste</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Poste</label>
                        <select name="id_poste" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($postes_list ?? [] as $pt): ?>
                                <option value="<?= $pt['id_poste'] ?>"><?= htmlspecialchars($pt['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Département</label>
                        <select name="id_departement" class="form-select">
                            <option value="">-- (laisser vide pour conserver) --</option>
                            <?php foreach ($departements_list ?? [] as $d): ?>
                                <option value="<?= $d['id_departement'] ?>"><?= htmlspecialchars($d['nom_departement']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_debut" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date fin (optionnel)</label>
                        <input type="date" name="date_fin" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motif</label>
                        <input name="motif" class="form-control" placeholder="Promotion, mobilité, intérim...">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="apply_current" name="apply_to_current">
                        <label class="form-check-label" for="apply_current">Appliquer ce poste au contrat courant</label>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary">Ajouter</button>
                  </div>
                </form>
              </div>
            </div>

            <div class="card">
                <h5 class="section-title">Documents RH</h5>
                <?php if (!empty($documents)): ?>
                    <ul class="list-unstyled">
                        <?php foreach ($documents as $doc): ?>
                            <li class="mb-2">
                                <a href="<?= htmlspecialchars($doc['chemin_document']) ?>" target="_blank" class="d-flex align-items-center text-decoration-none">
                                    <i data-feather="file-text" class="me-2" style="width: 16px; height: 16px;"></i>
                                    <?= htmlspecialchars($doc['nom_document']) ?>
                                </a>
                                <div>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($doc['type_document'] ?? '') ?> — 
                                        <?= !empty($doc['uploaded_at']) ? date('d/m/Y H:i', strtotime($doc['uploaded_at'])) : '' ?>
                                    </small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Aucun document enregistré.</p>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<!-- ensure Bootstrap JS + Feather icons are loaded so modals and icons work -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
      feather.replace();
      // Debug helper: log modal form submissions
      document.addEventListener('submit', function(e){
        if (e.target.closest('.modal')) {
          console.log('Modal form submit ->', e.target.action);
        }
      });
    </script>
</body>
</html>