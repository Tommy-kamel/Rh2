<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche employé — Lecture seule</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #2c3e50;
            --accent-color: #9b59b6;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            background: #f8fafc;
        }

        .main-header {
            background: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .content-wrapper {
            padding: 2rem;
            max-width: 80%;
            margin: 0 auto;
        }
        
        .container {
            max-width: 100%;
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            padding: 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid #f0f0f0;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #e8eaed;
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
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .field-value {
            color: #1e293b;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0;
            padding: 0;
            border-bottom: none;
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
            padding: 10px 20px;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }
        
        .btn-danger {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        }
        
        .btn-secondary {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: var(--transition);
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            background-color: #e2e8f0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }
        
        .status-active {
            background-color: #d1f4e0;
            color: #0d6832;
            border: 1px solid #a8e6c8;
        }
        
        .status-inactive {
            background-color: #fee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .employee-header {
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
            padding: 2rem;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border-radius: 12px;
            border: 1px solid #e8eaed;
        }
        
        .employee-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 2rem;
            border: 4px solid var(--primary-color);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }
        
        .employee-name {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            color: var(--secondary-color);
        }
        
        .employee-id {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }
        
        .section-title {
            position: relative;
            padding-left: 0;
            margin-bottom: 2rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e8eaed;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            height: 2px;
            width: 60px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        
        .info-item {
            background-color: #ffffff;
            padding: 1.25rem;
            border-radius: 10px;
            border: 1px solid #e8eaed;
            transition: var(--transition);
        }
        
        .info-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .employee-header {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem;
            }
            
            .employee-avatar {
                margin-right: 0;
                margin-bottom: 1.5rem;
            }
            
            .content-wrapper {
                max-width: 95%;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<?php $e = $employee ?? []; ?>
<div class="app-container">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    
    <main class="main-content">
        <header class="main-header">
            <h1 class="page-title">
                <i data-feather="user"></i>
                Fiche employé — <?= htmlspecialchars($e['prenom'].' '.$e['nom']) ?>
            </h1>
        </header>
        
        <div class="content-wrapper">
            <div class="container">
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
        </div>
    </main>
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