// Pagination, recherche et filtres pour la liste complète des congés en attente
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('congesTable');
    if (!table) {
        console.error('Table congesTable non trouvée');
        return;
    }

    const tbody = table.querySelector('tbody');
    const searchInput = document.getElementById('searchTable');
    const filterDepartement = document.getElementById('filterDepartement');
    const filterType = document.getElementById('filterType');
    const btnResetFilters = document.getElementById('btnResetFilters');
    const paginationContainer = document.getElementById('pagination');
    
    if (!tbody || !paginationContainer) {
        console.error('Éléments requis non trouvés');
        return;
    }
    
    const rowsPerPage = 10;
    let currentPage = 1;
    let allRows = [];
    let filteredRows = [];

    // Initialiser les lignes
    function initRows() {
        allRows = Array.from(tbody.querySelectorAll('tr')).filter(row => {
            return !row.querySelector('td[colspan]');
        });
        console.log('Nombre de lignes trouvées:', allRows.length);
        filteredRows = [...allRows];
        updateTable();
    }

    // Fonction de recherche et filtrage
    function applyFilters() {
        const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const deptFilter = filterDepartement ? filterDepartement.value.toLowerCase() : '';
        const typeFilter = filterType ? filterType.value.toLowerCase() : '';
        
        filteredRows = allRows.filter(row => {
            const cells = row.querySelectorAll('td');
            
            // Recherche textuelle
            let matchesSearch = true;
            if (searchQuery !== '') {
                matchesSearch = Array.from(cells).some(cell => {
                    return cell.textContent.toLowerCase().includes(searchQuery);
                });
            }
            
            // Filtre département
            let matchesDept = true;
            if (deptFilter !== '') {
                const deptCell = cells[0]; // Première colonne = département
                matchesDept = deptCell && deptCell.textContent.toLowerCase().includes(deptFilter);
            }
            
            // Filtre type
            let matchesType = true;
            if (typeFilter !== '') {
                const typeCell = cells[3]; // Quatrième colonne = type
                matchesType = typeCell && typeCell.textContent.toLowerCase().includes(typeFilter);
            }
            
            return matchesSearch && matchesDept && matchesType;
        });
        
        currentPage = 1;
        updateTable();
    }

    // Mettre à jour l'affichage du tableau
    function updateTable() {
        // Cacher toutes les lignes
        allRows.forEach(row => row.style.display = 'none');

        // Calculer les indices
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageRows = filteredRows.slice(start, end);

        // Afficher les lignes de la page actuelle
        pageRows.forEach(row => row.style.display = '');

        // Mettre à jour les infos de pagination
        updatePaginationInfo(start, end);
        
        // Générer les boutons de pagination
        generatePagination();

        // Réappliquer feather icons pour les boutons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Mettre à jour les informations de pagination
    function updatePaginationInfo(start, end) {
        const currentStartEl = document.getElementById('currentStart');
        const currentEndEl = document.getElementById('currentEnd');
        const totalRowsEl = document.getElementById('totalRows');
        const totalRowsInfoEl = document.getElementById('totalRowsInfo');
        
        if (currentStartEl) currentStartEl.textContent = filteredRows.length > 0 ? start + 1 : 0;
        if (currentEndEl) currentEndEl.textContent = Math.min(end, filteredRows.length);
        if (totalRowsEl) totalRowsEl.textContent = filteredRows.length;
        if (totalRowsInfoEl) totalRowsInfoEl.textContent = filteredRows.length;
    }

    // Générer les boutons de pagination
    function generatePagination() {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        paginationContainer.innerHTML = '';

        // Ne pas afficher les boutons si une seule page ou aucune donnée
        if (totalPages <= 1) return;

        // Bouton Précédent
        const prevBtn = createPaginationButton('Précédent', currentPage > 1, () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable();
            }
        });
        paginationContainer.appendChild(prevBtn);

        // Boutons de pages
        const maxButtons = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxButtons - 1);

        if (endPage - startPage < maxButtons - 1) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }

        if (startPage > 1) {
            paginationContainer.appendChild(createPaginationButton('1', true, () => {
                currentPage = 1;
                updateTable();
            }));
            
            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.className = 'pagination-dots';
                dots.textContent = '...';
                paginationContainer.appendChild(dots);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = createPaginationButton(i.toString(), true, () => {
                currentPage = i;
                updateTable();
            }, i === currentPage);
            paginationContainer.appendChild(pageBtn);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'pagination-dots';
                dots.textContent = '...';
                paginationContainer.appendChild(dots);
            }
            
            paginationContainer.appendChild(createPaginationButton(totalPages.toString(), true, () => {
                currentPage = totalPages;
                updateTable();
            }));
        }

        // Bouton Suivant
        const nextBtn = createPaginationButton('Suivant', currentPage < totalPages, () => {
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();
            }
        });
        paginationContainer.appendChild(nextBtn);
    }

    // Créer un bouton de pagination
    function createPaginationButton(text, enabled, onClick, isActive = false) {
        const btn = document.createElement('button');
        btn.textContent = text;
        btn.className = 'pagination-btn' + (isActive ? ' active' : '');
        btn.disabled = !enabled;
        
        if (enabled && onClick) {
            btn.addEventListener('click', onClick);
        }
        
        return btn;
    }

    // Écouteurs d'événements pour la recherche et les filtres
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    
    if (filterDepartement) {
        filterDepartement.addEventListener('change', applyFilters);
    }
    
    if (filterType) {
        filterType.addEventListener('change', applyFilters);
    }
    
    if (btnResetFilters) {
        btnResetFilters.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (filterDepartement) filterDepartement.value = '';
            if (filterType) filterType.value = '';
            applyFilters();
        });
    }

    // Initialiser
    initRows();
});
