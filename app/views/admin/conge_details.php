<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du congé - RH</title>
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
                    <i data-feather="eye"></i>
                    Détails du congé
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

                <!-- Détails du congé -->
                <?php if (isset($details_conge) && $details_conge): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i data-feather="calendar"></i>
                                Congé #<?= htmlspecialchars($details_conge['id_conge']) ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informations de l'employé</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Nom:</strong></td>
                                            <td><?= htmlspecialchars($details_conge['nom']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Prénom:</strong></td>
                                            <td><?= htmlspecialchars($details_conge['prenom']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Département:</strong></td>
                                            <td><?= htmlspecialchars($details_conge['nom_departement']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Poste:</strong></td>
                                            <td><?= htmlspecialchars($details_conge['nom_poste']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Informations du congé</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Date de début:</strong></td>
                                            <td><?= htmlspecialchars($details_conge['date_debut']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de fin:</strong></td>
                                            <td><?= htmlspecialchars($details_conge['date_fin']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre de jours:</strong></td>
                                            <td><?= htmlspecialchars($details_conge['nb_jour']) ?> jours</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                <?php
                                                $type_conge = $details_conge['type'];
                                                $badge_class = 'secondary';
                                                if ($type_conge == 'annuel') $badge_class = 'success';
                                                elseif ($type_conge == 'maladie') $badge_class = 'warning';
                                                elseif ($type_conge == 'maternite') $badge_class = 'info';
                                                ?>
                                                <span class="badge bg-<?= $badge_class ?>">
                                                    <?= htmlspecialchars($type_conge) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <?php if (!empty($details_conge['raison'])): ?>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Raison</h6>
                                        <div class="border p-3 bg-light">
                                            <?= nl2br(htmlspecialchars($details_conge['raison'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Historique</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Date de soumission:</strong></td>
                                            <td><?= htmlspecialchars($details_conge['date_demande']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut actuel:</strong></td>
                                            <td>
                                                <?php
                                                $statut = $details_conge['status'];
                                                $statut_text = 'En attente';
                                                if ($statut == 0) {
                                                    $statut_text = 'Refusé';
                                                } elseif ($statut == 11) {
                                                    $statut_text = 'Validé département';
                                                } elseif ($statut == 21) {
                                                    $statut_text = 'Validé RH';
                                                }
                                                ?>
                                                <?= $statut_text ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i data-feather="alert-triangle"></i>
                        Aucun détail trouvé pour ce congé.
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        feather.replace();
    </script>
</body>
</html>