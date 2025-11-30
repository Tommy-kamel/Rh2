<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postes libres et suggestions de formation</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f7fafc;
            color: var(--text-primary);
            line-height: 1.6;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 0;
            font-weight: 700;
            font-size: 28px;
            color: var(--dark-color);
        }

        .page-title i {
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: end;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-role {
            font-size: 0.85rem;
            background: var(--primary-color) !important;
        }

        .content {
            padding: 0;
        }

        h2 {
            margin-bottom: 25px;
            color: var(--dark-color);
            font-weight: 700;
            font-size: 24px;
        }

        h3 {
            margin-bottom: 20px;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
            display: inline-block;
        }

        /* Section Postes en cartes */
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .post-card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .post-title {
            font-weight: 700;
            font-size: 18px;
            color: var(--primary-color);
            margin: 0;
            flex: 1;
        }

        .post-department {
            background: var(--light-color);
            color: var(--text-secondary);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .post-dates {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }

        .date-item {
            display: flex;
            flex-direction: column;
        }

        .date-label {
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 0.75rem;
        }

        .date-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .competences-section {
            margin-bottom: 20px;
            flex: 1;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .competences-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .competence-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .competence-item:last-child {
            border-bottom: none;
        }

        .competence-name {
            font-weight: 500;
        }

        .competence-level {
            color: var(--primary-color);
            font-weight: 600;
        }

        .post-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
            align-items: center;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* Bouton Matcher - Mise en avant */
        .btn-match {
            background: linear-gradient(135deg, var(--success-color), #38b2ac);
            border: none;
            color: white;
            padding: 10px 16px;
            font-weight: 600;
            flex: 2;
            box-shadow: 0 2px 8px rgba(76, 201, 240, 0.3);
        }

        .btn-match:hover {
            background: linear-gradient(135deg, #38b2ac, var(--success-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 201, 240, 0.4);
        }

        /* Bouton Supprimer - Réduit et discret */
        .btn-delete {
            background: rgba(247, 37, 133, 0.1);
            border: 1px solid rgba(247, 37, 133, 0.3);
            color: var(--danger-color);
            padding: 6px 10px;
            font-size: 0.75rem;
            flex: 1;
            max-width: 80px;
        }

        .btn-delete:hover {
            background: rgba(247, 37, 133, 0.2);
            border-color: var(--danger-color);
            transform: translateY(-1px);
        }

        /* Section Historique */
        .table-responsive {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 40px;
            border: 1px solid var(--border-color);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 15px 12px;
            cursor: pointer;
            position: relative;
        }

        .table th:hover {
            background-color: var(--secondary-color);
        }

        .table th .sort-icon {
            margin-left: 5px;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .table th:hover .sort-icon {
            opacity: 1;
        }

        .table th.sorted-asc .sort-icon,
        .table th.sorted-desc .sort-icon {
            opacity: 1;
        }

        .table th.sorted-asc .sort-icon::after {
            content: "↑";
        }

        .table th.sorted-desc .sort-icon::after {
            content: "↓";
        }

        .table td {
            padding: 12px 12px;
            vertical-align: middle;
            font-size: 0.9rem;
            border-color: var(--border-color);
        }

        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        /* Recherche et actions */
        .search-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border: 1px solid var(--border-color);
        }

        .search-box {
            flex: 1;
            max-width: 400px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 10px 15px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
        }

        /* Modal styles */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }

        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-secondary);
            transition: color 0.2s ease;
        }

        .close-modal:hover {
            color: var(--danger-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .posts-grid {
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .posts-grid {
                grid-template-columns: 1fr;
            }
            
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: none;
            }
            
            .btn-group {
                width: 100%;
                justify-content: stretch;
            }
            
            .btn-group .btn {
                flex: 1;
            }
            
            .post-actions {
                flex-direction: column;
            }
        }

        /* Badge pour statut */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .status-active {
            background: rgba(76, 201, 240, 0.2);
            color: var(--success-color);
        }

        .status-expired {
            background: rgba(247, 37, 133, 0.2);
            color: var(--danger-color);
        }
    </style>
</head>

<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="briefcase"></i>
                    Postes libres et suggestions de formation
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role badge badge-primary">Ressources Humaines</span>
                </div>
            </header>

            <div class="content">
                <h2>Gestion des postes libres</h2>

                <!-- Section Postes actuels en cartes -->
                <h3>Postes actuels à matcher</h3>

                <div class="search-container">
                    <div class="search-box">
                        <input type="text" id="searchCurrent" class="form-control" placeholder="Rechercher un poste...">
                    </div>
                    <div class="btn-group">
                        <button id="openAddModal" class="btn btn-primary">
                            <i data-feather="plus"></i> Ajouter un poste libre
                        </button>
                    </div>
                </div>

                <div class="posts-grid" id="currentPosts">
                    <?php foreach ($postesActuels as $p): 
                        $isActive = strtotime($p['date_expiration']) > time();
                    ?>
                    <div class="post-card" data-post-name="<?= strtolower($p['nom_poste']) ?>">
                        <div class="post-header">
                            <h3 class="post-title"><?= htmlspecialchars($p['nom_poste']) ?></h3>
                            <span class="status-badge <?= $isActive ? 'status-active' : 'status-expired' ?>">
                                <?= $isActive ? 'Actif' : 'Expiré' ?>
                            </span>
                        </div>
                        
                        <div class="post-department"><?= htmlspecialchars($p['nom_departement']) ?></div>
                        
                        <div class="post-dates">
                            <div class="date-item">
                                <span class="date-label">Publication</span>
                                <span class="date-value"><?= $p['date_publication'] ?></span>
                            </div>
                            <div class="date-item">
                                <span class="date-label">Expiration</span>
                                <span class="date-value"><?= $p['date_expiration'] ?></span>
                            </div>
                        </div>
                        
                        <div class="competences-section">
                            <div class="section-title">
                                <i data-feather="award" width="16" height="16"></i>
                                Compétences requises
                            </div>
                            <ul class="competences-list">
                                <?php foreach ($p['competences'] as $c): ?>
                                    <li class="competence-item">
                                        <span class="competence-name"><?= htmlspecialchars($c['nom_competence']) ?></span>
                                        <span class="competence-level">Niv. <?= $c['niveau_requis'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="post-actions">
                            <form method="POST" action="/matching/postes/delete/<?= $p['id_poste_libre'] ?>" style="display:inline; flex:1;">
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Marquer comme expiré ?')">
                                    <i data-feather="trash-2" width="12" height="12"></i> Supprimer
                                </button>
                            </form>
                            <a href="/matching/poste/<?= $p['id_poste'] ?>" class="btn btn-match">
                                <i data-feather="users" width="16" height="16"></i> Matcher
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Section Historique -->
                <h3>Historique des postes libérés</h3>

                <div class="search-container">
                    <div class="search-box">
                        <input type="text" id="searchHistory" class="form-control" placeholder="Rechercher dans l'historique...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table" id="historiqueTable">
                        <thead>
                            <tr>
                                <th data-sort="poste">Poste <span class="sort-icon"></span></th>
                                <th data-sort="publication">Date publication <span class="sort-icon"></span></th>
                                <th data-sort="expiration">Date expiration <span class="sort-icon"></span></th>
                                <th data-sort="departement">Département <span class="sort-icon"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historique as $h): ?>
                            <tr>
                                <td data-sort-value="<?= strtolower($h['nom_poste']) ?>">
                                    <?= htmlspecialchars($h['nom_poste']) ?>
                                </td>
                                <td data-sort-value="<?= $h['date_publication'] ?>">
                                    <?= $h['date_publication'] ?>
                                </td>
                                <td data-sort-value="<?= $h['date_expiration'] ?>">
                                    <?= $h['date_expiration'] ?>
                                </td>
                                <td data-sort-value="<?= strtolower($h['nom_departement']) ?>">
                                    <?= htmlspecialchars($h['nom_departement']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Ajouter Poste -->
    <div class="modal-backdrop" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un poste libre</h3>
                <button type="button" class="close-modal">&times;</button>
            </div>
            <form method="POST" action="/matching/postes/add">
                <div class="form-group">
                    <label>Poste</label>
                    <select name="id_poste" class="form-control" required>
                        <?php
                        $stmt = Flight::db()->prepare("SELECT id_poste, nom FROM poste ORDER BY nom");
                        $stmt->execute();
                        $postes = $stmt->fetchAll();
                        foreach ($postes as $poste):
                        ?>
                            <option value="<?= $poste['id_poste'] ?>"><?= htmlspecialchars($poste['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Département</label>
                    <select name="id_departement" class="form-control" required>
                        <?php
                        $stmt = Flight::db()->prepare("SELECT id_departement, nom_departement FROM departement ORDER BY nom_departement");
                        $stmt->execute();
                        $departements = $stmt->fetchAll();
                        foreach ($departements as $d):
                        ?>
                            <option value="<?= $d['id_departement'] ?>"><?= htmlspecialchars($d['nom_departement']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date publication</label>
                    <input type="date" name="date_publication" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Date expiration</label>
                    <input type="date" name="date_expiration" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Description du poste..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Ajouter le poste</button>
                    <button type="button" id="closeAddModal" class="btn btn-secondary">Annuler</button>
                </div>
            </form>
        </div>
        
    </div>

    <script>
        feather.replace();

        // Gestion du menu déroulant
        document.querySelectorAll('.has-submenu > .menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                const parent = link.parentElement;
                if (!parent.classList.contains('active')) {
                    e.preventDefault();
                    parent.classList.toggle('open');
                }
            });
        });

        // Recherche instantanée pour les cartes
        document.getElementById('searchCurrent').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.post-card').forEach(card => {
                const postName = card.getAttribute('data-post-name');
                const text = card.textContent.toLowerCase();
                card.style.display = (text.includes(q) || postName.includes(q)) ? '' : 'none';
            });
        });

        // Recherche instantanée pour l'historique
        document.getElementById('searchHistory').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#historiqueTable tbody tr').forEach(tr => {
                const txt = tr.textContent.toLowerCase();
                tr.style.display = txt.includes(q) ? '' : 'none';
            });
        });

        // Tri des colonnes pour l'historique
        function setupTableSorting(tableId) {
            document.querySelectorAll(`#${tableId} th[data-sort]`).forEach(header => {
                header.addEventListener('click', () => {
                    const table = header.closest('table');
                    const tbody = table.querySelector('tbody');
                    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
                    const sortKey = header.getAttribute('data-sort');
                    
                    // Réinitialiser les autres en-têtes
                    document.querySelectorAll(`#${tableId} th`).forEach(th => {
                        if (th !== header) {
                            th.classList.remove('sorted-asc', 'sorted-desc');
                        }
                    });

                    // Basculer entre ascendant, descendant et aucun tri
                    if (!header.classList.contains('sorted-asc') && !header.classList.contains('sorted-desc')) {
                        header.classList.add('sorted-asc');
                        sortTable(tbody, columnIndex, 'asc', sortKey);
                    } else if (header.classList.contains('sorted-asc')) {
                        header.classList.remove('sorted-asc');
                        header.classList.add('sorted-desc');
                        sortTable(tbody, columnIndex, 'desc', sortKey);
                    } else {
                        header.classList.remove('sorted-desc');
                    }
                });
            });
        }

        function sortTable(tbody, columnIndex, direction, sortKey) {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const cellA = a.cells[columnIndex];
                const cellB = b.cells[columnIndex];
                
                let valueA = cellA.getAttribute('data-sort-value') || cellA.textContent.trim();
                let valueB = cellB.getAttribute('data-sort-value') || cellB.textContent.trim();
                
                // Conversion pour les dates
                if (sortKey === 'publication' || sortKey === 'expiration') {
                    valueA = new Date(valueA);
                    valueB = new Date(valueB);
                }
                
                if (direction === 'asc') {
                    return valueA < valueB ? -1 : valueA > valueB ? 1 : 0;
                } else {
                    return valueA > valueB ? -1 : valueA < valueB ? 1 : 0;
                }
            });
            
            // Réorganiser les lignes
            rows.forEach(row => tbody.appendChild(row));
        }

        // Initialiser le tri pour le tableau historique
        setupTableSorting('historiqueTable');

        // Modal
        document.getElementById('openAddModal').addEventListener('click', function() {
            document.getElementById('addModal').style.display = 'flex';
        });

        document.getElementById('closeAddModal').addEventListener('click', function() {
            document.getElementById('addModal').style.display = 'none';
        });

        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('addModal').style.display = 'none';
        });

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    </script>

    <script src="/assets/js/rh-dashboard.js"></script>
</body>
</html>