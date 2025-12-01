<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Performance Individuel - <?= $rapport['employe']['prenom'] ?> <?= $rapport['employe']['nom'] ?></title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        .test {

        }
        
        /* NOUVEL EN-TÊTE PERSONNALISÉ */
        .header-employe {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            position: relative;
            overflow: hidden;
        }
        
        .header-employe::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,202.7C672,203,768,181,864,165.3C960,149,1056,139,1152,149.3C1248,160,1344,192,1392,208L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: center bottom;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .employe-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 4px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .employe-avatar i {
            width: 48px;
            height: 48px;
            color: white;
        }
        
        .employe-nom {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        
        .employe-poste {
            font-size: 1.2rem;
            opacity: 0.9;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .employe-infos {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            backdrop-filter: blur(5px);
        }
        
        .info-item i {
            width: 18px;
            height: 18px;
        }
        
        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto;
            border: 6px solid;
            background: white;
        }
        .score-excellent { color: #28a745; border-color: #28a745; }
        .score-bon { color: #20c997; border-color: #20c997; }
        .score-moyen { color: #ffc107; border-color: #ffc107; }
        .score-faible { color: #fd7e14; border-color: #fd7e14; }
        .score-critique { color: #dc3545; border-color: #dc3545; }
        .card-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .card-header-section {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem;
            font-weight: 600;
            color: #495057;
            border-radius: 12px 12px 0 0 !important;
        }
        .critere-score {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 8px;
            background: #f8f9fa;
            transition: background-color 0.2s ease;
        }
        
        .critere-score:hover {
            background: #e9ecef;
        }
        
        .critere-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
        }
        .progress-custom {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            flex-grow: 1;
            margin: 0 1rem;
        }
        .progress-bar-custom {
            height: 100%;
            border-radius: 4px;
        }
        .badge-niveau {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }
        .point-fort {
            /* border-left: 4px solid #28a745; */
            background: rgba(40, 167, 69, 0.1);
            transition: transform 0.2s ease;
        }
        
        .point-fort:hover {
            transform: translateX(5px);
        }
        
        .point-amelioration {
            /* border-left: 4px solid #dc3545; */
            background: rgba(220, 53, 69, 0.1);
            transition: transform 0.2s ease;
        }
        
        .point-amelioration:hover {
            transform: translateX(5px);
        }
        
        .recommandation {
            /* border-left: 4px solid #007bff; */
            background: rgba(0, 123, 255, 0.1);
            transition: transform 0.2s ease;
        }
        
        .recommandation:hover {
            transform: translateX(5px);
        }
        
        .historique-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }
        
        .historique-item:hover {
            background: #f8f9fa;
        }
        
        .historique-item:last-child {
            border-bottom: none;
        }
        .evolution-positive { color: #28a745; }
        .evolution-negative { color: #dc3545; }
        .evolution-neutre { color: #6c757d; }
        @media print {
            .no-print { display: none !important; }
            .card-section { box-shadow: none; border: 1px solid #dee2e6; }
            .filter-section { display: none !important; }
            .header-employe { background: #f8f9fa !important; color: #333 !important; }
        }
        
        @media (max-width: 768px) {
            .employe-infos {
                flex-direction: column;
                gap: 0.75rem;
                align-items: center;
            }
            
            .employe-nom {
                font-size: 1.8rem;
            }
            
            .header-employe {
                padding: 1.5rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <!-- Filtres TOUJOURS VISIBLES EN HAUT -->
            <div class="filter-section no-print">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label for="periode" class="form-label"><strong>Période d'analyse :</strong></label>
                        <select class="form-select" id="periode" name="periode" onchange="changerPeriode()">
                            <option value="mensuelle" <?= $rapport['periode'] === 'mensuelle' ? 'selected' : '' ?>>Mensuelle</option>
                            <option value="trimestrielle" <?= $rapport['periode'] === 'trimestrielle' ? 'selected' : '' ?>>Trimestrielle</option>
                            <option value="annuelle" <?= $rapport['periode'] === 'annuelle' ? 'selected' : '' ?>>Annuelle</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="select-employe" class="form-label"><strong>Sélectionner un employé :</strong></label>
                        <select class="form-select" id="select-employe" onchange="changerEmploye()">
                            <option value="">Choisir un employé...</option>
                            <!-- Chargé dynamiquement -->
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-outline-primary w-100" onclick="retourListe()">
                            <i data-feather="arrow-left"></i> Retour
                        </button>
                    </div>
                </div>
            </div>

            <!-- NOUVEL EN-TÊTE AVEC NOM DE L'EMPLOYÉ -->
            <div class="header-employe">
                <div class="header-content">
                    <div class="employe-avatar">
                        <i data-feather="user"></i>
                    </div>
                    <h1 class="employe-nom"><?= htmlspecialchars($rapport['employe']['prenom']) ?> <?= htmlspecialchars($rapport['employe']['nom']) ?></h1>
                    <div class="employe-poste"><?= htmlspecialchars($rapport['employe']['poste'] ?? 'Poste non défini') ?></div>
                    
                    <div class="employe-infos">
                        <div class="info-item">
                            <i data-feather="briefcase"></i>
                            <span><?= htmlspecialchars($rapport['employe']['nom_departement'] ?? 'Département non défini') ?></span>
                        </div>
                        <div class="info-item">
                            <i data-feather="mail"></i>
                            <span><?= htmlspecialchars($rapport['employe']['email'] ?? 'Email non défini') ?></span>
                        </div>
                        <div class="info-item">
                            <i data-feather="calendar"></i>
                            <span>Embauche : <?= $rapport['employe']['date_embauche'] ? date('d/m/Y', strtotime($rapport['employe']['date_embauche'])) : 'Non définie' ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="file-text"></i>
                    Rapport de Performance Individuel
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nom_utilisateur'] ?? '' ?></span>
                    <span class="user-role"><?= $_SESSION['nom_departement'] ?? 'Administrateur' ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <div>
                        <span class="text-muted">
                            <i data-feather="calendar"></i>
                            Période : <?= ucfirst($rapport['periode']) ?> 
                            • Généré le : <?= $rapport['date_generation'] ?>
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                    </div>
                </div>

                <div class="row">
                    <!-- Colonne gauche : Scores détaillés -->
                    <div class="col-lg-8">
                        <!-- Score Global et Analyse -->
                        <div class="card card-section">
                            <div class="card-header card-header-section">
                                <i data-feather="bar-chart-2" class="me-2"></i>
                                Analyse de la Performance
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Score Global</h5>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="progress-custom">
                                                <div class="progress-bar-custom bg-<?= $model->getCouleurNiveau($rapport['analyse']['niveau_performance']) ?>" 
                                                     style="width: <?= $rapport['score_global'] ?>%"></div>
                                            </div>
                                            <strong class="ms-2"><?= number_format($rapport['score_global'], 1) ?>/100</strong>
                                        </div>
                                        <p class="text-muted"><?= $rapport['analyse']['message_global'] ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Détail par Critère</h5>
                                        <?php foreach ($rapport['scores_detail'] as $critere => $data): ?>
                                            <div class="critere-score">
                                                <div class="critere-icon bg-<?= $model->getCouleurCritere($critere) ?>">
                                                    <i data-feather="<?= $model->getIconeCritere($critere) ?>" style="width: 18px; height: 18px;"></i>
                                                </div>
                                                <span style="min-width: 120px; font-weight: 500;">
                                                    <?= $model->getLibelleCritere($critere) ?>
                                                </span>
                                                <div class="progress-custom">
                                                    <div class="progress-bar-custom bg-<?= $model->getCouleurScore($data['score']) ?>" 
                                                         style="width: <?= $data['score'] ?>%"></div>
                                                </div>
                                                <strong class="text-<?= $model->getCouleurScore($data['score']) ?>" style="min-width: 40px; text-align: right;">
                                                    <?= $data['score'] ?>
                                                </strong>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Points Forts et Amélioration -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-section h-100">
                                    <div class="card-header card-header-section">
                                        <i data-feather="trending-up" class="me-2"></i>
                                        Points Forts
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($rapport['analyse']['points_forts'])): ?>
                                            <?php foreach ($rapport['analyse']['points_forts'] as $point): ?>
                                                <div class="point-fort p-3 mb-2 rounded">
                                                    <i data-feather="check-circle" class="text-success me-2"></i>
                                                    <?= htmlspecialchars($point) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted text-center py-3">
                                                <i data-feather="info" class="me-2"></i>
                                                Aucun point fort significatif identifié
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-section h-100">
                                    <div class="card-header card-header-section">
                                        <i data-feather="target" class="me-2"></i>
                                        Points d'Amélioration
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($rapport['analyse']['points_amelioration'])): ?>
                                            <?php foreach ($rapport['analyse']['points_amelioration'] as $point): ?>
                                                <div class="point-amelioration p-3 mb-2 rounded">
                                                    <i data-feather="alert-circle" class="text-danger me-2"></i>
                                                    <?= htmlspecialchars($point) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted text-center py-3">
                                                <i data-feather="check-circle" class="me-2"></i>
                                                Aucun point d'amélioration critique identifié
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recommandations -->
                        <div class="card card-section">
                            <div class="card-header card-header-section">
                                <i data-feather="lightbulb" class="me-2"></i>
                                Recommandations
                            </div>
                            <div class="card-body">
                                <?php foreach ($rapport['analyse']['recommandations'] as $recommandation): ?>
                                    <div class="recommandation p-3 mb-2 rounded">
                                        <i data-feather="arrow-right" class="text-primary me-2"></i>
                                        <?= htmlspecialchars($recommandation) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne droite : Informations et Historique -->
                    <div class="col-lg-4">
                        <!-- Informations Employé -->
                        <div class="card card-section">
                            <div class="card-header card-header-section">
                                <i data-feather="info" class="me-2"></i>
                                Informations Employé
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Poste :</strong><br>
                                    <?= htmlspecialchars($rapport['employe']['poste'] ?? 'Non défini') ?>
                                </div>
                                <div class="mb-3">
                                    <strong>Département :</strong><br>
                                    <?= htmlspecialchars($rapport['employe']['nom_departement'] ?? 'Non défini') ?>
                                </div>
                                <div class="mb-3">
                                    <strong>Date d'embauche :</strong><br>
                                    <?= $rapport['employe']['date_embauche'] ? date('d/m/Y', strtotime($rapport['employe']['date_embauche'])) : 'Non définie' ?>
                                </div>
                                <div class="mb-3">
                                    <strong>Email :</strong><br>
                                    <?= htmlspecialchars($rapport['employe']['email'] ?? 'Non défini') ?>
                                </div>
                                <div>
                                    <strong>Téléphone :</strong><br>
                                    <?= htmlspecialchars($rapport['employe']['telephone'] ?? 'Non défini') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Évolution des Scores -->
                        <div class="card card-section">
                            <div class="card-header card-header-section">
                                <i data-feather="activity" class="me-2"></i>
                                Évolution <?= ucfirst($rapport['periode']) ?>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($rapport['historique'])): ?>
                                    <?php for ($i = 0; $i < count($rapport['historique']); $i++): ?>
                                        <?php 
                                        $eval = $rapport['historique'][$i];
                                        $evolution = '';
                                        $classe_evolution = 'evolution-neutre';
                                        
                                        if ($i > 0) {
                                            $prev_score = $rapport['historique'][$i-1]['score_global'];
                                            $diff = $eval['score_global'] - $prev_score;
                                            if ($diff > 0) {
                                                $evolution = '(+' . number_format($diff, 1) . ')';
                                                $classe_evolution = 'evolution-positive';
                                            } elseif ($diff < 0) {
                                                $evolution = '(' . number_format($diff, 1) . ')';
                                                $classe_evolution = 'evolution-negative';
                                            }
                                        }
                                        ?>
                                        <div class="historique-item">
                                            <div>
                                                <strong><?= number_format($eval['score_global'], 1) ?></strong>
                                                <span class="<?= $classe_evolution ?>"><?= $evolution ?></span>
                                                <br>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y', strtotime($eval['date_evaluation'])) ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-<?= $model->getCouleurScore($eval['score_global']) ?>">
                                                <?= $model->getNiveauSimplifie($eval['score_global']) ?>
                                            </span>
                                        </div>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center py-3">
                                        <i data-feather="calendar" class="me-2"></i>
                                        Aucun historique disponible
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Détails Techniques -->
                        <div class="card card-section">
                            <div class="card-header card-header-section">
                                <i data-feather="bar-chart" class="me-2"></i>
                                Détails Techniques
                            </div>
                            <div class="card-body">
                                <?php 
                                $details_assiduite = $rapport['scores_detail']['assiduite']['details'] ?? [];
                                $details_objectifs = $rapport['scores_detail']['objectifs']['details'] ?? [];
                                ?>
                                
                                <small class="text-muted">Assiduité :</small>
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark">
                                        <?= $details_assiduite['jours_travailles'] ?? 0 ?> jours travaillés
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <?= $details_assiduite['retards']['total'] ?? 0 ?> retards
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <?= $details_assiduite['absences']['total'] ?? 0 ?> absences
                                    </span>
                                </div>

                                <small class="text-muted">Engagement :</small>
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark">
                                        <?= $details_objectifs['heures_supplementaires']['total_heures'] ?? 0 ?>h sup
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        Taux présence : <?= $details_objectifs['taux_presence'] ?? 0 ?>%
                                    </span>
                                </div>

                                <small class="text-muted">Qualité :</small>
                                <div>
                                    <span class="badge bg-light text-dark">
                                        Ponctualité : <?= $rapport['scores_detail']['qualite']['details']['taux_ponctualite'] ?? 0 ?>%
                                    </span>
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
                        
                        // Ajouter les employés et sélectionner l'employé actuel
                        data.employes.forEach(employe => {
                            const option = document.createElement('option');
                            option.value = employe.id_employe;
                            option.textContent = `${employe.prenom} ${employe.nom} - ${employe.poste}`;
                            // Sélectionner l'employé actuel
                            if (employe.id_employe == <?= $rapport['employe']['id_employe'] ?>) {
                                option.selected = true;
                            }
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        }

        function changerEmploye() {
            const idEmploye = document.getElementById('select-employe').value;
            const periode = document.getElementById('periode').value;
            
            if (idEmploye) {
                window.location.href = `/scoring/rapport/individuel?id_employe=${idEmploye}&periode=${periode}`;
            }
        }

        function changerPeriode() {
            const idEmploye = document.getElementById('select-employe').value;
            const periode = document.getElementById('periode').value;
            
            if (idEmploye) {
                window.location.href = `/scoring/rapport/individuel?id_employe=${idEmploye}&periode=${periode}`;
            }
        }

        function retourListe() {
            window.location.href = '/scoring/rapports';
        }

        function exporterPDF() {
            const idEmploye = <?= $rapport['employe']['id_employe'] ?>;
            const periode = '<?= $rapport['periode'] ?>';
            window.open(`/scoring/rapport/export-pdf?type=individuel&id_employe=${idEmploye}&periode=${periode}`, '_blank');
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