<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports de Performance - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .card-rapport {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            height: 100%;
        }
        .card-rapport:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .card-icon {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-individuel {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .btn-rapport {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .filter-section {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 30px;
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
                    Rapports de Performance
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Filtres -->
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label for="periode" class="form-label"><strong>Période d'analyse :</strong></label>
                            <select class="form-select" id="periode" name="periode">
                                <option value="mensuelle">Mensuelle</option>
                                <option value="trimestrielle">Trimestrielle</option>
                                <option value="annuelle">Annuelle</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="select-employe" class="form-label"><strong>Sélectionner un employé :</strong></label>
                            <select class="form-select" id="select-employe">
                                <option value="">Choisir un employé...</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="genererRapport()" id="btn-generer-rapport" disabled>
                                <i data-feather="play"></i> Générer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Carte Rapport Individuel -->
                <div class="row justify-content-center">
                    <div class="col-md-6 mb-4">
                        <div class="card-rapport p-4 text-center">
                            <div class="card-icon icon-individuel">
                                <i data-feather="user" style="width: 35px; height: 35px; color: white;"></i>
                            </div>
                            <h3>Rapport Individuel</h3>
                            <p class="text-muted">Rapport détaillé de performance pour un employé</p>
                            
                            <div class="mt-4">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-rapport" onclick="genererRapport()" id="btn-rapport-individuel" disabled>
                                        <i data-feather="eye"></i> Voir le rapport
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i data-feather="info" class="me-2"></i>
                                <div>
                                    <strong>Comment utiliser les rapports :</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Sélectionnez un employé et une période d'analyse</li>
                                        <li>Cliquez sur "Voir le rapport" pour accéder au rapport détaillé</li>
                                        <li>Le rapport inclura le score global, les détails par critère, et des recommandations personnalisées</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        feather.replace();
        
        let employes = [];

        // Charger la liste des employés au démarrage
        document.addEventListener('DOMContentLoaded', function() {
            chargerListeEmployes();
        });

        function chargerListeEmployes() {
            fetch('/scoring/api/employes')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        employes = data.employes;
                        const select = document.getElementById('select-employe');
                        
                        // Ajouter les employés
                        data.employes.forEach(employe => {
                            const option = document.createElement('option');
                            option.value = employe.id_employe;
                            option.textContent = `${employe.prenom} ${employe.nom} - ${employe.poste}`;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        }

        // Gestion de la sélection d'employé
        document.getElementById('select-employe').addEventListener('change', function() {
            const employeSelectionne = this.value;
            const btnGenerer = document.getElementById('btn-generer-rapport');
            const btnVoir = document.getElementById('btn-rapport-individuel');
            
            btnGenerer.disabled = !employeSelectionne;
            btnVoir.disabled = !employeSelectionne;
        });

        function genererRapport() {
            const idEmploye = document.getElementById('select-employe').value;
            const periode = document.getElementById('periode').value;
            
            if (!idEmploye) {
                alert('Veuillez sélectionner un employé');
                return;
            }

            // Rediriger vers la page de rapport individuel
            window.location.href = `/scoring/rapport/individuel?id_employe=${idEmploye}&periode=${periode}`;
        }

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
    </script>
</body>
</html>