<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Fiche de Paie - <?= $data['nom'] ?> <?= $data['prenom'] ?></title>
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

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
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
        <h3>FICHE DE PAIE <?= $data['mois_annee'] ?></h3>
    </div>

    <div class="table-container">
        <table>
            <tbody>
                <tr>
                    <td><strong>Nom et Prénoms :</strong> <?= $data['nom'] ?> <?= $data['prenom'] ?></td>
                    <td><strong>Matricule :</strong> <?= $data['matricule'] ?></td>
                </tr>
                <tr>
                    <td><strong>Classification :</strong> <?= $data['classification'] ?></td>
                    <td><strong>Fonction :</strong> <?= $data['fonction'] ?></td>
                </tr>
                <tr>
                    <td><strong>Salaire de base :</strong> <?= number_format($data['salaire_base'], 0, ',', ' ') ?>,00</td>
                    <td><strong>N° CNaPS :</strong> <?= $data['cnaps'] ?></td>
                </tr>
                <tr>
                    <td><strong>Taux journaliers :</strong> <?= number_format($data['taux_journalier'], 0, ',', ' ') ?>,00</td>
                    <td><strong>Date d'embauche :</strong> <?= date('d/m/Y', strtotime($data['date_embauche'])) ?></td>
                </tr>
                <tr>
                    <td><strong>Taux horaires :</strong> <?= number_format($data['taux_horaire'], 0, ',', ' ') ?>,00</td>
                    <td><strong>Ancienneté :</strong> <?= $data['anciennete'] ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Indice :</strong> <?= number_format($data['indice'], 0, ',', ' ') ?>,00</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <table>
            <tbody>
                <?php foreach ($data['irsa_tranches'] as $t): ?>
                    <tr>
                        <td><strong>Tranche IRSA <?= $t[0] ?></strong></td>
                        <td><?= $t[1] ?> %</td>
                        <td><?= number_format($t[3], 0, ',', ' ') ?>,00</td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td><strong>TOTAL IRSA</strong></td>
                    <td colspan="2"><strong><?= number_format($data['total_irsa'], 0, ',', ' ') ?>,00</strong></td>
                </tr>
            </tbody>
        </table>

        <a href="/paiement/fiche?mois=<?= $data['annee'] ?>-<?= $data['mois'] ?>&id_employe=<?= $data['id_employe'] ?>" class="btn no-pdf">Retour à la fiche de paie</a>
        <a href="/paiement/fichepdf/<?= $data['id_employe'] ?>/irsa/<?= $data['annee'] ?>-<?= $data['mois'] ?>" target="_blank" class="btn no-pdf">
            Télécharger PDF
        </a>
    </div>
</body>

</html>