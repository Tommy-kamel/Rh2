<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Assurez-vous que Composer est chargé

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GeminiService {
    private $apiKey;
    private $client;
    private $model = 'gemini-2.5-flash'; // Modèle de base disponible

    public function __construct($apiKey = null) {
        // Charge la clé depuis env ou config (plus sécurisé)
        $this->apiKey = $apiKey ?? $_ENV['GEMINI_API_KEY'] ?? $this->loadFromConfig();
        if (empty($this->apiKey)) {
            throw new Exception('Clé API Gemini manquante. Ajoute-la via env ou config.php.');
        }
        $this->client = new Client();
    }

    private function loadFromConfig() {
        // Exemple : charge depuis un fichier config.php (crée-le avec return 'ta_cle_ici';)
        $configFile = __DIR__ . '/config.php';
        return file_exists($configFile) ? include $configFile : '';
    }

    public function suggererActionsPourConge($congeDetails, $tousCongesAttente = [], $congesValidesFuturs = []) {
        // Préparer les données
        $prompt = "Tu es un assistant RH expert en planification des congés.\n\n";
        $prompt .= "Analyse TOUS les congés (en attente ET validés) et détecte les CHEVAUCHEMENTS et CONFLITS.\n";
        $prompt .= "Fournis un RÉCAPITULATIF CONCIS des actions recommandées.\n\n";
        
        // Ajouter les congés validés futurs pour contexte
        if (!empty($congesValidesFuturs)) {
            $prompt .= "CONGÉS DÉJÀ VALIDÉS (à prendre en compte pour détecter les chevauchements) :\n";
            foreach ($congesValidesFuturs as $cv) {
                $prompt .= "- {$cv['nom']} {$cv['prenom']} | {$cv['nom_departement']} | {$cv['nom_poste']} | {$cv['date_debut']} au {$cv['date_fin']}\n";
            }
            $prompt .= "\n";
        }
        
        $prompt .= "CONGÉS EN ATTENTE À ANALYSER :\n";
        
        // Ajouter d'abord le congé principal
        $prompt .= "\n[Congé principal à analyser]\n";
        $prompt .= "- Employé: {$congeDetails['nom']} {$congeDetails['prenom']}\n";
        $prompt .= "- Poste: {$congeDetails['nom_poste']}\n";
        $prompt .= "- Département: {$congeDetails['nom_departement']}\n";
        $prompt .= "- Type: {$congeDetails['type']}\n";
        $prompt .= "- Période: {$congeDetails['date_debut']} au {$congeDetails['date_fin']} ({$congeDetails['nb_jour']} jours)\n";
        $prompt .= "- Raison: {$congeDetails['raison']}\n";
        $prompt .= "- Demandé le: {$congeDetails['date_demande']}\n";

        if (!empty($tousCongesAttente)) {
            $prompt .= "\n[Autres congés en attente]\n";
            foreach ($tousCongesAttente as $c) {
                if ($c['id_conge'] != $congeDetails['id_conge']) {
                    $prompt .= "- {$c['nom']} {$c['prenom']} | {$c['nom_departement']} | {$c['nom_poste']} | {$c['date_debut']} au {$c['date_fin']} ({$c['nb_jour']} jours)\n";
                }
            }
        }

        $prompt .= "\n\nIMPORTANT : Vérifie les CHEVAUCHEMENTS de dates entre les congés en attente et les congés validés du MÊME DÉPARTEMENT.\n";
        $prompt .= "Un chevauchement existe si deux employés du même département ont des congés sur la même période.\n\n";
        
        $prompt .= "FORMAT DE RÉPONSE OBLIGATOIRE :\n";
        $prompt .= "Fournis UNIQUEMENT un récapitulatif structuré par ordre chronologique avec :\n";
        $prompt .= "1. Nom de l'employé (Département)\n";
        $prompt .= "2. Période du congé\n";
        $prompt .= "3. Action recommandée : VALIDER, REFUSER ou PROPOSER NOUVELLE DATE\n";
        $prompt .= "4. Justification en 1 phrase (mentionne EXPLICITEMENT les chevauchements détectés)\n";
        $prompt .= "\nSépare chaque congé par une ligne vide. Sois CONCIS et DIRECT. Maximum 3-4 lignes par congé.\n";
        $prompt .= "Réponds UNIQUEMENT en français.";

        // URL corrigée avec le bon modèle
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . urlencode($this->apiKey);

        // Appel à l'API Gemini
        try {
            $response = $this->client->post($url, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 60,
                'connect_timeout' => 15, // Timeout pour éviter les blocages
            ]);

            $data = json_decode($response->getBody(), true);
            
            // Vérification de la structure de réponse (conforme à la doc)
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return trim($data['candidates'][0]['content']['parts'][0]['text']);
            } else {
                return 'Aucune suggestion disponible (réponse inattendue).';
            }
        } catch (RequestException $e) {
            // Meilleure gestion pour les erreurs HTTP (ex. 429 pour quotas)
            $errorMsg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            error_log("Erreur API Gemini : " . $errorMsg); // Log pour debug
            return 'Erreur lors de la génération : ' . $errorMsg;
        } catch (Exception $e) {
            return 'Erreur inattendue : ' . $e->getMessage();
        }
    }
}