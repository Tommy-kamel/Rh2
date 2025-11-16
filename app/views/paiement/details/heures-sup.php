<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Détail Heures Supplémentaires - <?= $data['employe_nom'] ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 15px;
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }

        .header h3 {
            font-size: 16px;
            margin: 0 0 5px 0;
        }

        .table-container {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .btn {
            padding: 6px 12px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            border-radius: 3px;
            font-size: 11px;
            display: inline-block;
        }

        .btn:hover {
            background-color: #e9e9e9;
        }

        a {
            color: #0066cc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
        }

        @media print {
            .no-pdf {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h3><?= $title ?> - <?= $data['employe_nom'] ?> (<?= $data['mois'] ?>/<?= $data['annee'] ?>)</h3>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Minutes</th>
                    <th>Majoration</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['items'])): ?>
                    <tr>
                        <td colspan="4" class="no-data">Aucune heure supplémentaire</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['items'] as $item): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($item['date'])) ?></td>
                        <td><?= ucfirst($item['type']) ?></td>
                        <td><?= $item['minutes'] ?></td>
                        <td><?= $item['majoration'] ?> %</td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="/paiement/fiche?mois=<?= $data['annee'] ?>-<?= $data['mois'] ?>&id_employe=<?= $data['id_employe'] ?>" class="btn no-pdf">Retour à la fiche de paie</a>
    <a href="/paiement/fichepdf/<?= $data['id_employe'] ?>/heure-sup/<?= $data['annee'] ?>-<?= $data['mois'] ?>" target="_blank" class="btn no-pdf">
        Télécharger PDF
    </a>
</body>
</html>