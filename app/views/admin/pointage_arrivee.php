<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pointage d'Arrivée - Admin</title>
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
        .statut-badge { font-weight: bold; }
        .retard-info { color: #dc3545; }
        .heure-actuelle { margin-bottom: 10px; font-weight: bold; }
        .btn-pointer { background: #007bff; color: white; border: none; }
        .btn-absent { background: #dc3545; color: white; border: none; }
        .btn-tous { background: #28a745; color: white; border: none; }
        .statut-absent { color: #dc3545; font-weight: bold; }
        .statut-pointe { color: #28a745; font-weight: bold; }
        .statut-non-pointe { color: #ffc107; font-weight: bold; }
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
        .heure-input {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }
        .badge-retard {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
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
                    <div>
                        <h2><?= date('d/m/Y') ?></h2>
                        <p class="text-muted">Saisissez l'heure d'arrivée pour chaque employé</p>
                    </div>
                    <div class="pointage-controls">
                        <button type="button" class="btn btn-tous" onclick="pointerTous()">
                            <i data-feather="check-circle"></i> Pointer tous
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="actualiserHeures()">
                            <i data-feather="refresh-cw"></i> Actualiser les heures
                        </button>
                    </div>
                </div>
                
                <div class="employes-list">
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
                    ?>
                        <div class="employe-card">
                            <div class="employe-info">
                                <div class="employe-details">
                                    <h4><?= htmlspecialchars($employe['prenom'].' '.$employe['nom']) ?></h4>
                                    <p class="text-muted"><?= htmlspecialchars($employe['nom_poste']) ?> - Horaire: <?= substr($employe['heure_debut'],0,5) ?></p>
                                </div>
                                <div class="statut-badge">
                                    <?php if ($estDejaPointe): ?>
                                        <span class="statut-pointe">✅ Déjà Pointé</span>
                                    <?php elseif ($estAbsent): ?>
                                        <span class="statut-absent">🚫 Absent</span>
                                    <?php else: ?>
                                        <span class="statut-non-pointe">⏰ À Pointer</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($retardMinutes > 0): ?>
                                        <span class="badge-retard">Retard: <?= $retardMinutes ?> min</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($estDejaPointe && $heureArrivee): ?>
                                <div class="alert alert-success">
                                    Heure d'arrivée: <strong><?= date('H:i', strtotime($heureArrivee)) ?></strong> le <?= date('d/m/Y', strtotime($heureArrivee)) ?>
                                </div>
                            <?php elseif ($estAbsent): ?>
                                <div class="alert alert-danger">
                                    <strong>Employé marqué absent pour aujourd'hui</strong>
                                </div>
                            <?php else: ?>
                                <div class="heure-actuelle">Heure actuelle: <span id="heureActuelle_<?= $employe['id_employe'] ?>"><?= $heureActuelle ?></span></div>
                                <div class="employe-actions">
                                    <input type="time" class="heure-input" id="heure_<?= $employe['id_employe'] ?>" name="heure_<?= $employe['id_employe'] ?>" value="<?= $heureActuelle ?>" required>
                                    <input type="hidden" name="employe_<?= $employe['id_employe'] ?>" value="<?= $employe['id_employe'] ?>">
                                    
                                    <button type="button" class="btn btn-pointer" onclick="pointerEmploye(<?= $employe['id_employe'] ?>)" id="btn_<?= $employe['id_employe'] ?>">
                                        <i data-feather="check"></i> Pointer
                                    </button>
                                    
                                    <button type="button" class="btn btn-absent" onclick="marquerAbsentManuellement(<?= $employe['id_employe'] ?>)" id="btn_absent_<?= $employe['id_employe'] ?>">
                                        <i data-feather="x"></i> Marquer absent
                                    </button>
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
                    alert(data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Erreur: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i data-feather="x"></i> Marquer absent';
                }
            })
            .catch(error => {
                alert('Erreur de connexion');
                btn.disabled = false;
                btn.innerHTML = '<i data-feather="x"></i> Marquer absent';
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
            
            fetch('/pointage/multiple', {
                method: 'POST',
                headers: { 'Content-Type':'application/json' },
                body: JSON.stringify({ pointages })
            })
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success') {
                    alert('Tous pointés!'); 
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Erreur pointage multiple.');
                }
            })
            .catch(e => {
                alert('Erreur pointage multiple');
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