<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Primes - Tous les Employés</title>

    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-total { color: #6c757d; }
        .stat-rendement { color: #28a745; }
        .stat-divers { color: #007bff; }
        .stat-montant { color: #fd7e14; }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .badge-rendement {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-divers {
            background-color: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .montant-prime {
            font-weight: bold;
            color: #28a745;
        }
        .export-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .export-btn:hover {
            background: #218838;
        }
        .filter-toggle {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .advanced-filters {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            /* border-left: 4px solid #007bff; */
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
        }
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: #f8f9fa;
        }
        .sort-arrow {
            margin-left: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="award"></i>
                    Liste des Primes
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Filtres principaux -->
                <div class="filter-section">
                    <button class="filter-toggle" id="toggleFilters">
                        <i data-feather="filter"></i> Filtres avancés
                    </button>
                    
                    <div class="row g-3 align-items-center" id="mainFilters">
                        <div class="col-md-3">
                            <label class="form-label"><strong>Employé :</strong></label>
                            <select name="employe" class="form-select" id="filterEmploye">
                                <option value="">Tous les employés</option>
                                <?php foreach ($employes as $e): ?>
                                    <option value="<?= $e['id_employe'] ?>">
                                        <?= htmlspecialchars($e['nom_complet']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label"><strong>Type :</strong></label>
                            <select class="form-select" id="filterType">
                                <option value="">Tous les types</option>
                                <option value="rendement">Rendement</option>
                                <option value="divers">Divers</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label"><strong>Période :</strong></label>
                            <input type="month" class="form-control" id="filterMonth">
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="applyFilters">
                                <i data-feather="search"></i> Appliquer
                            </button>
                        </div>
                    </div>

                    <!-- Filtres avancés -->
                    <div class="advanced-filters" id="advancedFilters" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Montant minimum :</label>
                                <input type="number" class="form-control" id="filterMinAmount" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Montant maximum :</label>
                                <input type="number" class="form-control" id="filterMaxAmount" placeholder="1000000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Recherche texte :</label>
                                <input type="text" class="form-control" id="filterSearch" placeholder="Motif, employé...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <?php
                $totalPrimes = count($primes);
                $primesRendement = count(array_filter($primes, fn($p) => $p['type'] === 'rendement'));
                $primesDivers = count(array_filter($primes, fn($p) => $p['type'] === 'diver'));
                $montantTotal = array_sum(array_column($primes, 'montant_prime'));
                ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number stat-total"><?= $totalPrimes ?></div>
                        <div class="text-muted">Total primes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number stat-rendement"><?= $primesRendement ?></div>
                        <div class="text-muted">Primes rendement</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number stat-divers"><?= $primesDivers ?></div>
                        <div class="text-muted">Primes divers</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number stat-montant"><?= number_format($montantTotal, 0, ',', ' ') ?></div>
                        <div class="text-muted">Montant total (FCFA)</div>
                    </div>
                </div>

<!-- Modal d'ajout de prime -->
<!-- Modal d'ajout de prime -->

            <form method="POST" action="/primes-global/ajouter" id="formAjouterPrime">
                <div class="modal-body">
                    <!-- Messages d'alerte -->
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i data-feather="alert-circle"></i>
                            <?php
                            $errors = [
                                'method_not_allowed' => 'Méthode non autorisée.',
                                'prime_existe_deja' => 'Cette prime existe déjà.',
                                'erreur_ajout' => 'Erreur lors de l\'ajout de la prime.',
                                'erreur_systeme' => 'Erreur système. Veuillez réessayer.'
                            ];
                            echo $errors[$_GET['error']] ?? 'Une erreur est survenue.';
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success']) && $_GET['success'] === 'prime_ajoutee'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-feather="check-circle"></i>
                            Prime ajoutée avec succès !
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <!-- Employé -->
                        <div class="col-md-6">
                            <label for="id_employe" class="form-label fw-bold">Employé <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_employe" name="id_employe" required>
                                <option value="">Sélectionner un employé</option>
                                <?php foreach ($employes_actifs as $emp): ?>
                                    <option value="<?= $emp['id_employe'] ?>">
                                        <?= htmlspecialchars(strtoupper($emp['nom']) . ' ' . ucfirst($emp['prenom'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un employé.</div>
                        </div>

                        <!-- Type de prime -->
                        <div class="col-md-6">
                            <label for="type_prime" class="form-label fw-bold">Type de prime <span class="text-danger">*</span></label>
                            <select class="form-select" id="type_prime" name="type_prime" required>
                                <option value="">Sélectionner un type</option>
                                <option value="rendement">Prime de rendement</option>
                                <option value="divers">Prime diverse</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un type de prime.</div>
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <label for="date_prime" class="form-label fw-bold">Date de la prime <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_prime" name="date_prime" 
                                   max="<?= date('Y-m-d') ?>" required>
                            <div class="invalid-feedback">Veuillez saisir une date valide.</div>
                        </div>

                        <!-- Montant -->
                        <div class="col-md-6">
                            <label for="montant_prime" class="form-label fw-bold">Montant (FCFA) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="montant_prime" name="montant_prime" 
                                       min="1" max="10000000" step="1" placeholder="50000" required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <div class="invalid-feedback">Veuillez saisir un montant valide.</div>
                        </div>

                        <!-- Motif -->
                        <div class="col-12">
                            <label for="motif" class="form-label fw-bold">Motif <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="motif" name="motif" rows="3" 
                                      placeholder="Décrivez le motif de cette prime..." 
                                      maxlength="255" required></textarea>
                            <div class="form-text">
                                <span id="motif_counter">0</span>/255 caractères
                            </div>
                            <div class="invalid-feedback">Veuillez saisir un motif.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="submit" class="btn btn-success"> -->
                        <!-- <i data-feather="save"></i> Enregistrer la prime -->
                    <!-- </button> -->
                    <input type="submit" class="btn btn-success" value="Enregistrer la prime">
                </div>
            </form>

<!-- Script pour la gestion du modal -->
<script>
// Gestion du compteur de caractères pour le motif
document.getElementById('motif').addEventListener('input', function() {
    const counter = document.getElementById('motif_counter');
    counter.textContent = this.value.length;
});

// Validation du formulaire
document.getElementById('formAjouterPrime').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    this.classList.add('was-validated');
});

// Réinitialiser le formulaire quand le modal est fermé
document.getElementById('ajouterPrimeModal').addEventListener('hidden.bs.modal', function() {
    const form = document.getElementById('formAjouterPrime');
    form.classList.remove('was-validated');
    form.reset();
    document.getElementById('motif_counter').textContent = '0';
});
</script>

<script>
    feather.replace();
</script>
                <!-- En-tête avec actions -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2>Liste des primes</h2>
                    <!-- <button class="export-btn" id="">
                        <a href="primePdf"> Exporter </a>
                    </button> -->
                </div>

                <!-- Tableau des primes -->
                <div class="table-responsive">
                    <table class="table table-hover" id="primesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="sortable" data-sort="date">Date <span class="sort-arrow">▼</span></th>
                                <th class="sortable" data-sort="employe">Employé <span class="sort-arrow">▼</span></th>
                                <th class="sortable" data-sort="type">Type <span class="sort-arrow">▼</span></th>
                                <th>Motif</th>
                                <th class="sortable" data-sort="montant">Montant <span class="sort-arrow">▼</span></th>
                            </tr>
                        </thead>
                        <tbody id="primesTableBody">
                            <?php if (empty($primes)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                                        <p class="mt-2 text-muted">Aucune prime trouvée</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($primes as $p): ?>
                                    <tr data-employe="<?= $p['id_employe'] ?>" 
                                        data-type="<?= $p['type'] ?>"
                                        data-montant="<?= $p['montant_prime'] ?>"
                                        data-date="<?= $p['date_prime'] ?>">
                                        <td><?= date('d/m/Y', strtotime($p['date_prime'])) ?></td>
                                        <td><strong><?= htmlspecialchars($p['employe_nom']) ?></strong></td>
                                        <td>
                                            <?php if ($p['type'] == 'rendement'): ?>
                                                <span class="badge-rendement">Rendement</span>
                                            <?php else: ?>
                                                <span class="badge-divers">Divers</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($p['motif']) ?></td>
                                        <td class="montant-prime"><?= number_format($p['montant_prime'], 0, ',', ' ') ?>,00 FCFA</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Pagination" class="mt-4">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- La pagination sera générée par JavaScript -->
                    </ul>
                </nav>
            </div>
        </main>
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

        // Variables globales
        let currentPage = 1;
        const itemsPerPage = 10;
        let currentSort = { field: 'date', direction: 'desc' };
        let allPrimes = Array.from(document.querySelectorAll('#primesTableBody tr')).filter(tr => tr.cells.length > 1);

        // Filtres avancés
        document.getElementById('toggleFilters').addEventListener('click', function() {
            const advancedFilters = document.getElementById('advancedFilters');
            const isVisible = advancedFilters.style.display === 'block';
            advancedFilters.style.display = isVisible ? 'none' : 'block';
            this.innerHTML = isVisible ? 
                '<i data-feather="filter"></i> Filtres avancés' : 
                '<i data-feather="chevron-up"></i> Masquer les filtres';
            feather.replace();
        });

        // Tri des colonnes
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const field = this.dataset.sort;
                const isCurrent = currentSort.field === field;
                
                if (isCurrent) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.field = field;
                    currentSort.direction = 'asc';
                }

                // Mise à jour des flèches
                document.querySelectorAll('.sort-arrow').forEach(arrow => {
                    arrow.innerHTML = '▼';
                });
                this.querySelector('.sort-arrow').innerHTML = currentSort.direction === 'asc' ? '▲' : '▼';

                sortAndFilterPrimes();
            });
        });

        // Application des filtres
        document.getElementById('applyFilters').addEventListener('click', sortAndFilterPrimes);

        // Fonction de tri et filtrage
        function sortAndFilterPrimes() {
            let filteredPrimes = [...allPrimes];

            // Filtre par employé
            const employeFilter = document.getElementById('filterEmploye').value;
            if (employeFilter) {
                filteredPrimes = filteredPrimes.filter(tr => tr.dataset.employe === employeFilter);
            }

            // Filtre par type
            const typeFilter = document.getElementById('filterType').value;
            if (typeFilter) {
                filteredPrimes = filteredPrimes.filter(tr => tr.dataset.type === typeFilter);
            }

            // Filtre par mois
            const monthFilter = document.getElementById('filterMonth').value;
            if (monthFilter) {
                filteredPrimes = filteredPrimes.filter(tr => {
                    const primeDate = new Date(tr.dataset.date);
                    const filterDate = new Date(monthFilter);
                    return primeDate.getFullYear() === filterDate.getFullYear() && 
                           primeDate.getMonth() === filterDate.getMonth();
                });
            }

            // Filtre par montant
            const minAmount = parseInt(document.getElementById('filterMinAmount').value) || 0;
            const maxAmount = parseInt(document.getElementById('filterMaxAmount').value) || Infinity;
            filteredPrimes = filteredPrimes.filter(tr => {
                const montant = parseInt(tr.dataset.montant);
                return montant >= minAmount && montant <= maxAmount;
            });

            // Filtre par recherche texte
            const searchFilter = document.getElementById('filterSearch').value.toLowerCase();
            if (searchFilter) {
                filteredPrimes = filteredPrimes.filter(tr => {
                    const text = tr.textContent.toLowerCase();
                    return text.includes(searchFilter);
                });
            }

            // Tri
            filteredPrimes.sort((a, b) => {
                let aValue, bValue;

                switch (currentSort.field) {
                    case 'date':
                        aValue = new Date(a.dataset.date);
                        bValue = new Date(b.dataset.date);
                        break;
                    case 'employe':
                        aValue = a.cells[1].textContent;
                        bValue = b.cells[1].textContent;
                        break;
                    case 'type':
                        aValue = a.dataset.type;
                        bValue = b.dataset.type;
                        break;
                    case 'montant':
                        aValue = parseInt(a.dataset.montant);
                        bValue = parseInt(b.dataset.montant);
                        break;
                    default:
                        return 0;
                }

                if (currentSort.direction === 'asc') {
                    return aValue > bValue ? 1 : -1;
                } else {
                    return aValue < bValue ? 1 : -1;
                }
            });

            displayPrimes(filteredPrimes);
            updateStats(filteredPrimes);
        }

        // Affichage des primes avec pagination
        function displayPrimes(primes) {
            const tbody = document.getElementById('primesTableBody');
            tbody.innerHTML = '';

            if (primes.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                            <p class="mt-2 text-muted">Aucune prime ne correspond aux critères</p>
                        </td>
                    </tr>
                `;
                feather.replace();
                return;
            }

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pagePrimes = primes.slice(startIndex, endIndex);

            pagePrimes.forEach(prime => {
                tbody.appendChild(prime);
            });

            updatePagination(primes.length);
        }

        // Mise à jour de la pagination
        function updatePagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            // Page précédente
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#">Précédent</a>`;
            prevLi.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    sortAndFilterPrimes();
                }
            });
            pagination.appendChild(prevLi);

            // Pages
            for (let i = 1; i <= totalPages; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage = i;
                    sortAndFilterPrimes();
                });
                pagination.appendChild(pageLi);
            }

            // Page suivante
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#">Suivant</a>`;
            nextLi.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    sortAndFilterPrimes();
                }
            });
            pagination.appendChild(nextLi);
        }

        // Mise à jour des statistiques
        function updateStats(primes) {
            const total = primes.length;
            const rendement = primes.filter(tr => tr.dataset.type === 'rendement').length;
            const divers = primes.filter(tr => tr.dataset.type === 'divers').length;
            const montantTotal = primes.reduce((sum, tr) => sum + parseInt(tr.dataset.montant), 0);

            document.querySelector('.stat-total').textContent = total;
            document.querySelector('.stat-rendement').textContent = rendement;
            document.querySelector('.stat-divers').textContent = divers;
            document.querySelector('.stat-montant').textContent = montantTotal.toLocaleString('fr-FR');
        }

        // Export des données
        document.getElementById('exportBtn').addEventListener('click', function() {
            // Simulation d'export - à adapter selon les besoins
            const filteredPrimes = [...document.querySelectorAll('#primesTableBody tr')].filter(tr => tr.cells.length > 1);
            const data = filteredPrimes.map(tr => ({
                date: tr.cells[0].textContent,
                employe: tr.cells[1].textContent,
                type: tr.cells[2].textContent,
                motif: tr.cells[3].textContent,
                montant: tr.cells[4].textContent
            }));

            // Ici vous pouvez implémenter l'export CSV ou Excel
            alert(`Export de ${data.length} primes préparé !`);
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            sortAndFilterPrimes();
        });
    </script>
</body>
</html>