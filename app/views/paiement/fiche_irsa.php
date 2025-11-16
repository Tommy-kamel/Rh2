<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Paie - <?= $data['nom'] ?> <?= $data['prenom'] ?></title>

    <!-- mêmes CSS du dashboard -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
</head>

<body>
    <div class="app-container">

        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        <main class="main-content">

            <div class="content p-4">

                <h3 class="mb-4">FICHE DE PAIE <?= $data['mois_annee'] ?></h3>

                <!-- Bloc Informations Employé -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        Informations générales
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
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
                                <td><strong>Taux journalier :</strong> <?= number_format($data['taux_journalier'], 0, ',', ' ') ?>,00</td>
                                <td><strong>Date d'embauche :</strong> <?= date('d/m/Y', strtotime($data['date_embauche'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Taux horaire :</strong> <?= number_format($data['taux_horaire'], 0, ',', ' ') ?>,00</td>
                                <td><strong>Ancienneté :</strong> <?= $data['anciennete'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Indice :</strong> <?= number_format($data['indice'], 0, ',', ' ') ?>,00</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Bloc IRSA -->
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        Calcul IRSA
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <?php foreach ($data['irsa_tranches'] as $t): ?>
                                <tr>
                                    <td><strong>Tranche IRSA <?= $t[0] ?></strong></td>
                                    <td><?= $t[1] ?> %</td>
                                    <td><?= number_format($t[3], 0, ',', ' ') ?>,00</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-primary">
                                <td><strong>TOTAL IRSA</strong></td>
                                <td></td>
                                <td><strong><?= number_format($data['total_irsa'], 0, ',', ' ') ?>,00</strong></td>
                            </tr>
                        </table>
                    </div>
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
    </script>

    <script src="/assets/js/rh-dashboard.js"></script>
</body>

</html>