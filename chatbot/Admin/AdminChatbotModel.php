<?php

namespace chatbot\Admin;

require_once __DIR__ . '/../../app/services/GeminiService.php';
require_once __DIR__ . '/../DocumentGenerator.php';

class AdminChatbotModel
{
    private $db;
    private $gemini;

    public function __construct($db)
    {
        $this->db = $db;
        try {
            // Vérifier si Guzzle est disponible
            if (class_exists('\GuzzleHttp\Client')) {
                $this->gemini = new \GeminiService();
            } else {
                error_log("GeminiService non disponible: Guzzle HTTP Client manquant");
                $this->gemini = null;
            }
        } catch (\Exception $e) {
            error_log("Erreur Gemini: " . $e->getMessage());
            $this->gemini = null;
        }
    }

    /**
     * Répondre aux questions fréquentes
     */
    public function getResponse($message)
    {
        $originalMessage = $message;
        $message = strtolower(trim($message));
        
        // Réponses contextuelles rapides
        if (preg_match('/(bonjour|salut|hello|hi|hey)/i', $message)) {
            return "Bonjour ! Je suis votre assistant RH. Comment puis-je vous aider aujourd'hui ?";
        }
        
        if (preg_match('/(merci|thanks|thx)/i', $message)) {
            return "Je vous en prie ! N'hésitez pas si vous avez d'autres questions.";
        }
        
        // Chercher d'abord dans la base de données chatbot
        try {
            // Recherche exacte par keyword
            $stmt = $this->db->prepare("
                SELECT response 
                FROM chatbot_responses 
                WHERE LOWER(keyword) = ?
                LIMIT 1
            ");
            $stmt->execute([$message]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['response'])) {
                return $result['response'];
            }
            
            // Recherche si le message contient le mot-clé
            $stmt = $this->db->prepare("
                SELECT response 
                FROM chatbot_responses 
                WHERE LOWER(?) LIKE CONCAT('%', LOWER(keyword), '%')
                ORDER BY LENGTH(keyword) DESC
                LIMIT 1
            ");
            $stmt->execute([$message]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['response'])) {
                return $result['response'];
            }
            
            // Recherche dans les synonymes
            $stmt = $this->db->prepare("
                SELECT cr.response 
                FROM chatbot_responses cr
                INNER JOIN chatbot_synonyms cs ON cr.id = cs.keyword_id
                WHERE LOWER(cs.synonym) = ? OR LOWER(?) LIKE CONCAT('%', LOWER(cs.synonym), '%')
                LIMIT 1
            ");
            $stmt->execute([$message, $message]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['response'])) {
                return $result['response'];
            }
        } catch (\Exception $e) {
            error_log("Erreur recherche DB: " . $e->getMessage());
        }
        
        // Base de connaissances pour les questions fréquentes
        $keywords = [
            'congé' => 'conges_guide.txt',
            'paie' => 'politique_paie.txt',
            'salaire' => 'salaire_guide.txt',
            'pointage' => 'pointage_guide.txt',
            'profil' => 'profil_guide.txt',
            'règlement' => 'reglement_interieur.txt'
        ];

        // Recherche du fichier de guide approprié
        $docFile = null;
        foreach ($keywords as $keyword => $file) {
            if (strpos($message, $keyword) !== false) {
                $docFile = __DIR__ . '/../docs_rh/' . $file;
                break;
            }
        }

        // Si un guide est trouvé, l'utiliser comme contexte
        if ($docFile && file_exists($docFile)) {
            $context = file_get_contents($docFile);
            
            if ($this->gemini) {
                try {
                    $prompt = "Tu es un assistant RH expert. Réponds à cette question en te basant sur le contexte fourni.\n\n";
                    $prompt .= "CONTEXTE:\n$context\n\n";
                    $prompt .= "QUESTION: $message\n\n";
                    $prompt .= "Fournis une réponse claire, concise et professionnelle en français.";
                    
                    $response = $this->gemini->genererTexte($prompt);
                    return $response;
                } catch (\Exception $e) {
                    error_log("Erreur Gemini: " . $e->getMessage());
                }
            }
            
            // Si Gemini n'est pas disponible, retourner un extrait du guide
            $lines = explode("\n", $context);
            return implode("\n", array_slice($lines, 0, 10)) . "\n\n[...] Pour plus d'informations, contactez le service RH.";
        }

        // Réponses par défaut selon le type de message
        if (preg_match('/(bonjour|salut|hello|hi)/i', $message)) {
            return "Bonjour ! Je suis votre assistant RH intelligent. Comment puis-je vous aider aujourd'hui ?";
        }
        
        if (preg_match('/(merci|thanks)/i', $message)) {
            return "Je vous en prie ! N'hésitez pas si vous avez d'autres questions.";
        }

        // Réponse par défaut
        return "Je peux vous aider avec vos questions sur les congés, la paie, le pointage, ou toute autre information RH. Quelle est votre question ?";
    }

