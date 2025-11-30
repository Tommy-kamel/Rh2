<?php

namespace app\controllers\paiement;

require 'C:\xampp\htdocs\S5\Mr Tovo\Rh2\public\assets\lib\dompdf\autoload.inc.php';

use Dompdf\Dompdf;
use app\models\paiement\PaiementEmployeModel;
use app\models\paiement\FichePaiementModel;
use Flight;

class PaiementEmployeController
{
    public static function liste()
    {
        $model = new PaiementEmployeModel();
        $employes = $model->getAllEmployes();

        Flight::render('paiement/liste', ['employes' => $employes]);
    }

    public static function listeIrsa()
    {
        $model = new PaiementEmployeModel();
        $employes = $model->getAllEmployes();

        Flight::render('paiement/liste_irsa', ['employes' => $employes]);
    }

    public static function fiche()
    {
        $model = new FichePaiementModel();
        $annee_mois = $_GET['mois'];
        $id_employe = $_GET['id_employe'];
        $data = $model->genererFichePaie($id_employe, $annee_mois);

        if (!$data) {
            echo "Fiche non disponible pour cet employé.";
            return;
        }

        Flight::render('paiement/fiche', ['data' => $data]);
    }

    public static function fichePDF()
    {
        $model = new FichePaiementModel();
        $annee_mois = $_GET['mois'];
        $id_employe = $_GET['id_employe'];
        $data = $model->genererFichePaie($id_employe, $annee_mois);

        if (!$data) {
            echo "Fiche non disponible pour cet employé.";
            return;
        }

        // Capturer le rendu HTML de la vue existante
        ob_start();
        include __DIR__ . '/../../views/paiement/fiche.php';
        $html = ob_get_clean();

        // Supprime tout ce qui a la classe "no-pdf"
        $html = preg_replace('#<[^>]*class="[^"]*no-pdf[^"]*"[^>]*>.*?</[^>]+>#is', '', $html);

        // Instancier Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // Papier A4 portrait
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        // Afficher dans le navigateur
        $dompdf->stream("Fiche_{$id_employe}.pdf", ["Attachment" => false]);
    }

