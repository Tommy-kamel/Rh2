<?php
$dsn = 'mysql:host=localhost;dbname=rh2;charset=utf8';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    $pdo->exec('DROP VIEW IF EXISTS vue_nombre_conge');
    $pdo->exec('CREATE VIEW vue_nombre_conge AS SELECT id_employe, SUM(DATEDIFF(date_fin, date_debut) + 1) as duree_totale_conges_jours FROM conge WHERE status = 21 GROUP BY id_employe');
    echo "View created.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>