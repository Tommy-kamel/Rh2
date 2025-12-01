<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pointage de Départ - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .employes-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .employe-card { 
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px; 
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            position: relative;
        }

        .employe-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-color: #17a2b8;
        }

        .info-arrivee { 
            background: #f8f9fa; 
            padding: 15px; 
            margin-bottom: 15px; 
            border-radius: 6px;
            border-left: 4px solid #17a2b8;
        }
        
        .heure-actuelle { 
            margin-bottom: 12px; 
            font-weight: 500;
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .heure-actuelle i {
            color: #6c757d;
        }
        
        .btn-depart { 
            background: #28a745;
            color: white; 
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
            flex: 1;
        }

        .btn-depart:hover {
            background: #1e7e34;
            transform: translateY(-1px);
        }
        
        .btn-tous { 
            background: #17a2b8;
            color: white; 
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-tous:hover {
            background: #138496;
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            border-radius: 6px;
            padding: 12px 24px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-1px);
        }
        
        .employe-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .employe-details {
            flex: 1;
        }

        .employe-nom {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .employe-poste {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .employe-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .action-group {
            display: flex;
            gap: 10px;
            width: 100%;
        }
        
        .pointage-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .date-section h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .date-section p {
            margin: 5px 0 0 0;
            color: #6c757d;
        }
        
        .pointage-controls {
            display: flex;
            gap: 12px;
        }
        
        .heure-input {
            padding: 10px 12px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            width: 100%;
            background: white;
        }

        .heure-input:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 2px rgba(23,162,184,0.1);
            outline: none;
        }
        
        .aucun-employe { 
            padding: 60px 40px; 
            text-align: center; 
            background: #f8f9fa; 
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            margin: 20px 0;
        }

        .aucun-employe i {
            width: 64px;
            height: 64px;
            color: #28a745;
            margin-bottom: 20px;
        }

        .aucun-employe h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .info-item {
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .info-item strong {
            color: #2c3e50;
        }

        .card-divider {
            height: 1px;
            background: #e9ecef;
            margin: 15px 0;
        }

        @media (max-width: 768px) {
            .employes-grid {
                grid-template-columns: 1fr;
            }
            
            .pointage-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .pointage-controls {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        .content-wrapper {
            padding: 20px;
        }

        .main-header {
            margin-bottom: 0;
        }

        .info-arrivee-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="log-out"></i>
                    Pointage de Départ
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <?php if (empty($employes)): ?>
                    <div class="aucun-employe">
                        <i data-feather="check-circle"></i>
                        <h3>Aucun employé en attente de départ</h3>
                        <p class="text-muted">Tous les employés ont déjà pointé leur départ aujourd'hui.</p>
                        <a href="/pointage/arrivee" class="btn btn-outline-primary mt-3">
                            <i data-feather="arrow-left"></i> Retour au pointage d'arrivée
                        </a>
                    </div>
                <?php else: ?>
                    <div class="pointage-header">
                        <div class="date-section">
                            <h2><?= date('d/m/Y') ?></h2>
                            <p>Saisissez l'heure de départ pour les employés arrivés</p>
                        </div>
                        <div class="pointage-controls">
                            <button type="button" class="btn btn-tous" onclick="pointerTousLesDeparts()">
                                <i data-feather="check-circle"></i> Pointer tous les départs
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="actualiserHeures()">
                                <i data-feather="refresh-cw"></i> Actualiser
                            </button>
                            <a href="/pointage/arrivee" class="btn btn-outline-primary">
                                <i data-feather="arrow-left"></i> Retour à l'arrivée
                            </a>
                        </div>
                    </div>
                    
                    <div class="employes-grid">
                        <?php foreach ($employes as $employe): 
                            $initiales = substr($employe['prenom'],0,1) . substr($employe['nom'],0,1);
                            $heureArrivee = $employe['date_heure_arrive'];
                            $heureActuelle = date('H:i');
                            
                            // Calcul de la durée travaillée si départ pointé
                            $dureeTravaillee = '';
                            if ($employe['date_heure_depart']) {
                                $debut = new DateTime($employe['date_heure_arrive']);
                                $fin = new DateTime($employe['date_heure_depart']);
                                $interval = $debut->diff($fin);
                                $dureeTravaillee = $interval->format('%hh %im');
                            }
                        ?>
                            <div class="employe-card">
                                <div class="employe-info">
                                    <div class="employe-details">
                                        <div class="employe-nom"><?= htmlspecialchars($employe['prenom'].' '.$employe['nom']) ?></div>
                                        <div class="employe-poste"><?= htmlspecialchars($employe['nom_poste']) ?></div>
                                    </div>
                                </div>

                                <div class="info-arrivee">
                                    <div class="info-arrivee-content">
                                        <div class="info-item">
                                            <strong>Arrivée:</strong> <?= date('H:i', strtotime($heureArrivee)) ?> le <?= date('d/m/Y', strtotime($heureArrivee)) ?>
                                        </div>
                                        <div class="info-item">
                                            <strong>Horaire de fin référence:</strong> <?= substr($employe['heure_fin'],0,5) ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-divider"></div>

                                <div class="heure-actuelle">
                                    <i data-feather="clock"></i>
                                    Heure actuelle: <span id="heureActuelle_<?= $employe['id_employe'] ?>"><?= $heureActuelle ?></span>
                                </div>
                                
                                <div class="employe-actions">
                                    <input type="time" 
                                           class="heure-input" 
                                           id="heure_<?= $employe['id_employe'] ?>" 
                                           name="heure_<?= $employe['id_employe'] ?>" 
                                           value="<?= $heureActuelle ?>" 
                                           required>
                                    
                                    <input type="hidden" 
                                           name="employe_<?= $employe['id_employe'] ?>" 
                                           value="<?= $employe['id_employe'] ?>">
                                    
                                    <div class="action-group">
                                        <button type="button" 
                                                class="btn btn-depart" 
                                                onclick="pointerDepartEmploye(<?= $employe['id_employe'] ?>)" 
                                                id="btn_<?= $employe['id_employe'] ?>">
                                            <i data-feather="log-out"></i> Pointer le départ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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

        // Fonctions pour le pointage de départ
        function actualiserHeures() {
            const maintenant = new Date();
            const h = String(maintenant.getHours()).padStart(2,'0');
            const m = String(maintenant.getMinutes()).padStart(2,'0');
            const t = `${h}:${m}`;
            document.querySelectorAll('input[type="time"]').forEach(i => i.value = t);
            document.querySelectorAll('[id^="heureActuelle_"]').forEach(s => s.textContent = t);
            
            // Animation de rafraîchissement
            const btn = event.target;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i data-feather="check"></i> Actualisé';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
                feather.replace();
            }, 1000);
        }

        function pointerDepartEmploye(id) {
            const heure = document.getElementById(`heure_${id}`).value;
            const btn = document.getElementById(`btn_${id}`);
            
            if (!heure) {
                alert('Veuillez saisir une heure de départ.');
                return;
            }
            
            btn.disabled = true; 
            btn.innerHTML = '<i data-feather="loader"></i> Pointage...';

            const aujourdhui = new Date().toISOString().split('T')[0];
            const dateHeureDepart = `${aujourdhui}T${heure}`;

            fetch('/pointage/depart/individuel', {
                method: 'POST',
                headers: { 'Content-Type':'application/json' },
                body: JSON.stringify({ 
                    id_employe: id, 
                    date_heure_depart: dateHeureDepart 
                })
            })
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success'){ 
                    btn.innerHTML = '<i data-feather="check"></i> Départ pointé!';
                    btn.classList.remove('btn-depart');
                    btn.classList.add('btn-success');
                    setTimeout(() => location.reload(), 1500);
                } else { 
                    alert(d.message); 
                    btn.disabled = false; 
                    btn.innerHTML = '<i data-feather="log-out"></i> Pointer le départ'; 
                }
            })
            .catch(e => {
                alert('Erreur de connexion'); 
                btn.disabled = false; 
                btn.innerHTML = '<i data-feather="log-out"></i> Pointer le départ'; 
            });
        }

        function pointerTousLesDeparts() {
            const departs = [];
            document.querySelectorAll('input[type="time"]').forEach(input => {
                const id = parseInt(input.id.replace('heure_',''));
                const heure = input.value;
                
                if (heure) {
                    const aujourdhui = new Date().toISOString().split('T')[0];
                    const dateHeureDepart = `${aujourdhui}T${heure}`;
                    
                    departs.push({
                        id_employe: id,
                        date_heure_depart: dateHeureDepart
                    });
                }
            });

            if(departs.length === 0) {
                alert('Aucun départ à pointer.');
                return;
            }

            const btn = event.target;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i data-feather="loader"></i> Pointage en cours...';
            btn.disabled = true;

            fetch('/pointage/depart/multiple', {
                method: 'POST',
                headers: { 'Content-Type':'application/json' },
                body: JSON.stringify({ departs: departs })
            })
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success') {
                    btn.innerHTML = '<i data-feather="check"></i> Tous pointés!';
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Erreur lors du pointage multiple des départs.');
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            })
            .catch(e => {
                alert('Erreur de connexion lors du pointage multiple.');
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            actualiserHeures();
            setInterval(actualiserHeures, 60000);
            feather.replace();
        });
    </script>
</body>
</html>