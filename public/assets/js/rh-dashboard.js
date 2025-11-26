// Pagination et recherche pour le tableau des congés
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('congesTable');
    if (!table) {
        console.error('Table congesTable non trouvée');
        return;
    }

    const tbody = table.querySelector('tbody');
    const searchInput = document.getElementById('searchTable');
    const paginationContainer = document.getElementById('pagination');
    
    if (!tbody) {
        console.error('tbody non trouvé');
        return;
    }
    if (!paginationContainer) {
        console.error('pagination container non trouvé');
        return;
    }
    
    const rowsPerPage = 5;
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

    // Fonction de recherche
    function searchTable(query) {
        query = query.toLowerCase().trim();
        
        if (query === '') {
            filteredRows = [...allRows];
        } else {
            filteredRows = allRows.filter(row => {
                const cells = row.querySelectorAll('td');
                return Array.from(cells).some(cell => {
                    return cell.textContent.toLowerCase().includes(query);
                });
            });
        }
        
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
        document.getElementById('currentStart').textContent = filteredRows.length > 0 ? start + 1 : 0;
        document.getElementById('currentEnd').textContent = Math.min(end, filteredRows.length);
        document.getElementById('totalRows').textContent = filteredRows.length;
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

    // Écouteur pour la recherche
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchTable(e.target.value);
        });
    }

    // Initialiser
    initRows();
});

// Fonctions de gestion des congés
// function validerConge(idConge) {
//     if (confirm('Voulez-vous vraiment valider cette demande de congé ?')) {
//         // TODO: Implémenter l'appel AJAX pour valider
//         console.log('Valider congé:', idConge);
//         // Exemple d'appel AJAX :
//         // fetch('/rh/conges/valider', {
//         //     method: 'POST',
//         //     headers: { 'Content-Type': 'application/json' },
//         //     body: JSON.stringify({ id_conge: idConge })
//         // })
//         // .then(response => response.json())
//         // .then(data => {
//         //     if (data.success) {
//         //         location.reload();
//         //     }
//         // });
//     }
// }

// function refuserConge(idConge) {
//     if (confirm('Voulez-vous vraiment refuser cette demande de congé ?')) {
//         // TODO: Implémenter l'appel AJAX pour refuser
//         console.log('Refuser congé:', idConge);
//     }
// }

// function voirDetailsConge(idConge) {
//     // TODO: Implémenter l'affichage des détails dans une modale
//     console.log('Voir détails congé:', idConge);
// }

// Fonction pour basculer l'affichage du dropdown des notifications
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
    
    // Réinitialiser les icônes Feather après l'affichage
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

// Fermer le dropdown si on clique ailleurs
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('notifications-dropdown');
    const notificationIcon = event.target.closest('.notification-icon');
    
    if (dropdown && !notificationIcon) {
        dropdown.style.display = 'none';
    }
});
