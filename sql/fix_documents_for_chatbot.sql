-- Supprimer les entrées en double pour les documents
DELETE FROM chatbot_responses 
WHERE keyword IN ('attestation', 'contrat', 'certificat', 'fiche de paie', 'bulletin', 'avenant', 'mes documents', 'document', 'generer document')
AND response = 'DYNAMIC_RESPONSE';

-- Mettre à jour l'entrée 'attestation' existante pour ne pas être dynamique
UPDATE chatbot_responses 
SET is_dynamic = 0,
    response = 'Je peux générer une attestation de travail pour vous ! Tapez simplement "attestation" pour la créer.'
WHERE keyword = 'attestation' 
AND response != 'DYNAMIC_RESPONSE';

-- Optionnel : ajouter des mots-clés d'aide pour les documents
INSERT INTO chatbot_responses (keyword, response, is_dynamic) VALUES
('documents', 'Je peux générer les documents suivants : attestation, contrat, certificat, fiche de paie, avenant. Tapez le nom du document que vous souhaitez.', 0)
ON DUPLICATE KEY UPDATE response = VALUES(response);