    /**
     * Réponses privilégiées pour l'interface Admin (accès aux données RH)
     */
    public function getAdminResponse($message)
    {
        $msg = strtolower(trim($message));

        // Intent: Recherche d'informations sur un employé spécifique
        // Ex: "numéro de Jean Rakoto", "poste de Lucie Ramanantsoa", "adresse de Marc Dupont"
        if (preg_match('/(num[ée]ro|t[ée]l[ée]phone|contact|appeler|joindre).*(de |d\')?([A-Z][a-zéèêàâïîôù]+(\s+[A-Z][a-zéèêàâïîôù]+)+)/iu', $message, $matches) ||
            preg_match('/(poste|fonction|travail|job).*(de |d\')?([A-Z][a-zéèêàâïîôù]+(\s+[A-Z][a-zéèêàâïîôù]+)+)/iu', $message, $matches) ||
            preg_match('/(adresse|habite|domicile).*(de |d\')?([A-Z][a-zéèêàâïîôù]+(\s+[A-Z][a-zéèêàâïîôù]+)+)/iu', $message, $matches) ||
            preg_match('/(email|mail|e-mail|courriel).*(de |d\')?([A-Z][a-zéèêàâïîôù]+(\s+[A-Z][a-zéèêàâïîôù]+)+)/iu', $message, $matches) ||
            preg_match('/(salaire|paie|r[ée]mun[ée]ration).*(de |d\')?([A-Z][a-zéèêàâïîôù]+(\s+[A-Z][a-zéèêàâïîôù]+)+)/iu', $message, $matches) ||
            preg_match('/(info|infos|informations?|d[ée]tails?|donn[ée]es?).*(de |d\'|sur\s+)?([A-Z][a-zéèêàâïîôù]+(\s+[A-Z][a-zéèêàâïîôù]+)+)/iu', $message, $matches) ||
            preg_match('/([A-Z][a-zéèêàâïîôù]+(\s+[A-Z][a-zéèêàâïîôù]+)+).*(num[ée]ro|t[ée]l[ée]phone|poste|adresse|email|salaire)/iu', $message, $matches)) {
            
            $employeInfo = $this->getEmployeeInfo($message);
            return $employeInfo;
        }

        // Intent: combien de congés en attente
        if (preg_match('/(combien|nombre).*(cong[eé]s?).*(attente|en attente|en attente)/i', $msg) || preg_match('/cong[eé]s? en attente/i', $msg)) {
            $count = $this->countPendingConges();
            return "Il y a actuellement $count congé(s) en attente de validation.";
        }

        // Intent: qui est/part/sont en congé (cette semaine, actuellement, maintenant, aujourd'hui)
        if (preg_match('/qui.*(est|sont|part|vont).*(en )?cong[eé]e?s?/i', $msg) || 
            preg_match('/cong[eé]e?s?.*(cette semaine|semaine|actuellement|maintenant|en cours|aujourd\'?hui)/i', $msg) ||
            preg_match('/(qui|quels?|employés?).*(actuellement|maintenant|cette semaine|semaine).*(cong[eé]e?s?|absent)/i', $msg)) {
            $rows = $this->getCongesThisWeek();
            if (empty($rows)) {
                return "Aucun employé n'est en congé prévu cette semaine.";
            }
            $lines = [];
            foreach ($rows as $r) {
                $lines[] = "{$r['nom']} {$r['prenom']} : {$r['date_debut']} → {$r['date_fin']} ({$r['raison']})";
            }
            return "Congés cette semaine:\n" . implode("\n", $lines);
        }

        // Intent: pour quel raison / raisons des congés
        if (preg_match('/pour quelle raison|pourquoi.*cong[eé]/i', $msg) || preg_match('/raisons? des cong[eé]s?/i', $msg)) {
            $rows = $this->getRecentCongesReasons(30);
            if (empty($rows)) {
                return "Aucune demande de congé récente trouvée.";
            }
            $summary = [];
            foreach ($rows as $r) {
                $summary[] = "{$r['nom']} {$r['prenom']}: {$r['raison']} ({$r['date_debut']}→{$r['date_fin']})";
            }
            return "Raisons des congés récents:\n" . implode("\n", $summary);
        }

        // Intent: paie / bulletins
        if (preg_match('/paie|bulletin|fiche de paie|salaire/i', $msg)) {
            $paie = $this->getPaieSummary();
            return "Résumé paie: employés=" . $paie['total_employes'] . ", fiches paie ce mois=" . $paie['fiches_ce_mois'] . ", employés sans fiche=" . $paie['missing_fiches'] . ".";
        }

        // Intent: retard / absences
        if (preg_match('/retard|retards|absen|absences|absent/i', $msg)) {
            $retards = $this->getRecentRetards(1); // last month
            if (empty($retards)) {
                return "Aucun retard notable trouvé sur la période demandée.";
            }
            $lines = [];
            foreach ($retards as $r) {
                $lines[] = "{$r['nom']} {$r['prenom']}: {$r['retards']} retard(s)";
            }
            return "Retards récents:\n" . implode("\n", $lines);
        }

        // Fallback to generic handler (search DB/guides/AI)
        return $this->getResponse($message);
    }

