<?php
/**
 * Script de réimportation complète de la base de données
 */

$host = 'localhost';
$dbname = 'rh_database';
$user = 'root';
$pass = '';

try {
    // Connexion sans base de données spécifique pour pouvoir la recréer
    $db = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "🔄 Réinitialisation de la base de données...\n\n";
    
    // Supprimer et recréer la base
    $db->exec("DROP DATABASE IF EXISTS $dbname");
    echo "✅ Ancienne base supprimée\n";
    
    $db->exec("CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Nouvelle base créée\n";
    
    $db->exec("USE $dbname");
    
    // Importer base.sql
    echo "\n📥 Import de la structure (base.sql)...\n";
    $baseSql = file_get_contents(__DIR__ . '/sql/base.sql');
    $db->exec($baseSql);
    echo "✅ Structure créée\n";
    
    // Importer donnees.sql
    echo "\n📥 Import des données (donnees.sql)...\n";
    $donneesSql = file_get_contents(__DIR__ . '/sql/donnees.sql');
    
    // Exécuter chaque requête séparément
    $statements = explode(';', $donneesSql);
    $count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !str_starts_with($statement, '--')) {
            try {
                $db->exec($statement);
                $count++;
            } catch (PDOException $e) {
                // Ignorer les erreurs de table déjà existante
                if (!str_contains($e->getMessage(), 'already exists')) {
                    echo "⚠️  Avertissement : " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "✅ $count requêtes exécutées\n";
    
    // Vérifier que les documents ne sont pas en double
    echo "\n🔍 Vérification des mots-clés documents...\n";
    $stmt = $db->query("SELECT keyword, COUNT(*) as count FROM chatbot_responses WHERE keyword IN ('attestation', 'contrat', 'certificat', 'avenant', 'document') GROUP BY keyword HAVING count > 1");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($duplicates)) {
        echo "❌ Doublons trouvés :\n";
        foreach ($duplicates as $dup) {
            echo "  - {$dup['keyword']} : {$dup['count']} occurrences\n";
        }
        echo "\n🔧 Nettoyage des doublons...\n";
        $db->exec("DELETE FROM chatbot_responses WHERE keyword IN ('attestation', 'contrat', 'certificat', 'avenant', 'document', 'fiche de paie', 'mes documents', 'generer document')");
        echo "✅ Doublons supprimés\n";
    } else {
        echo "✅ Aucun doublon\n";
    }
    
    echo "\n✅ Base de données prête !\n";
    echo "\n🎉 Vous pouvez maintenant tester la génération de documents :\n";
    echo "   - Connectez-vous au chatbot\n";
    echo "   - Tapez : 'attestation', 'contrat', 'avenant', etc.\n\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
