<?php
$dsn = 'mysql:host=localhost;dbname=rh2;charset=utf8';
$username = 'root';
$password = 'root123';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    $pdo->exec("UPDATE chatbot_responses SET keyword = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(keyword, 'é', 'e'), 'è', 'e'), 'ê', 'e'), 'à', 'a'), 'ç', 'c')");
    echo "Keywords normalized.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>