    // ---------- Helpers for admin queries ----------
    private function countPendingConges()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as c FROM conge WHERE status = 1");
        $stmt->execute();
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        return intval($r['c'] ?? 0);
    }

    private function getCongesThisWeek()
    {
        // Determine start and end of current week (Monday-Sunday)
        $stmt = $this->db->prepare("SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())) DAY) as week_start, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())) DAY), INTERVAL 6 DAY) as week_end");
        $stmt->execute();
        $range = $stmt->fetch(\PDO::FETCH_ASSOC);
        $start = $range['week_start'];
        $end = $range['week_end'];

        $q = "SELECT c.*, e.nom, e.prenom FROM conge c JOIN employe e ON e.id_employe = c.id_employe WHERE (
                (c.date_debut BETWEEN ? AND ?) OR (c.date_fin BETWEEN ? AND ?) OR (c.date_debut <= ? AND c.date_fin >= ?)
            ) AND c.status IN (1,11,21) ORDER BY c.date_debut";
        $stmt = $this->db->prepare($q);
        $stmt->execute([$start, $end, $start, $end, $start, $end]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getRecentCongesReasons($days = 30)
    {
        $stmt = $this->db->prepare("SELECT c.*, e.nom, e.prenom FROM conge c JOIN employe e ON e.id_employe = c.id_employe WHERE c.date_demande >= DATE_SUB(NOW(), INTERVAL ? DAY) ORDER BY c.date_demande DESC LIMIT 50");
        $stmt->execute([$days]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPaieSummary()
    {
        // total employees
        $stmt = $this->db->query("SELECT COUNT(*) as t FROM employe");
        $total = intval($stmt->fetch(\PDO::FETCH_ASSOC)['t'] ?? 0);

        // fiches paie this month
        $stmt = $this->db->prepare("SELECT COUNT(*) as f FROM fiche_paie WHERE MONTH(date_fiche) = MONTH(NOW()) AND YEAR(date_fiche) = YEAR(NOW())");
        $stmt->execute();
        $fiches = intval($stmt->fetch(\PDO::FETCH_ASSOC)['f'] ?? 0);

        return ['total_employes' => $total, 'fiches_ce_mois' => $fiches, 'missing_fiches' => max(0, $total - $fiches)];
    }

    private function getRecentRetards($months = 1)
    {
        // Best-effort: count arrivals after 09:10 in the given period
        $stmt = $this->db->prepare("SELECT e.id_employe, e.nom, e.prenom, COUNT(*) as retards FROM pointage p JOIN employe e ON e.id_employe = p.id_employe WHERE p.date_heure_arrive >= DATE_SUB(NOW(), INTERVAL ? MONTH) AND TIME(p.date_heure_arrive) > '09:10:00' GROUP BY e.id_employe HAVING retards > 0 ORDER BY retards DESC LIMIT 20");
        $stmt->execute([$months]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Rechercher des informations sur un employé par nom/prénom
     */
    private function getEmployeeInfo($message)
    {
        // Extraire le nom de l'employé du message
        $names = $this->extractEmployeeName($message);
        
        if (empty($names)) {
            return "Je n'ai pas pu identifier le nom de l'employé dans votre question. Veuillez préciser le nom et prénom.";
        }

        // Rechercher l'employé dans la base
        $employe = $this->findEmployeeByName($names);
        
        if (!$employe) {
            return "Aucun employé trouvé avec le nom \"" . implode(' ', $names) . "\". Veuillez vérifier l'orthographe.";
        }

        // Déterminer quelle information est demandée - en priorité par rapport aux infos complètes
        $msg = strtolower($message);
        
        // Numéro de téléphone
        if (preg_match('/(num[ée]ro|t[ée]l[ée]phone|contact|appeler|joindre)/iu', $msg)) {
            $tel = $employe['telephone'] ?? 'Non renseigné';
            return "Le numéro de {$employe['prenom']} {$employe['nom']} est :\n📞 Téléphone: {$tel}";
        }
        
        // Email
        if (preg_match('/(email|mail|e-mail|courriel)/iu', $msg)) {
            $email = $employe['email'] ?? 'Non renseigné';
            return "L'email de {$employe['prenom']} {$employe['nom']} est :\n📧 Email: {$email}";
        }
        
        // Adresse
        if (preg_match('/(adresse|habite|domicile|où)/iu', $msg)) {
            $adresse = $employe['adresse'] ?? 'Non renseignée';
            return "L'adresse de {$employe['prenom']} {$employe['nom']} est :\n🏠 Adresse: {$adresse}";
        }
        
        // Poste
        if (preg_match('/(poste|fonction|travail|job)/iu', $msg)) {
            $poste = $employe['nom_poste'] ?? 'Non renseigné';
            $dept = $employe['nom_departement'] ?? 'Non renseigné';
            return "Les informations professionnelles de {$employe['prenom']} {$employe['nom']} :\n💼 Poste: {$poste}\n🏢 Département: {$dept}";
        }
        
        // Salaire
        if (preg_match('/(salaire|paie|r[ée]mun[ée]ration|combien gagne)/iu', $msg)) {
            $salaire = isset($employe['salaire']) ? number_format($employe['salaire'], 0, ',', ' ') . ' Ar' : 'Non renseigné';
            return "Le salaire de {$employe['prenom']} {$employe['nom']} est :\n💰 Salaire: {$salaire}";
        }
        
        // Informations complètes par défaut
        $info = [];
        $info[] = "**{$employe['prenom']} {$employe['nom']}** (ID: {$employe['id_employe']})";
        $info[] = "📞 Téléphone: " . ($employe['telephone'] ?? 'Non renseigné');
        $info[] = "📧 Email: " . ($employe['email'] ?? 'Non renseigné');
        $info[] = "🏠 Adresse: " . ($employe['adresse'] ?? 'Non renseignée');
        $info[] = "💼 Poste: " . ($employe['nom_poste'] ?? 'Non renseigné');
        $info[] = "🏢 Département: " . ($employe['nom_departement'] ?? 'Non renseigné');
        $info[] = "💰 Salaire: " . (isset($employe['salaire']) ? number_format($employe['salaire'], 0, ',', ' ') . ' Ar' : 'Non renseigné');
        $info[] = "📅 Date de naissance: " . ($employe['date_naissance'] ?? 'Non renseignée');
        $info[] = "👤 Sexe: " . ($employe['sexe'] ?? 'Non renseigné');
        
        return implode("\n", $info);
    }

    /**
     * Extraire le nom de l'employé du message
     */
    private function extractEmployeeName($message)
    {
        // Patterns pour extraire les noms
        $patterns = [
            '/(de |d\'|sur\s+)?([A-Z][a-zéèêàâïîôù]+(?:\s+[A-Z][a-zéèêàâïîôù]+)+)/u',
            '/([A-Z][a-zéèêàâïîôù]+(?:\s+[A-Z][a-zéèêàâïîôù]+)+)/u'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $fullName = isset($matches[2]) ? $matches[2] : $matches[1];
                $fullName = trim($fullName);
                $parts = preg_split('/\s+/', $fullName);
                return $parts;
            }
        }
        
        return [];
    }

    /**
     * Rechercher un employé par nom et prénom
     */
    private function findEmployeeByName($names)
    {
        if (count($names) < 2) {
            return null;
        }

        // Essayer différentes combinaisons nom/prénom
        $queries = [];
        
        // Cas 1: Prénom Nom
        $queries[] = [
            'prenom' => $names[0],
            'nom' => implode(' ', array_slice($names, 1))
        ];
        
        // Cas 2: Nom Prénom
        $queries[] = [
            'nom' => $names[0],
            'prenom' => implode(' ', array_slice($names, 1))
        ];
        
        // Cas 3: Si 3 mots ou plus, essayer différentes combinaisons
        if (count($names) >= 3) {
            $queries[] = [
                'prenom' => implode(' ', array_slice($names, 0, 2)),
                'nom' => $names[count($names) - 1]
            ];
        }

        foreach ($queries as $query) {
            // D'abord trouver l'employé
            $stmt = $this->db->prepare("
                SELECT * FROM employe 
                WHERE LOWER(nom) LIKE LOWER(?) 
                  AND LOWER(prenom) LIKE LOWER(?)
                LIMIT 1
            ");
            
            $nom = '%' . $query['nom'] . '%';
            $prenom = '%' . $query['prenom'] . '%';
            $stmt->execute([$nom, $prenom]);
            $employe = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$employe) {
                continue;
            }
            
            // Ensuite récupérer son contrat le plus récent avec poste et département
            $stmt2 = $this->db->prepare("
                SELECT c.*, p.nom as nom_poste, d.nom_departement
                FROM contrat c
                LEFT JOIN poste p ON c.id_poste = p.id_poste
                LEFT JOIN departement d ON c.id_departement = d.id_departement
                WHERE c.id_employe = ?
                ORDER BY 
                    CASE 
                        WHEN c.date_fin IS NULL THEN 0
                        WHEN c.date_fin >= CURDATE() THEN 1
                        ELSE 2
                    END,
                    c.date_debut DESC
                LIMIT 1
            ");
            $stmt2->execute([$employe['id_employe']]);
            $contrat = $stmt2->fetch(\PDO::FETCH_ASSOC);
            
            // Fusionner les données
            if ($contrat) {
                $result = array_merge($employe, [
                    'nom_poste' => $contrat['nom_poste'],
                    'nom_departement' => $contrat['nom_departement'],
                    'salaire' => $contrat['salaire'],
                    'date_debut' => $contrat['date_debut'],
                    'date_fin' => $contrat['date_fin']
                ]);
            } else {
                $result = $employe;
            }
            
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Générer un document RH
     */
    public function generateDocument($type, $employeId, $params = [])
    {
        try {
            // Récupérer les informations complètes de l'employé
            $employe = $this->getEmployeFullInfo($employeId);
            
            if (!$employe) {
                return ['error' => 'Employé non trouvé'];
            }
            
            $docGen = new \chatbot\DocumentGenerator($this->db);
            
            switch ($type) {
                case 'contrat':
                    $result = $docGen->genererContrat($employe, $params);
                    break;
                    
                case 'attestation':
                    $result = $docGen->genererAttestation($employe, $params);
                    break;
                    
                case 'fiche_paie':
                    $mois = $params['mois'] ?? date('m');
                    $annee = $params['annee'] ?? date('Y');
                    $result = $docGen->genererFichePaie($employe, $mois, $annee);
                    break;
                    
                case 'certificat':
                    $result = $docGen->genererCertificat($employe, $params);
                    break;
                    
                default:
                    return ['error' => 'Type de document non supporté'];
            }
            
            return ['success' => true, 'data' => $result];
            
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Prédire le turnover des employés
     */
    public function predictTurnover($employeId = null)
    {
        try {
            if ($employeId) {
                // Analyse pour un employé spécifique
                $employe = $this->getEmployeData($employeId);
                $prediction = $this->analyzeSingleEmployeeTurnover($employe);
            } else {
                // Analyse globale
                $employes = $this->getAllEmployesData();
                $prediction = $this->analyzeGlobalTurnover($employes);
            }
            
            return ['success' => true, 'data' => $prediction];
            
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Analyser le risque de départ d'un employé
     */
    private function analyzeSingleEmployeeTurnover($employe)
    {
        $score = 0;
        $factors = [];

        // Facteur 1: Ancienneté (courbe en U)
        $anciennete = $this->calculerAnciennete($employe['contrat_debut'] ?? date('Y-m-d'));
        if ($anciennete < 1) {
            $score += 30;
            $factors[] = "Nouvelle recrue (< 1 an) - Risque élevé d'adaptation";
        } elseif ($anciennete > 5) {
            $score += 20;
            $factors[] = "Ancienneté élevée (> 5 ans) - Possible recherche de nouveaux défis";
        }

        // Facteur 2: Absences/Retards
        $absences = $this->getAbsencesCount($employe['id_employe'], 6); // 6 derniers mois
        if ($absences > 5) {
            $score += 25;
            $factors[] = "Absences fréquentes ($absences en 6 mois) - Signe de désengagement";
        }

        // Facteur 3: Congés non pris
        $congesNonPris = $this->getCongesNonPris($employe['id_employe']);
        if ($congesNonPris > 10) {
            $score += 15;
            $factors[] = "Congés non pris ($congesNonPris jours) - Possible surcharge";
        }

        // Facteur 4: Heures supplémentaires
        $heuresSup = $this->getHeuresSupCount($employe['id_employe'], 3);
        if ($heuresSup > 40) {
            $score += 20;
            $factors[] = "Heures sup excessives ($heuresSup h en 3 mois) - Burnout possible";
        }

        // Facteur 5: Pas de promotion récente
        $dernierePromotion = $this->getDernierePromotion($employe['id_employe']);
        if ($anciennete > 2 && !$dernierePromotion) {
            $score += 15;
            $factors[] = "Pas d'évolution professionnelle - Stagnation";
        }

        // Déterminer le niveau de risque
        $risk_level = 'Faible';
        if ($score >= 60) {
            $risk_level = 'Élevé';
        } elseif ($score >= 40) {
            $risk_level = 'Moyen';
        }

        $recommendation = $this->generateTurnoverRecommendation($score, $factors);

        return [
            'employe' => $employe['nom'] . ' ' . $employe['prenom'],
            'score' => $score,
            'risk_level' => $risk_level,
            'factors' => $factors,
            'recommendation' => $recommendation
        ];
    }

    /**
     * Analyser le turnover global
     */
    private function analyzeGlobalTurnover($employes)
    {
        $highRisk = [];
        $mediumRisk = [];
        $lowRisk = [];

        foreach ($employes as $employe) {
            $analysis = $this->analyzeSingleEmployeeTurnover($employe);
            
            if ($analysis['risk_level'] === 'Élevé') {
                $highRisk[] = $analysis;
            } elseif ($analysis['risk_level'] === 'Moyen') {
                $mediumRisk[] = $analysis;
            } else {
                $lowRisk[] = $analysis;
            }
        }

        return [
            'total_employes' => count($employes),
            'high_risk' => [
                'count' => count($highRisk),
                'percentage' => round((count($highRisk) / count($employes)) * 100, 2),
                'employes' => $highRisk
            ],
            'medium_risk' => [
                'count' => count($mediumRisk),
                'percentage' => round((count($mediumRisk) / count($employes)) * 100, 2),
                'employes' => $mediumRisk
            ],
            'low_risk' => [
                'count' => count($lowRisk),
                'percentage' => round((count($lowRisk) / count($employes)) * 100, 2)
            ]
        ];
    }

    /**
     * Détecter les anomalies
     */
    public function detectAnomalies($type, $period)
    {
        try {
            $anomalies = [];

            if ($type === 'hours' || $type === 'all') {
                $anomalies['hours'] = $this->detectHoursAnomalies($period);
            }

            if ($type === 'salary' || $type === 'all') {
                $anomalies['salary'] = $this->detectSalaryAnomalies($period);
            }

            return ['success' => true, 'data' => $anomalies];
            
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Détecter les anomalies sur les heures
     */
    private function detectHoursAnomalies($period)
    {
        $anomalies = [];
        $dateCondition = $this->getPeriodCondition($period);

        // Détection 1: Heures excessives
        $query = "SELECT e.id_employe, e.nom, e.prenom, 
                  SUM(TIMESTAMPDIFF(HOUR, p.date_heure_arrive, p.date_heure_depart)) as total_heures
                  FROM employe e
                  JOIN pointage p ON e.id_employe = p.id_employe
                  WHERE $dateCondition
                  GROUP BY e.id_employe
                  HAVING total_heures > 200"; // Plus de 200h par mois

        $stmt = $this->db->query($query);
        $excessiveHours = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if ($excessiveHours) {
            $anomalies[] = [
                'type' => 'Heures excessives',
                'severity' => 'high',
                'count' => count($excessiveHours),
                'details' => $excessiveHours
            ];
        }

        // Détection 2: Pointages manquants
        $query = "SELECT e.id_employe, e.nom, e.prenom, 
                  COUNT(DISTINCT DATE(p.date_heure_arrive)) as jours_pointes
                  FROM employe e
                  LEFT JOIN pointage p ON e.id_employe = p.id_employe AND $dateCondition
                  GROUP BY e.id_employe
                  HAVING jours_pointes < 15"; // Moins de 15 jours pointés par mois

        $stmt = $this->db->query($query);
        $missingPointages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if ($missingPointages) {
            $anomalies[] = [
                'type' => 'Pointages manquants',
                'severity' => 'medium',
                'count' => count($missingPointages),
                'details' => $missingPointages
            ];
        }

        // Détection 3: Heures de nuit suspectes
        $query = "SELECT e.id_employe, e.nom, e.prenom, COUNT(*) as pointages_nuit
                  FROM employe e
                  JOIN pointage p ON e.id_employe = p.id_employe
                  WHERE $dateCondition
                  AND (HOUR(p.date_heure_arrive) >= 22 OR HOUR(p.date_heure_arrive) <= 5)
                  GROUP BY e.id_employe
                  HAVING pointages_nuit > 5";

        $stmt = $this->db->query($query);
        $nightWork = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if ($nightWork) {
            $anomalies[] = [
                'type' => 'Pointages nocturnes inhabituels',
                'severity' => 'medium',
                'count' => count($nightWork),
                'details' => $nightWork
            ];
        }

        return $anomalies;
    }

    /**
     * Détecter les anomalies sur la paie
     */
    private function detectSalaryAnomalies($period)
    {
        $anomalies = [];
        $dateCondition = $this->getPeriodCondition($period, 'fp1.date_fiche');

        // Détection 1: Variations salariales importantes
        $query = "SELECT fp1.id_employe, e.nom, e.prenom,
                  fp1.salaire_brut as salaire_actuel,
                  fp2.salaire_brut as salaire_precedent,
                  ((fp1.salaire_brut - fp2.salaire_brut) / fp2.salaire_brut * 100) as variation_pct
                  FROM fiche_paie fp1
                  JOIN employe e ON fp1.id_employe = e.id_employe
                  JOIN fiche_paie fp2 ON fp2.id_employe = fp1.id_employe
                  WHERE $dateCondition
                  AND fp2.date_fiche = (
                      SELECT MAX(date_fiche) 
                      FROM fiche_paie 
                      WHERE id_employe = fp1.id_employe 
                      AND date_fiche < fp1.date_fiche
                  )
                  HAVING ABS(variation_pct) > 20"; // Variation > 20%

        $stmt = $this->db->query($query);
        $salaryVariations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if ($salaryVariations) {
            $anomalies[] = [
                'type' => 'Variations salariales importantes',
                'severity' => 'high',
                'count' => count($salaryVariations),
                'details' => $salaryVariations
            ];
        }

        // Détection 2: Salaires hors normes
        $query = "SELECT e.id_employe, e.nom, e.prenom, c.salaire, p.nom as nom_poste,
                  AVG(c2.salaire) as salaire_moyen_poste
                  FROM employe e
                  JOIN contrat c ON e.id_employe = c.id_employe AND c.date_fin IS NULL
                  JOIN poste p ON c.id_poste = p.id_poste
                  JOIN contrat c2 ON c2.id_poste = p.id_poste AND c2.date_fin IS NULL
                  GROUP BY e.id_employe
                  HAVING (c.salaire > salaire_moyen_poste * 1.5 
                      OR c.salaire < salaire_moyen_poste * 0.5)";

        $stmt = $this->db->query($query);
        $abnormalSalaries = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if ($abnormalSalaries) {
            $anomalies[] = [
                'type' => 'Salaires hors normes du poste',
                'severity' => 'medium',
                'count' => count($abnormalSalaries),
                'details' => $abnormalSalaries
            ];
        }

        // Détection 3: Primes excessives
        $query = "SELECT e.id_employe, e.nom, e.prenom,
                  SUM(pd.montant_prime) as total_primes, c.salaire
                  FROM employe e
                  JOIN prime_divers pd ON e.id_employe = pd.id_employe
                  JOIN contrat c ON e.id_employe = c.id_employe AND c.date_fin IS NULL
                  WHERE pd.date_prime >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                  GROUP BY e.id_employe
                  HAVING total_primes > c.salaire * 0.5";        $stmt = $this->db->query($query);
        $excessivePrimes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if ($excessivePrimes) {
            $anomalies[] = [
                'type' => 'Primes excessives',
                'severity' => 'medium',
                'count' => count($excessivePrimes),
                'details' => $excessivePrimes
            ];
        }

        return $anomalies;
    }

    /**
     * Recommander des candidats pour un poste
     */
    public function recommendCandidates($posteId, $jobDescription, $skills)
    {
        try {
            // Récupérer les informations du poste
            $poste = null;
            if ($posteId) {
                $stmt = $this->db->prepare("SELECT * FROM poste WHERE id_poste = ?");
                $stmt->execute([$posteId]);
                $poste = $stmt->fetch(\PDO::FETCH_ASSOC);
            }

            // Récupérer tous les candidats/employés potentiels
            $candidates = $this->getCandidatesData();

            // Scoring avec IA si disponible
            if ($this->gemini && ($jobDescription || $poste)) {
                $recommendations = $this->scoreWithAI($candidates, $poste, $jobDescription, $skills);
            } else {
                $recommendations = $this->scoreWithRules($candidates, $poste, $skills);
            }

            return ['success' => true, 'data' => $recommendations];
            
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Scoring des candidats avec IA
     */
    private function scoreWithAI($candidates, $poste, $jobDescription, $skills)
    {
        $recommendations = [];

        foreach ($candidates as $candidate) {
            try {
                $prompt = "Tu es un expert en recrutement RH. Évalue ce candidat pour le poste.\n\n";
                
                if ($poste) {
                    $prompt .= "POSTE: {$poste['nom_poste']}\n";
                    $prompt .= "Description: {$poste['description']}\n\n";
                }
                
                if ($jobDescription) {
                    $prompt .= "DESCRIPTION DU POSTE: $jobDescription\n\n";
                }
                
                if ($skills) {
                    $prompt .= "COMPÉTENCES REQUISES: " . implode(', ', $skills) . "\n\n";
                }
                
                $prompt .= "CANDIDAT:\n";
                $prompt .= "Nom: {$candidate['nom']} {$candidate['prenom']}\n";
                $prompt .= "Formation: {$candidate['formation']}\n";
                $prompt .= "Expérience: {$candidate['experience']} ans\n";
                $prompt .= "Compétences: {$candidate['competences']}\n\n";
                $prompt .= "Fournis un score de 0 à 100 et une justification courte (2-3 lignes).";
                $prompt .= "Format: SCORE: [nombre]\nJUSTIFICATION: [texte]";

                $response = $this->gemini->genererTexte($prompt);
                
                // Parser la réponse
                preg_match('/SCORE:\s*(\d+)/', $response, $scoreMatch);
                preg_match('/JUSTIFICATION:\s*(.+)/s', $response, $justMatch);
                
                $score = isset($scoreMatch[1]) ? intval($scoreMatch[1]) : 50;
                $justification = isset($justMatch[1]) ? trim($justMatch[1]) : "Évaluation automatique";

                $recommendations[] = [
                    'candidate' => $candidate,
                    'score' => $score,
                    'justification' => $justification,
                    'match_level' => $this->getMatchLevel($score)
                ];
                
            } catch (\Exception $e) {
                // Fallback sur scoring par règles
                $recommendations[] = $this->scoreCandidate($candidate, $skills);
            }
        }

        // Trier par score décroissant
        usort($recommendations, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return array_slice($recommendations, 0, 10); // Top 10
    }

    /**
     * Scoring des candidats avec règles
     */
    private function scoreWithRules($candidates, $poste, $skills)
    {
        $recommendations = [];

        foreach ($candidates as $candidate) {
            $scored = $this->scoreCandidate($candidate, $skills);
            $recommendations[] = $scored;
        }

        usort($recommendations, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return array_slice($recommendations, 0, 10);
    }

    /**
     * Scorer un candidat
     */
    private function scoreCandidate($candidate, $requiredSkills)
    {
        $score = 0;
        $factors = [];

        // Expérience
        $experience = intval($candidate['experience'] ?? 0);
        if ($experience >= 5) {
            $score += 30;
            $factors[] = "Expérience solide ($experience ans)";
        } elseif ($experience >= 2) {
            $score += 20;
            $factors[] = "Expérience moyenne ($experience ans)";
        } else {
            $score += 10;
            $factors[] = "Expérience limitée ($experience ans)";
        }

        // Compétences
        $candidateSkills = explode(',', strtolower($candidate['competences'] ?? ''));
        $matchedSkills = 0;
        
        foreach ($requiredSkills as $skill) {
            foreach ($candidateSkills as $cSkill) {
                if (strpos(trim($cSkill), strtolower($skill)) !== false) {
                    $matchedSkills++;
                    break;
                }
            }
        }
        
        if (count($requiredSkills) > 0) {
            $skillScore = ($matchedSkills / count($requiredSkills)) * 40;
            $score += $skillScore;
            $factors[] = "Compétences: $matchedSkills/" . count($requiredSkills) . " requises";
        }

        // Formation
        if (!empty($candidate['formation'])) {
            $score += 20;
            $factors[] = "Formation: {$candidate['formation']}";
        }

        // Performance (si employé actuel)
        if (isset($candidate['performance'])) {
            $score += $candidate['performance'] * 10;
            $factors[] = "Performance: {$candidate['performance']}/10";
        }

        return [
            'candidate' => $candidate,
            'score' => round($score),
            'factors' => $factors,
            'match_level' => $this->getMatchLevel($score)
        ];
    }

    /**
     * Analyser un CV uploadé
     */
    public function analyzeCV($file)
    {
        try {
            // Vérifier le type de fichier
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                return ['error' => 'Type de fichier non supporté. Utilisez PDF ou DOC'];
            }

            // Extraction basique des informations du CV
            $cvText = '';
            
            // Si c'est un PDF, essayer d'extraire le texte
            if ($file['type'] === 'application/pdf') {
                // Utiliser une méthode simple pour extraire le texte
                // Note: Pour une extraction robuste, utiliser une bibliothèque comme smalot/pdfparser
                $cvText = $this->extractTextFromPDF($file['tmp_name']);
            }
            
            // Analyser le texte extrait
            $analysis = $this->parseCV($cvText, $file['name']);
            
            // Calculer un score global basé sur les informations trouvées
            $score = 0;
            if (!empty($analysis['email']) && $analysis['email'] !== 'À extraire du CV') $score += 15;
            if (!empty($analysis['telephone']) && $analysis['telephone'] !== 'À extraire du CV') $score += 15;
            if (!empty($analysis['poste_vise']) && $analysis['poste_vise'] !== 'Non spécifié') $score += 10;
            if (count($analysis['competences']) > 0) $score += 20;
            if (count($analysis['competences']) > 5) $score += 10;
            if (count($analysis['experience']) > 0) $score += 15;
            if (count($analysis['experience']) > 2) $score += 10;
            if (count($analysis['formation']) > 0) $score += 15;
            
            $analysis['score_global'] = $score;

            return ['success' => true, 'data' => $analysis];
            
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Extraire le texte d'un PDF (méthode basique)
     */
    private function extractTextFromPDF($filepath)
    {
        // Essayer d'utiliser la bibliothèque smalot/pdfparser si disponible
        if (class_exists('\Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filepath);
                return $pdf->getText();
            } catch (\Exception $e) {
                // Continuer avec les autres méthodes
            }
        }
        
        // Méthode simple utilisant pdftotext si disponible
        if (function_exists('shell_exec')) {
            $output = shell_exec("pdftotext " . escapeshellarg($filepath) . " -");
            if ($output) return $output;
        }
        
        // Essayer d'extraire avec file_get_contents (méthode basique)
        $content = @file_get_contents($filepath);
        if ($content) {
            // Extraction très basique du texte d'un PDF
            if (preg_match_all('/\((.*?)\)/s', $content, $matches)) {
                return implode(' ', $matches[1]);
            }
        }
        
        return '';
    }

    /**
     * Parser le contenu du CV pour extraire les informations
     */
    private function parseCV($text, $filename)
    {
        $analysis = [
            'nom_complet' => $this->extractName($text, $filename),
            'email' => $this->extractEmail($text),
            'telephone' => $this->extractPhone($text),
            'poste_vise' => $this->extractTargetPosition($text),
            'formation' => $this->extractEducation($text),
            'experience' => $this->extractExperience($text),
            'competences' => $this->extractSkills($text),
            'langues' => $this->extractLanguages($text),
            'score_global' => 0
        ];

        return $analysis;
    }

    private function extractName($text, $filename)
    {
        // Essayer d'extraire le nom depuis le nom du fichier
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['_', '-', 'CV', 'cv'], ' ', $name);
        $name = trim($name);
        
        // Si le nom extrait est vide ou trop court, chercher dans le texte
        if (strlen($name) < 3 && !empty($text)) {
            // Chercher des patterns de nom au début du CV
            $lines = explode("\n", $text);
            foreach ($lines as $line) {
                $line = trim($line);
                // Un nom est généralement en majuscules au début
                if (preg_match('/^[A-ZÉÈÊËÀÂÄÏÎÔÙÛÇ]{2,}\s+[A-ZÉÈÊËÀÂÄÏÎÔÙÛÇa-zéèêëàâäïîôùûç]{2,}/u', $line)) {
                    return $line;
                }
            }
        }
        
        return $name ?: 'À extraire du CV';
    }

    private function extractEmail($text)
    {
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            return $matches[0];
        }
        return 'À extraire du CV';
    }

    private function extractPhone($text)
    {
        // Patterns pour différents formats de téléphone
        $patterns = [
            '/(\+261|0)\s?3[2-4]\s?\d{2}\s?\d{3}\s?\d{2}/',  // Madagascar
            '/(\+?\d{1,3}[-.\s]?)?\(?\d{2,3}\)?[-.\s]?\d{2,3}[-.\s]?\d{2,4}[-.\s]?\d{2,4}/',  // Général
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[0]);
            }
        }
        return 'À extraire du CV';
    }

    private function extractTargetPosition($text)
    {
        // Chercher des mots-clés indiquant le poste visé
        $keywords = [
            'Poste visé', 'Poste recherché', 'Objectif professionnel', 'Objectif',
            'Titre', 'Position', 'Candidature pour', 'Candidature au poste'
        ];
        
        $lines = explode("\n", $text);
        foreach ($lines as $i => $line) {
            foreach ($keywords as $keyword) {
                if (stripos($line, $keyword) !== false) {
                    // Le poste est souvent sur la même ligne ou la ligne suivante
                    $position = str_replace($keyword, '', $line);
                    $position = trim(str_replace([':', '-'], '', $position));
                    
                    if (strlen($position) > 3) {
                        return $position;
                    } elseif (isset($lines[$i + 1])) {
                        return trim($lines[$i + 1]);
                    }
                }
            }
        }
        
        // Chercher des patterns de postes courants
        $commonPositions = [
            'Développeur', 'Developer', 'Ingénieur', 'Engineer', 'Manager', 'Directeur',
            'Chef de projet', 'Project Manager', 'Analyste', 'Consultant', 'Designer',
            'Administrateur', 'Technicien', 'Assistant', 'Responsable', 'Coordinateur',
            'Développeur Web', 'Développeur Full Stack', 'Data Scientist', 'DevOps'
        ];
        
        foreach ($commonPositions as $position) {
            if (stripos($text, $position) !== false) {
                return $position;
            }
        }
        
        return 'Non spécifié';
    }

    private function extractEducation($text)
    {
        $education = [];
        $keywords = ['Diplôme', 'Formation', 'Université', 'École', 'Licence', 'Master', 'Doctorat', 'Bachelor', 'MBA'];
        
        $lines = explode("\n", $text);
        $inEducationSection = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Détecter le début de la section formation
            foreach ($keywords as $keyword) {
                if (stripos($line, $keyword) !== false && strlen($line) < 50) {
                    $inEducationSection = true;
                    break;
                }
            }
            
            // Arrêter si on entre dans une autre section
            if (preg_match('/(Expérience|Compétence|Langue)/i', $line) && strlen($line) < 30) {
                $inEducationSection = false;
            }
            
            // Extraire les formations
            if ($inEducationSection && strlen($line) > 10) {
                // Chercher des années
                if (preg_match('/\b(19|20)\d{2}\b/', $line)) {
                    $education[] = $line;
                }
            }
        }
        
        return $education;
    }

    private function extractExperience($text)
    {
        $experience = [];
        $keywords = ['Expérience', 'Parcours', 'Carrière', 'Experience', 'Work'];
        
        $lines = explode("\n", $text);
        $inExperienceSection = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Détecter le début de la section expérience
            foreach ($keywords as $keyword) {
                if (stripos($line, $keyword) !== false && strlen($line) < 50) {
                    $inExperienceSection = true;
                    break;
                }
            }
            
            // Arrêter si on entre dans une autre section
            if (preg_match('/(Formation|Diplôme|Compétence|Langue)/i', $line) && strlen($line) < 30) {
                $inExperienceSection = false;
            }
            
            // Extraire les expériences
            if ($inExperienceSection && strlen($line) > 10) {
                // Chercher des années ou des durées
                if (preg_match('/\b(19|20)\d{2}\b/', $line) || preg_match('/\d+\s*(an|année|mois|year|month)/i', $line)) {
                    $experience[] = $line;
                }
            }
        }
        
        return $experience;
    }

    private function extractSkills($text)
    {
        $skills = [];
        $commonSkills = [
            'PHP', 'JavaScript', 'Python', 'Java', 'C#', 'SQL', 'HTML', 'CSS',
            'React', 'Angular', 'Vue', 'Node.js', 'Laravel', 'Symfony',
            'Git', 'Docker', 'AWS', 'Azure', 'Linux', 'Windows',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis',
            'Gestion de projet', 'Leadership', 'Communication', 'Travail en équipe'
        ];

        foreach ($commonSkills as $skill) {
            if (stripos($text, $skill) !== false) {
                $skills[] = $skill;
            }
        }

        return $skills;
    }

    private function extractLanguages($text)
    {
        $languages = [];
        $commonLanguages = ['Français', 'Anglais', 'Malgache', 'Espagnol', 'Allemand', 'Italien'];

        foreach ($commonLanguages as $lang) {
            if (stripos($text, $lang) !== false) {
                $languages[] = $lang;
            }
        }

        return $languages;
    }

    /**
     * Obtenir les statistiques du chatbot
     */
    public function getChatbotStatistics()
    {
        // TODO: Implémenter le tracking des interactions
        return [
            'total_messages' => 0,
            'avg_response_time' => 0,
            'satisfaction_rate' => 0,
            'most_asked_topics' => []
        ];
    }

    // ============= Méthodes utilitaires =============

    private function getEmployeData($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM employe WHERE id_employe = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function getAllEmployesData()
    {
        $stmt = $this->db->query("SELECT * FROM employe");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getCandidatesData()
    {
        // Pour l'instant, retourner les employés existants
        // TODO: Créer une table candidats séparée
        return $this->getAllEmployesData();
    }

    private function calculerAnciennete($dateEmbauche)
    {
        $date1 = new \DateTime($dateEmbauche);
        $date2 = new \DateTime();
        $interval = $date1->diff($date2);
        return $interval->y + ($interval->m / 12);
    }

    private function getAbsencesCount($employeId, $months)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM absence 
            WHERE id_employe = ? 
            AND date_absence >= DATE_SUB(NOW(), INTERVAL ? MONTH)
        ");
        $stmt->execute([$employeId, $months]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    private function getCongesNonPris($employeId)
    {
        // Calculer le solde théorique (30 jours par an) - congés pris
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(DATEDIFF(c.date_fin, c.date_debut) + 1), 0) as jours_pris
            FROM conge c
            WHERE c.id_employe = ?
            AND c.status IN (11, 21)
            AND YEAR(c.date_debut) = YEAR(NOW())
        ");
        $stmt->execute([$employeId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $joursPris = $result['jours_pris'] ?? 0;
        
        // Solde théorique: 30 jours - jours pris
        return max(0, 30 - $joursPris);
    }

    private function getHeuresSupCount($employeId, $months)
    {
        // Calculer les heures travaillées au-delà de 40h/semaine (8h/jour * 5 jours)
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(
                GREATEST(0, TIMESTAMPDIFF(HOUR, p.date_heure_arrive, p.date_heure_depart) - 8)
            ), 0) as total 
            FROM pointage p
            WHERE p.id_employe = ? 
            AND p.date_heure_arrive >= DATE_SUB(NOW(), INTERVAL ? MONTH)
        ");
        $stmt->execute([$employeId, $months]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getDernierePromotion($employeId)
    {
        // Vérifier s'il y a eu un changement de poste (promotion) dans les 2 dernières années
        $stmt = $this->db->prepare("
            SELECT c.*, p.nom as nom_poste
            FROM contrat c
            JOIN poste p ON c.id_poste = p.id_poste
            WHERE c.id_employe = ? 
            AND c.date_debut >= DATE_SUB(NOW(), INTERVAL 2 YEAR)
            ORDER BY c.date_debut DESC 
            LIMIT 1
        ");
        $stmt->execute([$employeId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function generateTurnoverRecommendation($score, $factors)
    {
        if ($score >= 60) {
            return "Action urgente requise: Organiser un entretien individuel, revoir les conditions de travail, proposer une formation ou évolution.";
        } elseif ($score >= 40) {
            return "Surveillance recommandée: Prévoir un point régulier, identifier les besoins, ajuster la charge de travail.";
        } else {
            return "Situation stable: Continuer le suivi régulier.";
        }
    }

    private function getPeriodCondition($period, $field = 'p.date_heure_arrive')
    {
        switch ($period) {
            case 'week':
                return "$field >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            case 'year':
                return "$field >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            case 'month':
            default:
                return "$field >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        }
    }

    private function getMatchLevel($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Bon';
        if ($score >= 40) return 'Moyen';
        return 'Faible';
    }

    /**
     * Récupérer les informations complètes d'un employé pour générer des documents
     */
    private function getEmployeFullInfo($employeId)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, 
                   p.nom as poste,
                   d.nom_departement as departement,
                   c.salaire as salaire,
                   c.date_debut as contrat_debut
            FROM employe e
            LEFT JOIN contrat c ON e.id_employe = c.id_employe AND c.date_fin IS NULL
            LEFT JOIN poste p ON c.id_poste = p.id_poste
            LEFT JOIN departement d ON c.id_departement = d.id_departement
            WHERE e.id_employe = ?
            ORDER BY c.date_debut DESC
            LIMIT 1
        ");
        $stmt->execute([$employeId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
