<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Contrats - <?= htmlspecialchars($employee['prenom'].' '.$employee['nom']) ?></title>
<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/styles.css">
<style>
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
}
.container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
</style>
</head>
<body>
<div class="app-container">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    
    <main class="main-content">
        <header class="main-header">
            <h1 class="page-title">
                <i data-feather="file-text"></i>
                Contrats - <?= htmlspecialchars($employee['prenom'].' '.$employee['nom']) ?>
            </h1>
        </header>
        
        <div class="content-wrapper">
            <div class="container">
    <a href="/employes/<?= $employee['id_employe'] ?>" class="btn btn-light mb-3">&larr; Retour profil</a>
    <h2>Suivi des contrats — <?= htmlspecialchars($employee['prenom'].' '.$employee['nom']) ?></h2>

    <div class="mb-3">
        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createContractModal">Créer contrat</a>
    </div>

    <table class="table">
        <thead><tr><th>Type</th><th>Début</th><th>Fin</th><th>Période d'essai (jours)</th><th>Renouvellements</th><th>Salaire</th></tr></thead>
        <tbody>
        <?php if (!empty($contrats)): foreach ($contrats as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['type']) ?></td>
                <td><?= $c['date_debut'] ? date('d/m/Y', strtotime($c['date_debut'])) : '-' ?></td>
                <td><?= $c['date_fin'] ? date('d/m/Y', strtotime($c['date_fin'])) : 'En cours' ?></td>
                <td><?= htmlspecialchars($c['periode_essai_jours'] ?? 0) ?></td>
                <td><?= htmlspecialchars($c['renewal_count'] ?? $c['renewalment_count'] ?? 0) ?></td>
                <td><?= isset($c['salaire']) ? number_format($c['salaire'],2,',',' ') : '-' ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center text-muted">Aucun contrat</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
            </div>
        </div>

<!-- Modal create (simple) -->
<div class="modal fade" id="createContractModal" tabindex="-1"><div class="modal-dialog">
<form class="modal-content" method="POST" action="/employes/<?= $employee['id_employe'] ?>/contrat/creer">
  <div class="modal-header"><h5 class="modal-title">Créer contrat</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <div class="mb-2"><label>Salaire</label><input name="salaire" class="form-control" required></div>
    <div class="mb-2"><label>Date début</label><input type="date" name="date_debut" class="form-control" required></div>
    <div class="mb-2"><label>Date fin</label><input type="date" name="date_fin" class="form-control"></div>
    <div class="mb-2"><label>Type</label><select name="type" class="form-select"><option>CDI</option><option>CDD</option><option>Essai</option></select></div>
    <div class="mb-2"><label>Période d'essai (jours)</label><input type="number" name="periode_essai_jours" class="form-control" min="0" value="0"></div>
    <div class="mb-2"><label>Poste (id_poste)</label><input name="id_poste" class="form-control"></div>
    <div class="mb-2"><label>Département (id_departement)</label><input name="id_departement" class="form-control"></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary">Créer</button></div>
</form>
</div></div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>feather.replace();</script>

</body>
</html>