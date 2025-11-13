<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le congé - RH</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="edit"></i>
                    Modifier le congé
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

                <!-- Formulaire de modification -->
                <?php if (isset($details_conge) && $details_conge): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i data-feather="calendar"></i>
                                Modifier le congé #<?= htmlspecialchars($details_conge['id_conge']) ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="/admin/conges/modifier/<?= $details_conge['id_conge'] ?>" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Informations de l'employé</h6>
                                        <div class="mb-3">
                                            <label class="form-label">Employé</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($details_conge['nom_employe'] . ' ' . $details_conge['prenom_employe']) ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Département</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($details_conge['nom_departement']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Informations du congé</h6>
                                        <div class="mb-3">
                                            <label for="date_debut" class="form-label">Date de début *</label>
                                            <input type="date" class="form-control" id="date_debut" name="date_debut"
                                                   value="<?= htmlspecialchars($details_conge['date_debut']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="date_fin" class="form-label">Date de fin *</label>
                                            <input type="date" class="form-control" id="date_fin" name="date_fin"
                                                   value="<?= htmlspecialchars($details_conge['date_fin']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="type_conge" class="form-label">Type de congé *</label>
                                            <select class="form-select" id="type_conge" name="type_conge" required>
                                                <option value="annuel" <?= $details_conge['type_conge'] == 'annuel' ? 'selected' : '' ?>>Congé annuel</option>
                                                <option value="maladie" <?= $details_conge['type_conge'] == 'maladie' ? 'selected' : '' ?>>Congé maladie</option>
                                                <option value="maternite" <?= $details_conge['type_conge'] == 'maternite' ? 'selected' : '' ?>>Congé maternité</option>
                                                <option value="autre" <?= $details_conge['type_conge'] == 'autre' ? 'selected' : '' ?>>Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="motif" class="form-label">Motif</label>
                                    <textarea class="form-control" id="motif" name="motif" rows="3"
                                              placeholder="Motif du congé (optionnel)"><?= htmlspecialchars($details_conge['motif'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="save"></i>
                                        Enregistrer les modifications
                                    </button>
                                    <a href="<?= ($_SESSION['id_departement'] ?? null) === 1 ? '/rh/dashboard' : '/dashboard' ?>" class="btn btn-secondary">
                                        <i data-feather="x"></i>
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i data-feather="alert-triangle"></i>
                        Aucun congé trouvé pour modification.
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        feather.replace();

        // Calcul automatique du nombre de jours
        function calculerNombreJours() {
            const dateDebut = new Date(document.getElementById('date_debut').value);
            const dateFin = new Date(document.getElementById('date_fin').value);

            if (dateDebut && dateFin && dateFin >= dateDebut) {
                const diffTime = Math.abs(dateFin - dateDebut);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                console.log('Nombre de jours calculé:', diffDays);
            }
        }

        // Écouteurs d'événements pour recalculer automatiquement
        document.getElementById('date_debut').addEventListener('change', calculerNombreJours);
        document.getElementById('date_fin').addEventListener('change', calculerNombreJours);
    </script>
</body>
</html>