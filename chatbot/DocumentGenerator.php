<?php

namespace chatbot;

require_once __DIR__ . '/../vendor/autoload.php';

use TCPDF;

class DocumentGenerator
{
    private $db;
    private $outputDir;

    public function __construct($db)
    {
        $this->db = $db;
        $this->outputDir = __DIR__ . '/generated_documents/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
        }
    }

    /**
     * Génère un document PDF selon le type demandé
     * @param string $type Type de document (attestation, contrat, certificat, fiche_paie, avenant)
     * @param int $userId ID de l'employé
     * @return array Résultat avec le chemin du fichier ou erreur
     */
    public function genererDocument($type, $userId)
    {
        // Récupérer les informations de l'employé
        $employe = $this->getEmployeeFullInfo($userId);
        
        if (!$employe) {
            return ['error' => 'Employé non trouvé'];
        }

        switch ($type) {
            case 'attestation':
                return $this->genererAttestation($employe);
            
            case 'contrat':
                return $this->genererContrat($employe);
            
            case 'certificat':
                return $this->genererCertificat($employe);
            
            case 'fiche_paie':
                return $this->genererFichePaie($employe);
            
            case 'avenant':
                return $this->genererAvenant($employe);
            
            default:
                return ['error' => 'Type de document non reconnu'];
        }
    }

    /**
     * Génère une attestation de travail
     */
    public function genererAttestation($employe, $params = [])
    {
        $pdf = $this->createPDF('Attestation de Travail');
        
        // En-tête entreprise
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'ATTESTATION DE TRAVAIL', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Corps du document
        $pdf->SetFont('helvetica', '', 12);
        
        $date = date('d/m/Y');
        $dateDebut = date('d/m/Y', strtotime($employe['contrat_debut']));
        
        $texte = "Je soussignée, Madame Rosette RABARIJAONA, Directrice des Ressources Humaines de Mon Entreprise SARL, " .
                 "certifie que :\n\n";
        
        $texte .= $employe['sexe'] == 'M' ? "Monsieur " : "Madame ";
        $texte .= strtoupper($employe['nom']) . " " . $employe['prenom'] . "\n";
        $texte .= "Né(e) le " . date('d/m/Y', strtotime($employe['date_naissance'])) . "\n";
        $texte .= "Adresse : " . $employe['adresse'] . "\n\n";
        
        $texte .= "Est employé(e) dans notre entreprise en qualité de " . $employe['poste'] . " ";
        $texte .= "au sein du département " . $employe['departement'] . " ";
        $texte .= "depuis le " . $dateDebut . ".\n\n";
        
        $texte .= $employe['sexe'] == 'M' ? "Il" : "Elle";
        $texte .= " exerce ses fonctions à temps plein et bénéficie d'un contrat à durée indéterminée.\n\n";
        
        $texte .= "Cette attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.\n\n";
        
        $texte .= "Fait à Antananarivo, le " . $date . "\n\n";
        
        $pdf->MultiCell(0, 6, $texte, 0, 'L');
        
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'La Directrice des Ressources Humaines', 0, 1, 'R');
        $pdf->Cell(0, 10, 'Rosette RABARIJAONA', 0, 1, 'R');
        
        // Sauvegarder
        $filename = 'attestation_' . $employe['nom'] . '_' . date('Ymd_His') . '.pdf';
        $filepath = $this->outputDir . $filename;
        $pdf->Output($filepath, 'F');
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'Attestation de travail générée avec succès'
        ];
    }

    /**
     * Génère un contrat de travail
     */
    public function genererContrat($employe, $params = [])
    {
        $pdf = $this->createPDF('Contrat de Travail');
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'CONTRAT DE TRAVAIL', 0, 1, 'C');
        $pdf->Cell(0, 8, 'À DURÉE INDÉTERMINÉE', 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('helvetica', '', 11);
        
        $texte = "Entre les soussignés :\n\n";
        
        $texte .= "D'UNE PART,\n";
        $texte .= "Mon Entreprise SARL, représentée par Madame Rosette RABARIJAONA, ";
        $texte .= "en qualité de Directrice des Ressources Humaines,\n\n";
        
        $texte .= "Ci-après dénommée « L'Employeur »\n\n";
        
        $texte .= "ET D'AUTRE PART,\n\n";
        
        $texte .= $employe['sexe'] == 'M' ? "Monsieur " : "Madame ";
        $texte .= strtoupper($employe['nom']) . " " . $employe['prenom'] . "\n";
        $texte .= "Né(e) le : " . date('d/m/Y', strtotime($employe['date_naissance'])) . "\n";
        $texte .= "Adresse : " . $employe['adresse'] . "\n";
        $texte .= "Email : " . $employe['email'] . "\n";
        $texte .= "Téléphone : " . $employe['telephone'] . "\n\n";
        
        $texte .= "Ci-après dénommé(e) « Le Salarié »\n\n";
        
        $texte .= "IL A ÉTÉ CONVENU ET ARRÊTÉ CE QUI SUIT :\n\n";
        
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        // Article 1 - Engagement
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'ARTICLE 1 - ENGAGEMENT', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        
        $dateDebut = date('d/m/Y', strtotime($employe['contrat_debut']));
        $texte = "L'Employeur engage le Salarié en qualité de " . $employe['poste'] . " ";
        $texte .= "à compter du " . $dateDebut . ".\n\n";
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        // Article 2 - Fonctions
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'ARTICLE 2 - FONCTIONS', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        
        $texte = "Le Salarié exercera les fonctions de " . $employe['poste'] . " ";
        $texte .= "au sein du département " . $employe['departement'] . ". ";
        $texte .= "Il sera placé sous l'autorité hiérarchique directe de son responsable de département.\n\n";
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        // Article 3 - Rémunération
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'ARTICLE 3 - RÉMUNÉRATION', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        
        $salaire = number_format($employe['salaire'], 0, ',', ' ');
        $texte = "En contrepartie de son travail, le Salarié percevra une rémunération mensuelle brute de ";
        $texte .= $salaire . " Ariary (AR).\n\n";
        $texte .= "Cette rémunération sera versée mensuellement par virement bancaire.\n\n";
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        // Article 4 - Durée du travail
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'ARTICLE 4 - DURÉE DU TRAVAIL', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        
        $texte = "La durée hebdomadaire de travail est fixée à 40 heures, réparties du lundi au vendredi.\n";
        $texte .= "Les horaires de travail sont : 8h00 - 12h00 et 14h00 - 18h00.\n\n";
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        // Article 5 - Congés
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'ARTICLE 5 - CONGÉS', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        
        $texte = "Le Salarié bénéficie de congés payés conformément à la législation en vigueur, ";
        $texte .= "soit 30 jours ouvrables par an.\n\n";
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        // Signatures
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', 'B', 11);
        
        $pdf->Cell(90, 10, 'L\'Employeur', 0, 0, 'C');
        $pdf->Cell(90, 10, 'Le Salarié', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(90, 8, 'Rosette RABARIJAONA', 0, 0, 'C');
        $pdf->Cell(90, 8, $employe['nom'] . ' ' . $employe['prenom'], 0, 1, 'C');
        
        $pdf->Ln(5);
        $date = date('d/m/Y');
        $pdf->Cell(0, 5, 'Fait à Antananarivo, le ' . $date, 0, 1, 'C');
        
        // Sauvegarder
        $filename = 'contrat_' . $employe['nom'] . '_' . date('Ymd_His') . '.pdf';
        $filepath = $this->outputDir . $filename;
        $pdf->Output($filepath, 'F');
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'Contrat de travail généré avec succès'
        ];
    }

    /**
     * Génère un certificat de travail
     */
    public function genererCertificat($employe, $params = [])
    {
        $pdf = $this->createPDF('Certificat de Travail');
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'CERTIFICAT DE TRAVAIL', 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('helvetica', '', 12);
        
        $date = date('d/m/Y');
        $dateDebut = date('d/m/Y', strtotime($employe['contrat_debut']));
        $dateFin = date('d/m/Y'); // Date actuelle comme date de fin
        
        $texte = "Je soussignée, Madame Rosette RABARIJAONA, Directrice des Ressources Humaines de Mon Entreprise SARL, " .
                 "certifie que :\n\n";
        
        $texte .= $employe['sexe'] == 'M' ? "Monsieur " : "Madame ";
        $texte .= strtoupper($employe['nom']) . " " . $employe['prenom'] . "\n";
        $texte .= "Né(e) le : " . date('d/m/Y', strtotime($employe['date_naissance'])) . "\n";
        $texte .= "Adresse : " . $employe['adresse'] . "\n\n";
        
        $texte .= "A été employé(e) dans notre entreprise du " . $dateDebut . " au " . $dateFin . " ";
        $texte .= "en qualité de " . $employe['poste'] . ".\n\n";
        
        $texte .= "Pendant la durée de son emploi, " . ($employe['sexe'] == 'M' ? "il" : "elle");
        $texte .= " s'est acquitté(e) de ses fonctions avec compétence et professionnalisme.\n\n";
        
        $texte .= $employe['sexe'] == 'M' ? "Il" : "Elle";
        $texte .= " quitte l'entreprise libre de tout engagement.\n\n";
        
        $texte .= "Ce certificat est délivré pour servir et valoir ce que de droit.\n\n";
        
        $texte .= "Fait à Antananarivo, le " . $date . "\n\n";
        
        $pdf->MultiCell(0, 6, $texte, 0, 'L');
        
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'La Directrice des Ressources Humaines', 0, 1, 'R');
        $pdf->Cell(0, 10, 'Rosette RABARIJAONA', 0, 1, 'R');
        
        // Sauvegarder
        $filename = 'certificat_' . $employe['nom'] . '_' . date('Ymd_His') . '.pdf';
        $filepath = $this->outputDir . $filename;
        $pdf->Output($filepath, 'F');
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'Certificat de travail généré avec succès'
        ];
    }

    /**
     * Génère une fiche de paie
     */
    public function genererFichePaie($employe, $mois = null, $annee = null)
    {
        $pdf = $this->createPDF('Fiche de Paie');
        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'BULLETIN DE PAIE', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $mois = date('F Y');
        $pdf->Cell(0, 8, 'Mois : ' . $mois, 0, 1, 'C');
        $pdf->Ln(5);
        
        // Informations employeur
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'EMPLOYEUR', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'Mon Entreprise SARL', 0, 1);
        $pdf->Cell(0, 5, 'Antananarivo, Madagascar', 0, 1);
        $pdf->Ln(3);
        
        // Informations salarié
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'SALARIÉ', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, $employe['nom'] . ' ' . $employe['prenom'], 0, 1);
        $pdf->Cell(0, 5, 'Poste : ' . $employe['poste'], 0, 1);
        $pdf->Cell(0, 5, 'Département : ' . $employe['departement'], 0, 1);
        $pdf->Ln(5);
        
        // Tableau des éléments de paie
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);
        
        // En-tête du tableau
        $pdf->Cell(100, 7, 'LIBELLÉ', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'MONTANT', 1, 1, 'R', true);
        
        $pdf->SetFont('helvetica', '', 9);
        
        // Salaire de base
        $salaireBase = $employe['salaire'];
        $pdf->Cell(100, 6, 'Salaire de base', 1);
        $pdf->Cell(40, 6, number_format($salaireBase, 0, ',', ' ') . ' AR', 1, 1, 'R');
        
        // Primes (exemple)
        $primes = $salaireBase * 0.1; // 10% de primes
        $pdf->Cell(100, 6, 'Primes', 1);
        $pdf->Cell(40, 6, number_format($primes, 0, ',', ' ') . ' AR', 1, 1, 'R');
        
        // Salaire brut
        $salaireBrut = $salaireBase + $primes;
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(100, 6, 'SALAIRE BRUT', 1, 0, 'L', true);
        $pdf->Cell(40, 6, number_format($salaireBrut, 0, ',', ' ') . ' AR', 1, 1, 'R', true);
        
        // Cotisations sociales
        $pdf->SetFont('helvetica', '', 9);
        $cotisations = $salaireBrut * 0.13; // 13% de cotisations
        $pdf->Cell(100, 6, 'Cotisations sociales (13%)', 1);
        $pdf->Cell(40, 6, '- ' . number_format($cotisations, 0, ',', ' ') . ' AR', 1, 1, 'R');
        
        // Impôts
        $impots = $salaireBrut * 0.15; // 15% d'impôts
        $pdf->Cell(100, 6, 'Impôts sur le revenu (15%)', 1);
        $pdf->Cell(40, 6, '- ' . number_format($impots, 0, ',', ' ') . ' AR', 1, 1, 'R');
        
        // Salaire net
        $salaireNet = $salaireBrut - $cotisations - $impots;
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(100, 8, 'SALAIRE NET À PAYER', 1, 0, 'L', true);
        $pdf->Cell(40, 8, number_format($salaireNet, 0, ',', ' ') . ' AR', 1, 1, 'R', true);
        
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 5, 'Ce bulletin de paie est confidentiel et personnel.', 0, 1, 'C');
        
        // Sauvegarder
        $filename = 'fiche_paie_' . $employe['nom'] . '_' . date('Ym') . '.pdf';
        $filepath = $this->outputDir . $filename;
        $pdf->Output($filepath, 'F');
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'Fiche de paie générée avec succès'
        ];
    }

    /**
     * Génère un avenant au contrat
     */
    private function genererAvenant($employe)
    {
        $pdf = $this->createPDF('Avenant au Contrat');
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'AVENANT AU CONTRAT DE TRAVAIL', 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('helvetica', '', 11);
        
        $date = date('d/m/Y');
        $dateContrat = date('d/m/Y', strtotime($employe['contrat_debut']));
        
        $texte = "Entre les soussignés :\n\n";
        
        $texte .= "Mon Entreprise SARL, représentée par Madame Rosette RABARIJAONA,\n";
        $texte .= "Ci-après dénommée « L'Employeur »\n\n";
        
        $texte .= "ET\n\n";
        
        $texte .= $employe['sexe'] == 'M' ? "Monsieur " : "Madame ";
        $texte .= strtoupper($employe['nom']) . " " . $employe['prenom'] . "\n";
        $texte .= "Né(e) le : " . date('d/m/Y', strtotime($employe['date_naissance'])) . "\n";
        $texte .= "Ci-après dénommé(e) « Le Salarié »\n\n";
        
        $texte .= "Il a été convenu ce qui suit :\n\n";
        
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'ARTICLE 1 - OBJET', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        
        $texte = "Le présent avenant a pour objet de modifier certaines dispositions du contrat de travail ";
        $texte .= "à durée indéterminée signé le " . $dateContrat . ".\n\n";
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'ARTICLE 2 - MODIFICATIONS', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        
        $salaire = number_format($employe['salaire'], 0, ',', ' ');
        $texte = "À compter du " . $date . ", les modifications suivantes sont apportées :\n\n";
        $texte .= "- Poste : " . $employe['poste'] . "\n";
        $texte .= "- Département : " . $employe['departement'] . "\n";
        $texte .= "- Rémunération mensuelle brute : " . $salaire . " Ariary\n\n";
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'ARTICLE 3 - DISPOSITIONS INCHANGÉES', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        
        $texte = "Toutes les autres clauses du contrat de travail initial demeurent inchangées.\n\n";
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        $texte .= "Fait en deux exemplaires originaux à Antananarivo, le " . $date . "\n\n";
        
        $pdf->MultiCell(0, 5, $texte, 0, 'L');
        
        // Signatures
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', 'B', 11);
        
        $pdf->Cell(90, 10, 'L\'Employeur', 0, 0, 'C');
        $pdf->Cell(90, 10, 'Le Salarié', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(90, 8, 'Rosette RABARIJAONA', 0, 0, 'C');
        $pdf->Cell(90, 8, $employe['nom'] . ' ' . $employe['prenom'], 0, 1, 'C');
        
        $pdf->Ln(5);
        $pdf->Cell(0, 5, 'Fait à Antananarivo, le ' . $date, 0, 1, 'C');
        
        // Sauvegarder
        $filename = 'avenant_' . $employe['nom'] . '_' . date('Ymd_His') . '.pdf';
        $filepath = $this->outputDir . $filename;
        $pdf->Output($filepath, 'F');
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'Avenant au contrat généré avec succès'
        ];
    }

    /**
     * Crée un objet PDF de base avec en-tête et pied de page
     */
    private function createPDF($titre)
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Métadonnées
        $pdf->SetCreator('Système RH');
        $pdf->SetAuthor('Mon Entreprise SARL');
        $pdf->SetTitle($titre);
        
        // Marges
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(true, 20);
        
        // Supprimer l'en-tête et pied de page par défaut
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Ajouter une page
        $pdf->AddPage();
        
        return $pdf;
    }

    /**
     * Récupère toutes les informations complètes d'un employé
     */
    private function getEmployeeFullInfo($userId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.id_employe,
                e.nom,
                e.prenom,
                e.date_naissance,
                e.email,
                e.sexe,
                e.telephone,
                e.adresse,
                p.nom as poste,
                d.nom_departement as departement,
                c.salaire,
                c.date_debut as contrat_debut,
                c.date_fin as contrat_fin
            FROM employe e
            LEFT JOIN contrat c ON e.id_employe = c.id_employe AND (c.date_fin IS NULL OR c.date_fin >= CURDATE())
            LEFT JOIN poste p ON c.id_poste = p.id_poste
            LEFT JOIN departement d ON c.id_departement = d.id_departement
            WHERE e.id_employe = ?
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Liste tous les documents générés pour un employé
     */
    public function listerDocuments($userId)
    {
        $employe = $this->getEmployeeFullInfo($userId);
        if (!$employe) {
            return [];
        }

        $nom = $employe['nom'];
        $files = glob($this->outputDir . '*_' . $nom . '_*.pdf');
        
        $documents = [];
        foreach ($files as $file) {
            $documents[] = [
                'filename' => basename($file),
                'path' => $file,
                'date' => date('d/m/Y H:i', filemtime($file)),
                'size' => filesize($file)
            ];
        }
        
        // Trier par date décroissante
        usort($documents, function($a, $b) {
            return filemtime($b['path']) - filemtime($a['path']);
        });
        
        return $documents;
    }
}
