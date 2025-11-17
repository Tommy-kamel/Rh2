<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Liste des employés</title>
<!-- <link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/styles.css"> -->

<style>
    /* === Styles de base === */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
    min-height: 100vh;
    padding: 20px;
}

.container {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 1400px;
    margin: 0 auto;
}

/* === Titre === */
h1 {
    color: #333;
    font-size: 2.5rem;
    margin-bottom: 25px;
    font-weight: 700;
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
    /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
    background: #2041d3ff;
    color: white;
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
    /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
    background:black;
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

<div class="container py-4">
    <h1>Liste des employés</h1>
    <div class="mb-3 d-flex">
        <input id="searchInput" class="form-control me-2" placeholder="Rechercher par nom, prénom, email, poste..." style="max-width:420px;">
        <a href="/employes/ajouter" class="btn btn-primary">Ajouter</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace();
        // Recherche existante
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