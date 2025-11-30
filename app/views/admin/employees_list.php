<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Liste des employés</title>
<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/styles.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
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
    display: flex;
    justify-content: space-between;
    align-items: center;
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

.user-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.user-name {
    font-weight: 600;
    color: #1e293b;
}

.user-role {
    font-size: 0.875rem;
    color: #64748b;
}

.container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #1e293b;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

/* === Barre de recherche === */
.mb-3 {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
}

#searchInput {
    padding: 12px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s;
}

#searchInput:focus {
    outline: none;
    border-color: #2041d3ff;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

/* === Boutons === */
.btn {
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
    text-decoration: none;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #2563eb;
    color: white;
    font-weight: 500;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
}

.btn-sm {
    padding: 8px 15px;
    font-size: 0.85rem;
}

.btn-outline-primary {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-outline-primary:hover {
    background: #667eea;
    color: white;
}

.btn-outline-secondary {
    background: white;
    color: #666;
    border: 2px solid #ddd;
}

.btn-outline-secondary:hover {
    background: #f5f5f5;
    border-color: #999;
}

/* === Table === */
.table {
    width: 100%;
    border-radius: 10px;
    overflow: hidden;
}

.table thead {
    background: #1e293b;
}

.table thead th {
    color: white;
    padding: 15px;
    font-weight: 600;
    text-align: left;
}

.table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s;
}

.table tbody tr:hover {
    background: linear-gradient(to right, #f8f9ff, #fff);
    transform: scale(1.01);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.table tbody td {
    padding: 15px;
    color: #333;
}

/* === Photo === */
.table td img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
  
    object-fit: cover;
}

.table td div[style*="background"] {
    width: 40px;
    height: 40px;
    border-radius: 50% !important;
    /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; */
}

/* === Actions === */
.table td:last-child {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* === Responsive === */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }
    
    .container {
        padding: 20px;
    }
    
    h1 {
        font-size: 1.8rem;
    }
    
    .mb-3 {
        flex-direction: column;
    }
    
    #searchInput {
        width: 100%;
    }
    
    .table {
        font-size: 0.9rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 6px 10px;
    }
}
</style>

</head>
<body>
<div class="app-container">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    
    <main class="main-content">
        <header class="main-header">
            <h1 class="page-title">
                <i data-feather="users"></i>
                Liste des employés
            </h1>
            <div class="user-info">
                <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
            </div>
        </header>
        
        <div class="content-wrapper">
            <div class="container">
                <div class="mb-3 d-flex">
                    <input id="searchInput" class="form-control me-2" placeholder="Rechercher par nom, prénom, email, poste..." style="max-width:420px;">
                    <a href="/employes/ajouter" class="btn btn-primary">
                        <i data-feather="plus"></i>
                        Ajouter un employé
                    </a>
                </div>

    <table class="table table-hover" id="employeesTable">
        <thead><tr><th>Photo</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Téléphone</th><th>Poste</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($employees as $emp): ?>
            <tr>
                <td>
                    <?php if (!empty($emp['photo'])): ?>
                        <img src="<?= htmlspecialchars($emp['photo']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px">
                    <?php else: ?>
                        <div style="width:40px;height:40px;background:#f0f0f0;border-radius:4px"></div>
                    <?php endif; ?>
                </td>
                <td class="cell-name"><?= htmlspecialchars($emp['nom']) ?></td>
                <td class="cell-prenom"><?= htmlspecialchars($emp['prenom']) ?></td>
                <td class="cell-email"><?= htmlspecialchars($emp['email']) ?></td>
                <td><?= htmlspecialchars($emp['telephone'] ?? '-') ?></td>
                <td><?= htmlspecialchars($emp['nom_poste'] ?? '-') ?></td>
                <td>
                    <a href="/employes/<?= $emp['id_employe'] ?>" class="btn btn-sm btn-outline-primary">Profil</a>
                    <a href="/employes/<?= $emp['id_employe'] ?>/contrat" class="btn btn-sm btn-outline-secondary">Contrat</a>
                    <a href="/employes/<?= $emp['id_employe'] ?>/postes" class="btn btn-sm btn-outline-secondary">Postes</a>
                    <a href="/employes/<?= $emp['id_employe'] ?>/documents" class="btn btn-sm btn-outline-secondary">Documents</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();
    document.querySelectorAll('.has-submenu > .menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const parent = link.parentElement;
                const submenu = parent.querySelector('.submenu');
                // Fermer tous les autres sous-menus
                document.querySelectorAll('.submenu').forEach(sub => {
                    if (sub !== submenu) {
                        sub.classList.remove('show');
                        sub.parentElement.classList.remove('open');
                    }
                });
                // Toggle le sous-menu actuel
                submenu.classList.toggle('show');
                parent.classList.toggle('open');
            });
        });

        const input = document.getElementById('searchInput');
        const rows = Array.from(document.querySelectorAll('#employeesTable tbody tr'));
        input.addEventListener('input', e => {
            const q = e.target.value.toLowerCase().trim();
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
</body>
</html>