<!DOCTYPE html>
<html>

<head>
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

        .header h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
        }

        .header h2 {
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }

        .employee-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 20px;
        }

        .info-group {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 140px;
        }

        .info-value {
            display: inline-block;
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

        .retenues-section {
            margin-bottom: 15px;
        }

        .retenues-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .retenue-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #eee;
        }

        .retenue-total {
            border-top: 1px solid #333;
            margin-top: 8px;
            padding-top: 8px;
            font-weight: bold;
        }

        .paiement-info {
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 15px;
        }

        .paiement-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .net-a-payer {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px solid #333;
            padding-top: 8px;
            margin-top: 8px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn {
            padding: 6px 12px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            border-radius: 3px;
            font-size: 11px;
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

        @media print {
            .no-pdf {
                display: none !important;
            }
        }

        .action-buttons a {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            margin-right: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .btn-save {
            background: #0d6efd;
        }

        .btn-back {
            background: #6c757d;
        }

        .btn-pdf {
            background: #198754;
        }

        .btn-excel {
            background: #ffc107;
            color: #000;
        }

        .btn-csv {
            background: #0dcaf0;
            color: #000;
        }

        .action-buttons a:hover {
            opacity: 0.85;
        }

        .fixe {
            position: fixed !important;
            bottom: -50px !important;
            left: 0;
            right: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #ddd;
            padding: 10px 20px;
            z-index: 99999 !important;
            display: flex;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.15);
        }

        .signature {
            margin-bottom: 100px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>FICHE DE PAIE</h1>
        <h2><?= $data['mois_annee'] ?></h2>
    </div>

    <div class="employee-info">
        <div class="info-group">
            <div class="info-label">Nom et Prénoms :</div>
            <div class="info-value"><?= $data['nom'] ?> <?= $data['prenom'] ?></div>
        </div>
        <div class="info-group">
            <div class="info-label">Matricule :</div>
            <div class="info-value"><?= $data['matricule'] ?></div>
        </div>
        <div class="info-group">
            <div class="info-label">Classification :</div>
            <div class="info-value"><?= $data['classification'] ?></div>
        </div>
        <div class="info-group">
            <div class="info-label">Fonction :</div>
            <div class="info-value"><?= $data['fonction'] ?></div>
        </div>
        <div class="info-group">
            <div class="info-label">Salaire de base :</div>
            <div class="info-value"><?= number_format($data['salaire_base'], 0, ',', ' ') ?>,00</div>
        </div>
        <div class="info-group">
            <div class="info-label">N° CNaPS :</div>
            <div class="info-value"><?= $data['cnaps'] ?></div>
        </div>
        <div class="info-group">
            <div class="info-label">Taux journaliers :</div>
            <div class="info-value"><?= number_format($data['taux_journalier'], 0, ',', ' ') ?>,00</div>
        </div>
        <div class="info-group">
            <div class="info-label">Date d'embauche :</div>
            <div class="info-value"><?= date('d/m/Y', strtotime($data['date_embauche'])) ?></div>
        </div>
        <div class="info-group">
            <div class="info-label">Taux horaires :</div>
            <div class="info-value"><?= number_format($data['taux_horaire'], 0, ',', ' ') ?>,00</div>
        </div>
        <div class="info-group">
            <div class="info-label">Ancienneté :</div>
            <div class="info-value"><?= $data['anciennete'] ?></div>
        </div>
        <div class="info-group">
            <div class="info-label">Indice :</div>
            <div class="info-value"><?= number_format($data['indice'], 0, ',', ' ') ?>,00</div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Désignations</th>
                    <th>Nombre</th>
                    <th>Taux</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Salaire du <?= $data['mois'] ?></td>
                    <td>1 mois</td>
                    <td><?= number_format($data['salaire_base'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['salaire_base'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Absences déductibles</td>
                    <td><a href="/paiement/details/<?= $data['id_employe'] ?>/absences/<?= $data['mois_annee'] ?>"><?= $data['absences_deductibles'] ?> jour(s)</a></td>
                    <td><?= number_format($data['taux_journalier'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['absences'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Primes de rendement</td>
                    <td></td>
                    <td></td>
                    <td><a href="/paiement/details/<?= $data['id_employe'] ?>/primes/<?= $data['mois_annee'] ?>"><?= number_format($data['primes_rendement'], 0, ',', ' ') ?>,00</a></td>
                </tr>
                <tr>
                    <td>Primes d'ancienneté</td>
                    <td></td>
                    <td></td>
                    <td><?= number_format($data['primes_anciennete'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Heures supplémentaires majorées de 30%</td>
                    <td><a href="/paiement/details/<?= $data['id_employe'] ?>/heures-sup/<?= $data['mois_annee'] ?>"><?= number_format($data['hs_30'] / $data['taux_hs_30'], 0, ',', ' ') ?> heure(s)</a></td>
                    <td><?= number_format($data['taux_hs_30'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['hs_30'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Heures supplémentaires majorées de 40%</td>
                    <td><a href="/paiement/details/<?= $data['id_employe'] ?>/heures-sup/<?= $data['mois_annee'] ?>"><?= number_format($data['hs_40'] / $data['taux_hs_40'], 0, ',', ' ') ?> heure(s)</a></td>
                    <td><?= number_format($data['taux_hs_40'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['hs_40'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Heures supplémentaires majorées de 50%</td>
                    <td><a href="/paiement/details/<?= $data['id_employe'] ?>/heures-sup/<?= $data['mois_annee'] ?>"><?= number_format($data['hs_50'] / $data['taux_hs_50'], 0, ',', ' ') ?> heure(s)</a></td>
                    <td><?= number_format($data['taux_hs_50'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['hs_50'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Heures supplémentaires majorées de 100%</td>
                    <td><a href="/paiement/details/<?= $data['id_employe'] ?>/heures-sup/<?= $data['mois_annee'] ?>"><?= number_format($data['hs_100'] / $data['taux_hs_100'], 0, ',', ' ') ?> heure(s)</a></td>
                    <td><?= number_format($data['taux_hs_100'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['hs_100'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Majoration pour heures de nuit</td>
                    <td><a href="/paiement/details/<?= $data['id_employe'] ?>/heures-nuit/<?= $data['mois_annee'] ?>"><?= number_format($data['nuit'] / $data['taux_nuit'], 0, ',', ' ') ?> heure(s)</a></td>
                    <td><?= number_format($data['taux_nuit'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['nuit'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Primes diverses</td>
                    <td></td>
                    <td></td>
                    <td><a href="/paiement/details/<?= $data['id_employe'] ?>/primes/<?= $data['mois_annee'] ?>"><?= number_format($data['primes_diverses'], 0, ',', ' ') ?>,00 </a></td>
                </tr>
                <tr>
                    <td>Rappels sur période antérieure</td>
                    <td></td>
                    <td></td>
                    <td><?= number_format($data['rappels'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Droits de congés</td>
                    <td><?= $data['conges'] ?></td>
                    <td><?= number_format($data['taux_journalier'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['droit_conge'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Droits de préavis</td>
                    <td><?= $data['preavis'] ?></td>
                    <td><?= number_format($data['droit_preavi'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['preavis'] * $data['droit_preavi'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr>
                    <td>Indemnités de licenciement</td>
                    <td><?= $data['licenciement'] ?></td>
                    <td><?= number_format($data['indemnite_liciencement'], 0, ',', ' ') ?>,00</td>
                    <td><?= number_format($data['licenciement'] * $data['indemnite_liciencement'], 0, ',', ' ') ?>,00</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3"><strong>Salaire brut</strong></td>
                    <td><strong><?= number_format($data['salaire_brut'], 0, ',', ' ') ?>,00</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="retenues-section">
        <h3 style="margin-bottom: 10px; font-size: 13px;">Retenues</h3>
        <div class="retenues-grid">
            <div class="retenue-item">
                <span>Retenue CNaPS 1%</span>
                <span><?= number_format($data['retenue_cnaps'], 0, ',', ' ') ?>,00</span>
            </div>
            <div class="retenue-item">
                <span>Retenue sanitaire 1%</span>
                <span><?= number_format($data['retenue_sanitaire'], 0, ',', ' ') ?>,00</span>
            </div>
            <?php foreach ($data['irsa_tranches'] as $t): ?>
                <div class="retenue-item">
                    <span><a href="/paiement/details/<?= $data['id_employe'] ?>/irsa/<?= $data['mois_annee'] ?>">Tranche IRSA <?= $t[0] ?></a></span>
                    <span><?= $t[1] ?>%</span>
                    <span><?= number_format($t[3], 0, ',', ' ') ?>,00</span>
                </div>
            <?php endforeach; ?>
            <div class="retenue-item retenue-total">
                <span>TOTAL IRSA</span>
                <span><?= number_format($data['total_irsa'], 0, ',', ' ') ?>,00</span>
            </div>
            <div class="retenue-item retenue-total">
                <span>Total des retenues</span>
                <span><?= number_format($data['total_retenues'], 0, ',', ' ') ?>,00</span>
            </div>
        </div>
    </div>

    <div class="paiement-info">
        <div class="paiement-row net-a-payer">
            <span>Net à payer</span>
            <span><?= number_format($data['net_a_payer'], 0, ',', ' ') ?>,00</span>
        </div>
        <div class="paiement-row">
            <span>Montant imposable :</span>
            <span><?= number_format($data['montant_imposable'], 0, ',', ' ') ?>,00</span>
        </div>
        <div class="paiement-row">
            <span>Mode de paiement :</span>
            <span><?= $data['mode_paiement'] ?></span>
        </div>
    </div>

    <div class="signature">
        <div class="signature-box" style="width: 48%; float: left;">
            <p style="margin-bottom: 40px;"><em>L'employeur</em></p>
            <p>_________________________</p>
        </div>
        <div class="signature-box" style="width: 48%; float: right;">
            <p style="margin-bottom: 40px;"><em>L'employé(e)</em></p>
            <p>_________________________</p>
        </div>
        <div style="clear: both;"></div><br><br><br><br><br>
        <b style="font-style: bold;">Antananarivo le : <?=date("d/m/Y"); ?></b>
    </div>


    <div class="action-buttons no-pdf fixe" style="clear: both; margin-bottom: 50px">
        <a href="/paiement/enregistrer_fiche?mois=<?= $data['mois_annee'] ?>&id_employe=<?= $data['id_employe'] ?>" class="btn-save no-pdf">Enregistrer</a>
        <a href="/paiement/employes" class="btn-back no-pdf">Retour</a>
        <a href="/paiement/fichepdf?id_employe=<?= $data['id_employe'] ?>&mois=<?= $data['mois_annee'] ?? '' ?>" target="_blank" class="btn-pdf no-pdf">Export PDF</a>
        <a href="/paiement/fiche-excel-xml?id_employe=<?= $data['id_employe'] ?>&mois=<?= $data['mois_annee'] ?? '' ?>" class="btn-excel no-pdf">Export Excel</a>
        <a href="/paiement/fiche-excel?id_employe=<?= $data['id_employe'] ?>&mois=<?= $data['mois_annee'] ?? '' ?>" class="btn-csv no-pdf">Export CSV</a>
    </div>
</body>

</html>