    public static function ficheExcelXML()
    {
        $model = new FichePaiementModel();
        $annee_mois = $_GET['mois'];
        $id_employe = $_GET['id_employe'];
        $data = $model->genererFichePaie($id_employe, $annee_mois);

        if (!$data) {
            echo "Fiche non disponible pour cet employé.";
            return;
        }

        $filename = "Fiche_Paie_{$data['nom']}_{$data['prenom']}_{$data['mois_annee']}.xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Générer le contenu XML Excel
        $xml = '<?xml version="1.0"?>
    <?mso-application progid="Excel.Sheet"?>
    <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Bottom"/>
            <Borders/>
            <Font ss:FontName="Arial" ss:Size="10"/>
            <Interior/>
            <NumberFormat/>
            <Protection/>
        </Style>
        <Style ss:ID="Header">
            <Font ss:FontName="Arial" ss:Size="12" ss:Bold="1"/>
            <Interior ss:Color="#E6E6E6" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="Title">
            <Font ss:FontName="Arial" ss:Size="14" ss:Bold="1"/>
            <Alignment ss:Horizontal="Center"/>
        </Style>
        <Style ss:ID="Bold">
            <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
        </Style>
    </Styles>
    <Worksheet ss:Name="Fiche de Paie">
    <Table>';

        // Fonction pour formater une ligne
        function addRow($cells, $style = '')
        {
            $row = '<Row>';
            foreach ($cells as $cell) {
                $type = is_numeric($cell) ? 'Number' : 'String';
                $styleAttr = $style ? ' ss:StyleID="' . $style . '"' : '';
                $row .= '<Cell' . $styleAttr . '><Data ss:Type="' . $type . '">' . htmlspecialchars($cell) . '</Data></Cell>';
            }
            $row .= '</Row>';
            return $row;
        }

        // Titre
        $xml .= addRow(['FICHE DE PAIE'], 'Title');
        $xml .= addRow([$data['mois_annee']], 'Title');
        $xml .= addRow(['']);

        // Informations employé
        $xml .= addRow(['INFORMATIONS EMPLOYÉ'], 'Header');
        $xml .= addRow(['Nom et Prénoms', $data['nom'] . ' ' . $data['prenom']]);
        $xml .= addRow(['Matricule', $data['matricule']]);
        $xml .= addRow(['Classification', $data['classification']]);
        $xml .= addRow(['Fonction', $data['fonction']]);
        $xml .= addRow(['Salaire de base', $data['salaire_base']]);
        $xml .= addRow(['N° CNaPS', $data['cnaps']]);
        $xml .= addRow(['Date d\'embauche', date('d/m/Y', strtotime($data['date_embauche']))]);
        $xml .= addRow(['Ancienneté', $data['anciennete']]);
        $xml .= addRow(['Mode de paiement', $data['mode_paiement']]);
        $xml .= addRow(['']);

        // Gains
        $xml .= addRow(['DÉTAIL DES GAINS'], 'Header');
        $xml .= addRow(['Désignations', 'Nombre', 'Taux', 'Montant'], 'Bold');

        $gains = [
            ['Salaire du ' . $data['mois'], '1 mois', $data['salaire_base'], $data['salaire_base']],
            ['Absences déductibles', $data['absences_deductibles'] . ' jour(s)', $data['taux_journalier'], $data['absences']],
            ['Primes de rendement', '', '', $data['primes_rendement']],
            ['Primes d\'ancienneté', '', '', $data['primes_anciennete']],
            ['Heures supplémentaires 30%', number_format($data['hs_30'] / $data['taux_hs_30'], 0, ',', ' ') . ' heure(s)', $data['taux_hs_30'], $data['hs_30']],
            ['Heures supplémentaires 40%', number_format($data['hs_40'] / $data['taux_hs_40'], 0, ',', ' ') . ' heure(s)', $data['taux_hs_40'], $data['hs_40']],
            ['Heures supplémentaires 50%', number_format($data['hs_50'] / $data['taux_hs_50'], 0, ',', ' ') . ' heure(s)', $data['taux_hs_50'], $data['hs_50']],
            ['Heures supplémentaires 100%', number_format($data['hs_100'] / $data['taux_hs_100'], 0, ',', ' ') . ' heure(s)', $data['taux_hs_100'], $data['hs_100']],
            ['Majoration heures de nuit', number_format($data['nuit'] / $data['taux_nuit'], 0, ',', ' ') . ' heure(s)', $data['taux_nuit'], $data['nuit']],
            ['Primes diverses', '', '', $data['primes_diverses']],
            ['Rappels période antérieure', '', '', $data['rappels']],
            ['Droits de congés', $data['conges'], $data['taux_journalier'], $data['droit_conge']],
            ['Droits de préavis', $data['preavis'], $data['droit_preavi'], $data['preavis'] * $data['droit_preavi']],
            ['Indemnités de licenciement', $data['licenciement'], $data['indemnite_liciencement'], $data['licenciement'] * $data['indemnite_liciencement']],
        ];

        foreach ($gains as $gain) {
            $xml .= addRow($gain);
        }

        // Salaire brut
        $xml .= addRow(['SALAIRE BRUT', '', '', $data['salaire_brut']], 'Bold');
        $xml .= addRow(['']);

        // Retenues
        $xml .= addRow(['RETENUES'], 'Header');

        $retenues = [
            ['Retenue CNaPS 1%', '', '', $data['retenue_cnaps']],
            ['Retenue sanitaire 1%', '', '', $data['retenue_sanitaire']],
        ];

        foreach ($data['irsa_tranches'] as $t) {
            $retenues[] = ['Tranche IRSA ' . $t[0] . ' (' . $t[1] . '%)', '', '', $t[3]];
        }

        $retenues[] = ['TOTAL IRSA', '', '', $data['total_irsa']];
        $retenues[] = ['TOTAL DES RETENUES', '', '', $data['total_retenues']];

        foreach ($retenues as $retenue) {
            $xml .= addRow($retenue);
        }

        $xml .= addRow(['']);

        // Net à payer
        $xml .= addRow(['NET À PAYER', '', '', $data['net_a_payer']], 'Bold');
        $xml .= addRow(['Montant imposable', '', '', $data['montant_imposable']]);

        $xml .= '</Table>
    </Worksheet>
    </Workbook>';

        echo $xml;
        exit;
    }

