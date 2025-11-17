<!-- app/views/retards.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Retards</title>
    <style>
        .table-container {
            margin: 20px 0;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .retard-leger { background-color: #fff3cd; }
        .retard-moyen { background-color: #ffeaa7; }
        .retard-grave { background-color: #fdcb6e; }
        .retard-critique { background-color: #e17055; color: white; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dc3545;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⏰ Gestion des Retards</h1>
        <div>
            <a href="/feuille-temps/vue" style="margin-right: 10px;">📊 Voir feuille de temps</a>
            <a href="/stats-retards">📈 Statistiques</a>
        </div>
    </div>

    <?php
    // Calcul des statistiques
    $totalRetards = count($retards);
    $totalMinutes = array_sum(array_column($retards, 'duree_retard'));
    $moyenneRetard = $totalRetards > 0 ? round($totalMinutes / $totalRetards, 1) : 0;
    ?>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number"><?= $totalRetards ?></div>
            <div>Total des retards</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $totalMinutes ?></div>
            <div>Minutes totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $moyenneRetard ?></div>
            <div>Moyenne (minutes)</div>
        </div>
    </div>

    <?php if (!empty($retards)): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Date du retard</th>
                        <th>Durée (minutes)</th>
                        <th>Niveau</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($retards as $r): ?>
                        <?php
                        $niveau = '';
                        $classe = '';
                        if ($r['duree_retard'] <= 5) {
                            $niveau = 'Léger';
                            $classe = 'retard-leger';
                        } elseif ($r['duree_retard'] <= 15) {
                            $niveau = 'Moyen';
                            $classe = 'retard-moyen';
                        } elseif ($r['duree_retard'] <= 30) {
                            $niveau = 'Grave';
                            $classe = 'retard-grave';
                        } else {
                            $niveau = 'Critique';
                            $classe = 'retard-critique';
                        }
                        ?>
                        <tr class="<?= $classe ?>">
                            <td><strong><?= htmlspecialchars($r['nom'] . ' ' . $r['prenom']) ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($r['date_retard'])) ?></td>
                            <td><strong><?= $r['duree_retard'] ?> min</strong></td>
                            <td><?= $niveau ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 5px;">
            <p style="font-size: 18px; color: #28a745;">✅ Aucun retard enregistré</p>
            <p style="color: #6c757d;">Tous les employés sont à l'heure !</p>
        </div>
    <?php endif; ?>
</body>
</html>