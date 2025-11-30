<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pointage de Départ - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .employe-card { 
            border: 1px solid #e0e0e0; 
            border-radius: 8px;
            padding: 15px; 
            margin-bottom: 15px; 
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .info-arrivee { 
            background: #f8f9fa; 
            padding: 12px; 
            margin-bottom: 12px; 
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        .heure-actuelle { margin-bottom: 10px; font-weight: bold; }
        .btn-depart { background: #28a745; color: white; border: none; }
        .btn-tous { background: #17a2b8; color: white; border: none; }
        .heure-input {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            margin-right: 10px;
        }
        .employe-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .employe-details {
            flex: 1;
        }
        .employe-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .pointage-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .pointage-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .aucun-employe { 
            padding: 40px; 
            text-align: center; 
            background: #f8f9fa; 
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            margin: 20px 0;
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
                        <i data-feather="check-circle" style="width: 48px; height: 48px; color: #28a745; margin-bottom: 15px;"></i>
                        <h3>Aucun employé en attente de départ</h3>
                        <p class="text-muted">Tous les employés ont déjà pointé leur départ aujourd'hui.</p>
                        <a href="/pointage/arrivee" class="btn btn-outline-primary">
                            <i data-feather="arrow-left"></i> Retour au pointage d'arrivée
                        </a>
                    </div>
                <?php else: ?>
                    <div class="pointage-header">
                        <div>
                            <h2><?= date('d/m/Y') ?></h2>
                            <p class="text-muted">Saisissez l'heure de départ pour les employés arrivés</p>
                        </div>
                        <div class="pointage-controls">
                            <button type="button" class="btn btn-tous" onclick="pointerTousLesDeparts()">
                                <i data-feather="check-circle"></i> Pointer tous les départs
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="actualiserHeures()">
                                <i data-feather="refresh-cw"></i> Actualiser les heures
                            </button>
                            <a href="/pointage/arrivee" class="btn btn-outline-primary">
                                <i data-feather="arrow-left"></i> Retour à l'arrivée
                            </a>
                        </div>
                    </div>
                    
                    <div class="employes-list">
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
                                        <h4><?= htmlspecialchars($employe['prenom'].' '.$employe['nom']) ?></h4>
                                        <p class="text-muted"><?= htmlspecialchars($employe['nom_poste']) ?></p>
                                    </div>
                                </div>

                                <div class="info-arrivee">
                                    <div><strong>Arrivée:</strong> <?= date('H:i', strtotime($heureArrivee)) ?> le <?= date('d/m/Y', strtotime($heureArrivee)) ?></div>
                                    <div><strong>Horaire de fin référence:</strong> <?= substr($employe['heure_fin'],0,5) ?></div>
                                </div>

                                <div class="heure-actuelle">Heure actuelle: <span id="heureActuelle_<?= $employe['id_employe'] ?>"><?= $heureActuelle ?></span></div>
                                
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
                                    
                                    <button type="button" 
                                            class="btn btn-depart" 
                                            onclick="pointerDepartEmploye(<?= $employe['id_employe'] ?>)" 
                                            id="btn_<?= $employe['id_employe'] ?>">
                                        <i data-feather="log-out"></i> Pointer le départ
                                    </button>
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
                    btn.style.background = '#17a2b8';
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

            fetch('/pointage/depart/multiple', {
                method: 'POST',
                headers: { 'Content-Type':'application/json' },
                body: JSON.stringify({ departs: departs })
            })
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success') {
                    alert('Tous les départs ont été pointés!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Erreur lors du pointage multiple des départs.');
                }
            })
            .catch(e => {
                alert('Erreur de connexion lors du pointage multiple.');
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