    public static function ficheExcel()
    {
        $model = new FichePaiementModel();
        $annee_mois = $_GET['mois'];
        $id_employe = $_GET['id_employe'];
        $data = $model->genererFichePaie($id_employe, $annee_mois);

        if (!$data) {
            echo "Fiche non disponible pour cet employé.";
            return;
        }

        $filename = "Fiche_Paie_{$data['nom']}_{$data['prenom']}_{$data['mois_annee']}.csv";

        // En-têtes pour le téléchargement CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // En-tête du document
        fputcsv($output, ['FICHE DE PAIE'], ';');
        fputcsv($output, [$data['mois_annee']], ';');
        fputcsv($output, [''], ';');

        // Informations de l'employé
        fputcsv($output, ['INFORMATIONS EMPLOYÉ'], ';');
        fputcsv($output, ['Nom et Prénoms', $data['nom'] . ' ' . $data['prenom']], ';');
        fputcsv($output, ['Matricule', $data['matricule']], ';');
        fputcsv($output, ['Classification', $data['classification']], ';');
        fputcsv($output, ['Fonction', $data['fonction']], ';');
        fputcsv($output, ['Salaire de base', $data['salaire_base']], ';');
        fputcsv($output, ['N° CNaPS', $data['cnaps']], ';');
        fputcsv($output, ['Date d\'embauche', date('d/m/Y', strtotime($data['date_embauche']))], ';');
        fputcsv($output, ['Ancienneté', $data['anciennete']], ';');
        fputcsv($output, ['Mode de paiement', $data['mode_paiement']], ';');
        fputcsv($output, [''], ';');

        // Détails des gains
        fputcsv($output, ['DÉTAIL DES GAINS'], ';');
        fputcsv($output, ['Désignations', 'Nombre', 'Taux', 'Montant'], ';');

        // Données des gains
        $gains = [
            ['Salaire du ' . $data['mois'], '1 mois', $data['salaire_base'], $data['salaire_base']],
            ['Absences déductibles', $data['absences_deductibles'] . ' jour(s)', $data['taux_journalier'], $data['absences']],
            ['Primes de rendement', '', '', $data['primes_rendement']],
            ['Primes d\'ancienneté', '', '', $data['primes_anciennete']],
            ['Heures supplémentaires 30%', number_format($data['hs_30'] / $data['taux_hs_30'], 0, ',', ' ') . ' heure(s)', $data['taux_hs_30'], $data['hs_30']],
            ['Heures supplémentaires 40%', number_format($data['hs_40'] / $data['taux_hs_40'], 0, ',', ' ') . ' heure(s)', $data['taux_hs_40'], $data['hs_40']],
            ['Heures supplémentaires 50%', number_format($data['hs_50'] / $data['taux_hs_50'], 0, ',', ' ') . ' heure(s)', $data['taux_hs_50'], $data['hs_50']],
            ['Heures supplémentaires 100%', number_format($data['hs_100'] / $data['taux_hs_100'], 0, ',', ' ') . ' heure(s)', $data['taux_hs_100'], $data['hs_100']],
            ['Majoration heures de nuit', number_format($data['nuit'] / $data['taux_nuit'], 0, ',', ' ') . ' heure(s)', $data['taux_nuit'], $data['nuit']],
            ['Primes diverses', '', '', $data['primes_diverses']],
            ['Rappels période antérieure', '', '', $data['rappels']],
            ['Droits de congés', $data['conges'], $data['taux_journalier'], $data['droit_conge']],
            ['Droits de préavis', $data['preavis'], $data['droit_preavi'], $data['preavis'] * $data['droit_preavi']],
            ['Indemnités de licenciement', $data['licenciement'], $data['indemnite_liciencement'], $data['licenciement'] * $data['indemnite_liciencement']],
        ];

        foreach ($gains as $gain) {
            fputcsv($output, $gain, ';');
        }

        // Salaire brut
        fputcsv($output, ['SALAIRE BRUT', '', '', $data['salaire_brut']], ';');
        fputcsv($output, [''], ';');

        // Retenues
        fputcsv($output, ['RETENUES'], ';');

        $retenues = [
            ['Retenue CNaPS 1%', $data['retenue_cnaps']],
            ['Retenue sanitaire 1%', $data['retenue_sanitaire']],
        ];

        foreach ($data['irsa_tranches'] as $t) {
            $retenues[] = ['Tranche IRSA ' . $t[0] . ' (' . $t[1] . '%)', $t[3]];
        }

        $retenues[] = ['TOTAL IRSA', $data['total_irsa']];
        $retenues[] = ['TOTAL DES RETENUES', $data['total_retenues']];

        foreach ($retenues as $retenue) {
            fputcsv($output, [$retenue[0], '', '', $retenue[1]], ';');
        }

        fputcsv($output, [''], ';');

        // Net à payer
        fputcsv($output, ['NET À PAYER', '', '', $data['net_a_payer']], ';');
        fputcsv($output, ['Montant imposable', '', '', $data['montant_imposable']], ';');

        fclose($output);    
        exit;
    }
}
