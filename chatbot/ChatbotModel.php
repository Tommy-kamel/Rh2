<?php

namespace chatbot;

require_once __DIR__ . '/DocumentGenerator.php';

class ChatbotModel
{
    private $db;
    private $documentGenerator;

    public function __construct($db)
    {
        $this->db = $db;
        $this->documentGenerator = new DocumentGenerator($db);
    }

    // Récupérer une réponse basée sur un mot-clé avec système de scoring
    public function getResponse($keyword, $userId = null)
    {
        // Normaliser les accents pour une meilleure correspondance
        $normalizedKeyword = $this->normalizeString($keyword);
        
        // Découper la question en mots
        $mots = explode(' ', $normalizedKeyword);
        
        // Récupérer toutes les réponses
        $stmt = $this->db->query("SELECT keyword, response, is_dynamic FROM chatbot_responses");
        $responses = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Calculer un score pour chaque réponse
        $scores = [];
        foreach ($responses as $row) {
            $score = $this->calculerScore($normalizedKeyword, $mots, $row);
            if ($score > 0) {
                $scores[] = [
                    'score' => $score,
                    'response' => $row['response'],
                    'is_dynamic' => $row['is_dynamic'],
                    'keyword' => $row['keyword']
                ];
            }
        }
        
        // Trier par score décroissant
        usort($scores, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Si on a trouvé une correspondance avec un bon score
        if (!empty($scores) && $scores[0]['score'] >= 2) {
            if ($scores[0]['is_dynamic']) {
                return $this->generateDynamicResponse($normalizedKeyword, $userId);
            }
            return $scores[0]['response'];
        }
        
        // Sinon, chercher des indices dans la question
        return $this->chercherIndices($normalizedKeyword, $mots, $userId);
    }
    
    // Calculer un score de pertinence entre la question et un mot-clé
    private function calculerScore($question, $mots, $reponseDB)
    {
        $dbKeyword = $this->normalizeString($reponseDB['keyword']);
        $score = 0;
        
        // Score fort si correspondance exacte
        if ($question === $dbKeyword) {
            return 10;
        }
        
        // Score moyen si l'un contient l'autre
        if (strpos($question, $dbKeyword) !== false) {
            $score += 5;
        }
        if (strpos($dbKeyword, $question) !== false) {
            $score += 5;
        }
        
        // Score par mot trouvé dans le mot-clé
        foreach ($mots as $mot) {
            if (strlen($mot) > 2 && strpos($dbKeyword, $mot) !== false) {
                $score += 2;
            }
        }
        
        // Bonus pour les synonymes communs
        $synonymes = $this->getSynonymes();
        foreach ($synonymes as $groupe) {
            $motsGroupe = array_intersect($mots, $groupe);
            if (!empty($motsGroupe) && in_array($dbKeyword, $groupe)) {
                $score += 3;
            }
        }
        
        return $score;
    }
    
    // Retourner des groupes de synonymes
    private function getSynonymes()
    {
        return [
            ['conge', 'vacances', 'repos', 'absence', 'permission'],
            ['salaire', 'paie', 'remuneration', 'paye', 'traitement', 'argent'],
            ['horaire', 'temps', 'pointage', 'heure', 'planning'],
            ['profil', 'info', 'information', 'donnees', 'compte'],
            ['contrat', 'engagement', 'embauche', 'emploi'],
            ['departement', 'service', 'equipe', 'division'],
            ['demander', 'faire', 'soumettre', 'deposer', 'envoyer'],
        ];
    }
    
    // Chercher des indices dans la question pour une réponse intelligente
    private function chercherIndices($question, $mots, $userId)
    {
        // Catégories de mots-clés avec leurs réponses
        $categories = [
            'conges' => ['conge', 'vacances', 'repos', 'absence', 'permission', 'jour', 'off'],
            'salaire' => ['salaire', 'paie', 'remuneration', 'paye', 'argent', 'bulletin'],
            'horaires' => ['horaire', 'temps', 'pointage', 'heure', 'planning', 'pointer'],
            'profil' => ['profil', 'info', 'information', 'donnees', 'compte', 'coordonnees'],
            'demande' => ['demander', 'comment', 'faire', 'procedure', 'soumettre', 'deposer'],
            'documents' => ['document', 'attestation', 'contrat', 'certificat', 'fiche', 'avenant', 'generer', 'creer', 'telecharger']
        ];
        
        // Compter les indices trouvés par catégorie
        $indices = [];
        foreach ($categories as $cat => $motsClés) {
            $count = 0;
            foreach ($mots as $mot) {
                if (in_array($mot, $motsClés)) {
                    $count++;
                }
            }
            if ($count > 0) {
                $indices[$cat] = $count;
            }
        }
        
        // Si on a trouvé des indices, donner une réponse contextuelle
        if (!empty($indices)) {
            arsort($indices);
            $meilleureCat = array_key_first($indices);
            
            // Générer une réponse selon la catégorie
            if ($meilleureCat === 'conges') {
                if ($userId) {
                    return $this->generateDynamicResponse('conge', $userId);
                }
                return "Pour tout savoir sur les congés :\n- Tapez 'mes congés' pour voir votre solde\n- Tapez 'demander un congé' pour la procédure\n- Tapez 'types de congés' pour les différents types";
            }
            
            if ($meilleureCat === 'salaire') {
                if ($userId) {
                    return $this->generateDynamicResponse('salaire', $userId);
                }
                return "Pour les informations sur le salaire :\n- Tapez 'mon salaire' pour voir votre rémunération\n- Tapez 'bulletin de paie' pour la procédure\n- Tapez 'paie' pour plus d'infos";
            }
            
            if ($meilleureCat === 'horaires') {
                return $this->readGuide('pointage_guide.txt');
            }
            
            if ($meilleureCat === 'profil') {
                if ($userId) {
                    return $this->generateDynamicResponse('profil', $userId);
                }
                return "Pour voir votre profil, tapez 'mon profil' ou 'mes informations'";
            }
            
            if ($meilleureCat === 'demande') {
                return "Que souhaitez-vous faire ?\n- Demander un congé\n- Consulter mes informations\n- Voir mon bulletin de paie\n- Pointer mes heures\n\nPrécisez votre demande pour que je puisse mieux vous aider !";
            }
            
            if ($meilleureCat === 'documents') {
                if ($userId) {
                    return $this->gererDocuments($question, $userId);
                }
                return "Je peux vous aider à générer des documents !\n\nTapez :\n- 'attestation' pour une attestation de travail\n- 'contrat' pour votre contrat\n- 'certificat' pour un certificat de travail\n- 'fiche de paie' pour votre bulletin\n- 'avenant' pour un avenant au contrat\n- 'mes documents' pour voir vos documents";
            }
        }
        
        // Aucun indice trouvé - réponse par défaut avec suggestions
        return $this->reponseParDefaut($question);
    }
    
    // Réponse par défaut avec suggestions intelligentes
    private function reponseParDefaut($question)
    {
        $suggestions = "Je n'ai pas bien compris votre question. Voici ce que je peux faire pour vous :\n\n";
        $suggestions .= "📅 **Congés** : 'mes congés', 'demander un congé', 'solde congés'\n";
        $suggestions .= "💰 **Salaire** : 'mon salaire', 'bulletin de paie'\n";
        $suggestions .= "⏰ **Horaires** : 'horaires', 'pointage', 'mes heures'\n";
        $suggestions .= "👤 **Profil** : 'mon profil', 'mes informations'\n";
        $suggestions .= "📄 **Documents** : 'attestation', 'contrat', 'certificat', 'fiche de paie', 'mes documents'\n";
        $suggestions .= "❓ **Aide** : 'aide', 'que peux-tu faire'\n\n";
        $suggestions .= "💡 Astuce : Posez votre question simplement, par exemple :\n";
        $suggestions .= "- 'Combien de jours de congé me restent ?'\n";
        $suggestions .= "- 'Comment pointer mes heures ?'\n";
        $suggestions .= "- 'Quel est mon salaire ?'\n";
        $suggestions .= "- 'Génère mon attestation de travail'";
        
        return $suggestions;
    }

    // Fonction pour normaliser les chaînes (supprimer accents)
    private function normalizeString($string)
    {
        $string = strtolower($string);
        $accents = ['à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'];
        $sans_accents = ['a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y'];
        return str_replace($accents, $sans_accents, $string);
    }

    // Générer des réponses dynamiques (exemple pour congés)
    private function generateDynamicResponse($keyword, $userId = null)
    {
        $info = $this->getEmployeeInfo($userId);
        if (!$info) {
            return "Désolé, je n'arrive pas à accéder à vos informations personnelles. Votre ID utilisateur est $userId. Assurez-vous que vous êtes connecté et que la base de données est à jour. Contactez l'administrateur si le problème persiste.";
        }

        // Questions sur les congés
        if (strpos($keyword, 'conge') !== false || strpos($keyword, 'vacance') !== false || 
            strpos($keyword, 'repos') !== false || strpos($keyword, 'absence') !== false) {
            
            // Questions spécifiques sur le nombre de jours
            if (strpos($keyword, 'combien') !== false || strpos($keyword, 'reste') !== false || 
                strpos($keyword, 'solde') !== false || strpos($keyword, 'nombre') !== false) {
                return "Bonjour {$info['prenom']} ! 📊\n\n" .
                       "Voici votre solde de congés :\n" .
                       "✅ Congés approuvés : {$info['conges_approuves']} jours\n" .
                       "⏳ En attente : {$info['conges_attente']}\n" .
                       "📅 Jours restants : {$info['conges_restants']} jours\n\n" .
                       "Besoin d'aide ? Tapez 'demander un congé' pour la procédure.";
            }
            
            // Historique détaillé
            if (strpos($keyword, 'historique') !== false || strpos($keyword, 'liste') !== false || 
                strpos($keyword, 'demande') !== false) {
                $response = "Bonjour {$info['prenom']} ! 📋\n\n";
                $response .= "Voici l'historique de vos demandes de congés :\n\n";
                
                if (empty($info['historique_conges'])) {
                    $response .= "Aucune demande trouvée.\n";
                } else {
                    foreach (array_slice($info['historique_conges'], 0, 5) as $conge) {
                        $status = $conge['status'] == 21 ? '✅ Approuvé' : 
                                 ($conge['status'] == 11 ? '❌ Rejeté' : '⏳ En attente');
                        $response .= "📅 {$conge['type']} : {$conge['date_debut']} → {$conge['date_fin']}\n";
                        $response .= "   Status : $status\n\n";
                    }
                }
                $response .= "Pour demander un nouveau congé, tapez 'comment demander un congé'.";
                return $response;
            }
            
            // Réponse générale sur les congés
            $salutations = ['Salut', 'Bonjour', 'Hello'];
            $salut = $salutations[array_rand($salutations)];
            $response = "$salut {$info['prenom']} ! 🏖️\n\n";
            $response .= "**Aperçu de vos congés :**\n";
            $response .= "✅ Approuvés : {$info['conges_approuves']} jours\n";
            $response .= "⏳ En attente : {$info['conges_attente']}\n";
            $response .= "📅 Restants : {$info['conges_restants']} jours\n\n";
            $response .= "💡 Que puis-je faire pour vous ?\n";
            $response .= "- Tapez 'historique congés' pour voir toutes vos demandes\n";
            $response .= "- Tapez 'demander un congé' pour la procédure\n";
            $response .= "- Tapez 'types de congés' pour plus d'infos";
            return $response;
        }

        // Questions sur le salaire
        if (strpos($keyword, 'salaire') !== false || strpos($keyword, 'paie') !== false || 
            strpos($keyword, 'remuneration') !== false || strpos($keyword, 'argent') !== false) {
            
            $response = "Bonjour {$info['prenom']} ! 💰\n\n";
            $response .= "**Informations sur votre rémunération :**\n";
            $response .= "💵 Salaire : {$info['salaire']} AR\n";
            $response .= "👔 Poste : {$info['poste']}\n";
            $response .= "📅 Contrat depuis : {$info['contrat_debut']}\n\n";
            $response .= "Pour plus d'infos sur votre bulletin de paie, tapez 'bulletin de paie'.";
            return $response;
        }

        // Questions sur le profil
        if (strpos($keyword, 'profil') !== false || strpos($keyword, 'info') !== false || 
            strpos($keyword, 'coordonnee') !== false || strpos($keyword, 'donnee') !== false) {
            
            $response = "Bonjour {$info['prenom']} ! 👤\n\n";
            $response .= "**Vos informations personnelles :**\n\n";
            $response .= "📝 Nom complet : {$info['nom']} {$info['prenom']}\n";
            $response .= "📧 Email : {$info['email']}\n";
            $response .= "📱 Téléphone : {$info['telephone']}\n";
            $response .= "🏠 Adresse : {$info['adresse']}\n\n";
            $response .= "**Informations professionnelles :**\n\n";
            $response .= "👔 Poste : {$info['poste']}\n";
            $response .= "🏢 Département : {$info['departement']}\n";
            $response .= "💰 Salaire : {$info['salaire']} AR\n";
            $response .= "📅 Contrat depuis : {$info['contrat_debut']}";
            return $response;
        }

        // Questions sur les procédures
        if (strpos($keyword, 'demander') !== false || strpos($keyword, 'comment') !== false || 
            strpos($keyword, 'faire') !== false || strpos($keyword, 'procedure') !== false) {
            
            // Demander un congé
            if (strpos($keyword, 'conge') !== false) {
                return $this->readGuide('conges_guide.txt');
            }
            
            // Pointage
            if (strpos($keyword, 'pointer') !== false || strpos($keyword, 'horaire') !== false) {
                return $this->readGuide('pointage_guide.txt');
            }
            
            // Bulletin
            if (strpos($keyword, 'bulletin') !== false || strpos($keyword, 'paie') !== false) {
                return $this->readGuide('salaire_guide.txt');
            }
            
            // Réponse générale
            return "Je peux vous aider avec plusieurs procédures :\n\n" .
                   "📋 **Demander un congé** : tapez 'demander un congé'\n" .
                   "⏰ **Pointer vos heures** : tapez 'comment pointer'\n" .
                   "💰 **Consulter votre bulletin** : tapez 'bulletin de paie'\n\n" .
                   "Précisez ce que vous souhaitez faire !";
        }

        // Horaires et pointage
        if (strpos($keyword, 'pointer') !== false || strpos($keyword, 'horaire') !== false || 
            strpos($keyword, 'heure') !== false || strpos($keyword, 'temps') !== false) {
            return $this->readGuide('pointage_guide.txt');
        }

        // Bulletin de paie
        if (strpos($keyword, 'bulletin') !== false) {
            return $this->readGuide('salaire_guide.txt');
        }

        return "Réponse dynamique non disponible pour ce mot-clé.";
    }

    // Lire un guide depuis docs_rh
    private function readGuide($filename)
    {
        $path = __DIR__ . '/docs_rh/' . $filename;
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return "Guide non trouvé.";
    }

    // Récupérer toutes les informations de l'employé
    public function getEmployeeInfo($userId)
    {
        // Infos de base
        $stmt = $this->db->prepare("
            SELECT e.nom, e.prenom, e.email, e.sexe, e.telephone, e.adresse, 
                   p.nom as poste, d.nom_departement, c.salaire, c.date_debut as contrat_debut
            FROM employe e
            LEFT JOIN contrat c ON e.id_employe = c.id_employe AND c.date_fin IS NULL
            LEFT JOIN poste p ON c.id_poste = p.id_poste
            LEFT JOIN departement d ON c.id_departement = d.id_departement
            WHERE e.id_employe = ?
        ");
        $stmt->execute([$userId]);
        $info = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$info) return null;

        // Congés approuvés
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM conge WHERE id_employe = ? AND status = 21");
        $stmt->execute([$userId]);
        $approved = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Congés en attente
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM conge WHERE id_employe = ? AND status = 1");
        $stmt->execute([$userId]);
        $pending = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Congés restants (supposons 30 jours max par an)
        $remaining = 30 - $approved;

        // Toutes les demandes de congés
        $stmt = $this->db->prepare("
            SELECT tc.type, c.date_demande, c.date_debut, c.date_fin, c.raison, c.status, c.date_validation
            FROM conge c
            LEFT JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge
            WHERE c.id_employe = ?
            ORDER BY c.date_demande DESC
        ");
        $stmt->execute([$userId]);
        $conges = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'nom' => $info['nom'],
            'prenom' => $info['prenom'],
            'email' => $info['email'],
            'sexe' => $info['sexe'],
            'telephone' => $info['telephone'],
            'adresse' => $info['adresse'],
            'poste' => $info['poste'],
            'departement' => $info['nom_departement'],
            'salaire' => $info['salaire'],
            'contrat_debut' => $info['contrat_debut'],
            'conges_approuves' => $approved,
            'conges_attente' => $pending,
            'conges_restants' => $remaining,
            'historique_conges' => $conges
        ];
    }
    
    // Gérer les demandes de génération de documents
    private function gererDocuments($question, $userId)
    {
        $questionNorm = $this->normalizeString($question);
        
        // Lister tous les documents générés
        if (strpos($questionNorm, 'mes documents') !== false || 
            strpos($questionNorm, 'liste') !== false || 
            strpos($questionNorm, 'tous mes documents') !== false) {
            
            $documents = $this->documentGenerator->listerDocuments($userId);
            
            if (empty($documents)) {
                return "📄 Vous n'avez aucun document généré pour le moment.\n\n" .
                       "Je peux créer pour vous :\n" .
                       "- 📋 Attestation de travail\n" .
                       "- 📝 Contrat de travail\n" .
                       "- 🎓 Certificat de travail\n" .
                       "- 💰 Fiche de paie\n" .
                       "- 📄 Avenant au contrat\n\n" .
                       "Tapez le type de document souhaité !";
            }
            
            $response = "📁 **Vos documents générés** :\n\n";
            foreach ($documents as $doc) {
                $icon = $this->getDocumentIcon($doc['type']);
                $response .= "$icon {$doc['nom']}\n";
                $response .= "   📅 Créé le : {$doc['date']}\n";
                $response .= "   📍 Chemin : {$doc['chemin']}\n\n";
            }
            $response .= "💡 Pour générer un nouveau document, tapez son type (attestation, contrat, etc.)";
            return $response;
        }
        
        // Détecter le type de document demandé
        $typeDocument = null;
        $nomDocument = '';
        
        if (strpos($questionNorm, 'attestation') !== false) {
            $typeDocument = 'attestation';
            $nomDocument = 'Attestation de travail';
        } elseif (strpos($questionNorm, 'contrat') !== false) {
            $typeDocument = 'contrat';
            $nomDocument = 'Contrat de travail';
        } elseif (strpos($questionNorm, 'certificat') !== false) {
            $typeDocument = 'certificat';
            $nomDocument = 'Certificat de travail';
        } elseif (strpos($questionNorm, 'fiche') !== false || 
                  strpos($questionNorm, 'paie') !== false || 
                  strpos($questionNorm, 'salaire') !== false ||
                  strpos($questionNorm, 'bulletin') !== false) {
            $typeDocument = 'fiche_paie';
            $nomDocument = 'Fiche de paie';
        } elseif (strpos($questionNorm, 'avenant') !== false || 
                  strpos($questionNorm, 'modification') !== false) {
            $typeDocument = 'avenant';
            $nomDocument = 'Avenant au contrat';
        }
        
        // Si aucun type détecté, proposer les options
        if (!$typeDocument) {
            return "📄 **Génération de documents**\n\n" .
                   "Je peux créer les documents suivants pour vous :\n\n" .
                   "📋 **Attestation de travail**\n" .
                   "   → Tapez 'attestation' ou 'generer attestation'\n\n" .
                   "📝 **Contrat de travail**\n" .
                   "   → Tapez 'contrat' ou 'mon contrat'\n\n" .
                   "🎓 **Certificat de travail**\n" .
                   "   → Tapez 'certificat' ou 'generer certificat'\n\n" .
                   "💰 **Fiche de paie**\n" .
                   "   → Tapez 'fiche de paie' ou 'bulletin de salaire'\n\n" .
                   "📄 **Avenant au contrat**\n" .
                   "   → Tapez 'avenant' ou 'modification contrat'\n\n" .
                   "💡 Pour voir vos documents existants, tapez 'mes documents'";
        }
        
        // Générer le document
        try {
            $resultat = $this->documentGenerator->genererDocument($typeDocument, $userId);
            
            if ($resultat['success']) {
                $icon = $this->getDocumentIcon($typeDocument);
                return "✅ **Document généré avec succès !**\n\n" .
                       "$icon **$nomDocument**\n" .
                       "📁 Fichier : {$resultat['filename']}\n" .
                       "📍 Emplacement : {$resultat['filepath']}\n\n" .
                       "💡 Le document a été créé avec vos informations personnelles.\n" .
                       "Tapez 'mes documents' pour voir tous vos documents.";
            } else {
                return "❌ **Erreur lors de la génération du document**\n\n" .
                       "Message : {$resultat['message']}\n\n" .
                       "💡 Veuillez réessayer ou contactez l'administrateur si le problème persiste.";
            }
        } catch (\Exception $e) {
            return "❌ **Une erreur est survenue**\n\n" .
                   "Détails : " . $e->getMessage() . "\n\n" .
                   "💡 Veuillez contacter l'administrateur.";
        }
    }
    
    // Obtenir l'icône appropriée selon le type de document
    private function getDocumentIcon($type)
    {
        $icons = [
            'attestation' => '📋',
            'contrat' => '📝',
            'certificat' => '🎓',
            'fiche_paie' => '💰',
            'avenant' => '📄'
        ];
        return $icons[$type] ?? '📄';
    }
}