<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pointage d'Arrivée - Admin</title>
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
            border-color: #007bff;
        }

        .employe-card.pointe {
            border-left: 4px solid #28a745;
        }

        .employe-card.absent {
            border-left: 4px solid #dc3545;
        }

        .employe-card.a-pointer {
            border-left: 4px solid #ffc107;
        }
        
        .statut-badge { 
            font-weight: 600;
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .statut-pointe {
            background-color: #f8f9fa;
            color: #28a745;
            border: 1px solid #28a745;
        }

        .statut-absent {
            background-color: #f8f9fa;
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .statut-non-pointe {
            background-color: #f8f9fa;
            color: #856404;
            border: 1px solid #ffc107;
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
        
        .btn-pointer { 
            background: #007bff;
            color: white; 
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
            flex: 1;
        }

        .btn-pointer:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
        
        .btn-absent { 
            background: #d73d3dff;
            color: white; 
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
            flex: 1;
        }

        .btn-absent:hover {
            background: #560600ff;
            transform: translateY(-1px);
            color:white ;
        }
        
        .btn-tous { 
            background: #28a745;
            color: white; 
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-tous:hover {
            background: #1e7e34;
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

        .employe-horaire {
            font-size: 0.85rem;
            color: #495057;
            font-weight: 500;
            margin-top: 4px;
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
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.1);
            outline: none;
        }
        
        .badge-retard {
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 8px;
        }

        .alert-pointage {
            border-radius: 6px;
            border: none;
            padding: 12px 16px;
            margin-bottom: 0;
            font-weight: 500;
        }

        .alert-success {
            background: #f8fff9;
            color: #155724;
            border-left: 3px solid #28a745;
        }

        .alert-danger {
            background: #fff8f8;
            color: #721c24;
            border-left: 3px solid #dc3545;
        }

        .statut-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .indicator-pointe { background-color: #28a745; }
        .indicator-absent { background-color: #dc3545; }
        .indicator-a-pointer { background-color: #ffc107; }

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
            }
        }

        .content-wrapper {
            padding: 20px;
        }

        .main-header {
            margin-bottom: 0;
        }

        .card-divider {
            height: 1px;
            background: #e9ecef;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="clock"></i>
                    Pointage d'Arrivée
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <div class="pointage-header">
                    <div class="date-section">
                        <h2><?= date('d/m/Y') ?></h2>
                        <p>Saisissez l'heure d'arrivée pour chaque employé</p>
                    </div>
                    <div class="pointage-controls">
                        <button type="button" class="btn btn-tous" onclick="pointerTous()">
                            <i data-feather="check-circle"></i> Pointer tous
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="actualiserHeures()">
                            <i data-feather="refresh-cw"></i> Actualiser
                        </button>
                    </div>
                </div>
                
                <div class="employes-grid">
                    <?php foreach ($employes as $employe): 
                        $initiales = substr($employe['prenom'],0,1) . substr($employe['nom'],0,1);
                        $estDejaPointe = $employe['statut_pointage'] === 'deja_pointe';
                        $estAbsent = $employe['statut_pointage'] === 'absent';
                        $estNonPointe = $employe['statut_pointage'] === 'non_pointe';
                        $heureArrivee = $estDejaPointe ? $employe['date_heure_arrive'] : null;

                        $retardMinutes = 0;
                        if ($heureArrivee) {
                            $diff = strtotime($heureArrivee) - strtotime(date('Y-m-d').' '.$employe['heure_debut']);
                            if ($diff > 0) $retardMinutes = ceil($diff/60);
                        }
                        $heureActuelle = date('H:i');
                        
                        // Déterminer la classe CSS en fonction du statut
                        $cardClass = '';
                        if ($estDejaPointe) $cardClass = 'pointe';
                        elseif ($estAbsent) $cardClass = 'absent';
                        else $cardClass = 'a-pointer';
                    ?>
                        <div class="employe-card <?= $cardClass ?>">
                            <div class="employe-info">
                                <div class="employe-details">
                                    <div class="employe-nom"><?= htmlspecialchars($employe['prenom'].' '.$employe['nom']) ?></div>
                                    <div class="employe-poste"><?= htmlspecialchars($employe['nom_poste']) ?></div>
                                    <div class="employe-horaire">Horaire: <?= substr($employe['heure_debut'],0,5) ?></div>
                                </div>
                                <div class="statut-badge">
                                    <?php if ($estDejaPointe): ?>
                                        <span class="statut-pointe">
                                            <span class="statut-indicator indicator-pointe"></span>
                                            Déjà Pointé
                                        </span>
                                    <?php elseif ($estAbsent): ?>
                                        <span class="statut-absent">
                                            <span class="statut-indicator indicator-absent"></span>
                                            Absent
                                        </span>
                                    <?php else: ?>
                                        <span class="statut-non-pointe">
                                            <span class="statut-indicator indicator-a-pointer"></span>
                                            À Pointer
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($retardMinutes > 5): ?>
                                        <span class="badge-retard">Retard: <?= $retardMinutes ?> min</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-divider"></div>

                            <?php if ($estDejaPointe && $heureArrivee): ?>
                                <div class="alert alert-success alert-pointage">
                                    <strong>Heure d'arrivée:</strong> <?= date('H:i', strtotime($heureArrivee)) ?> 
                                    <br><small>le <?= date('d/m/Y', strtotime($heureArrivee)) ?></small>
                                </div>
                            <?php elseif ($estAbsent): ?>
                                <div class="alert alert-danger alert-pointage">
                                    <strong>Employé marqué absent pour aujourd'hui</strong>
                                </div>
                            <?php else: ?>
                                <div class="heure-actuelle">
                                    <i data-feather="clock"></i>
                                    Heure actuelle: <span id="heureActuelle_<?= $employe['id_employe'] ?>"><?= $heureActuelle ?></span>
                                </div>
                                <div class="employe-actions">
                                    <input type="time" class="heure-input" id="heure_<?= $employe['id_employe'] ?>" name="heure_<?= $employe['id_employe'] ?>" value="<?= $heureActuelle ?>" required>
                                    <input type="hidden" name="employe_<?= $employe['id_employe'] ?>" value="<?= $employe['id_employe'] ?>">
                                    
                                    <div class="action-group">
                                        <button type="button" class="btn btn-pointer" onclick="pointerEmploye(<?= $employe['id_employe'] ?>)" id="btn_<?= $employe['id_employe'] ?>">
                                            <i data-feather="check"></i> Pointer
                                        </button>
                                        
                                        <button type="button" class="btn btn-absent" onclick="marquerAbsentManuellement(<?= $employe['id_employe'] ?>)" id="btn_absent_<?= $employe['id_employe'] ?>">
                                            <i data-feather="x"></i> Absent
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
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

        // Fonctions pour le pointage
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

        function pointerEmploye(id) {
            const heure = document.getElementById(`heure_${id}`).value;
            const btn = document.getElementById(`btn_${id}`);
            if (!heure) return alert('Veuillez saisir une heure.');
            btn.disabled = true; 
            btn.innerHTML = '<i data-feather="loader"></i> Pointage...';

            fetch('/pointage/individuel', {
                method: 'POST',
                headers: { 'Content-Type':'application/json' },
                body: JSON.stringify({ 
                    id_employe: id, 
                    date_heure_arrive: new Date().toISOString().split('T')[0] + 'T' + heure 
                })
            })
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success'){ 
                    btn.innerHTML = '<i data-feather="check"></i> Pointé'; 
                    btn.classList.remove('btn-pointer');
                    btn.classList.add('btn-success');
                    setTimeout(() => location.reload(), 1500);
                } else { 
                    alert(d.message); 
                    btn.disabled = false; 
                    btn.innerHTML = '<i data-feather="check"></i> Pointer'; 
                }
            })
            .catch(e => {
                alert('Erreur de connexion'); 
                btn.disabled = false; 
                btn.innerHTML = '<i data-feather="check"></i> Pointer'; 
            });
        }

        function marquerAbsentManuellement(idEmploye) {
            if (!confirm('Marquer cet employé comme absent pour aujourd\'hui ?')) return;

            const btn = document.getElementById(`btn_absent_${idEmploye}`);
            btn.disabled = true;
            btn.innerHTML = '<i data-feather="loader"></i> Marquage...';

            fetch('/pointage/marquer-absent', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_employe: idEmploye })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    btn.innerHTML = '<i data-feather="check"></i> Absent';
                    btn.classList.remove('btn-absent');
                    btn.classList.add('btn-danger');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Erreur: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i data-feather="x"></i> Absent';
                }
            })
            .catch(error => {
                alert('Erreur de connexion');
                btn.disabled = false;
                btn.innerHTML = '<i data-feather="x"></i> Absent';
            });
        }

        function pointerTous() {
            const pointages = [];
            document.querySelectorAll('input[type="time"]').forEach(input => {
                const id = parseInt(input.id.replace('heure_',''));
                const heure = input.value;
                if (heure) {
                    pointages.push({
                        id_employe: id,
                        date_heure_arrive: new Date().toISOString().split('T')[0] + 'T' + heure
                    });
                }
            });
            
            if(pointages.length === 0) return alert('Aucun employé à pointer.');
            
            const btn = event.target;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i data-feather="loader"></i> Pointage en cours...';
            btn.disabled = true;
            
            fetch('/pointage/multiple', {
                method: 'POST',
                headers: { 'Content-Type':'application/json' },
                body: JSON.stringify({ pointages })
            })
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success') {
                    btn.innerHTML = '<i data-feather="check"></i> Tous pointés!'; 
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Erreur pointage multiple.');
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            })
            .catch(e => {
                alert('Erreur pointage multiple');
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