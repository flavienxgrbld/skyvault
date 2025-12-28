<?php
// Connexion PDO vers MySQL (configuration Laragon par défaut)
$host = '192.168.1.13';
$db   = 'skyvault';
$user = 'admin';
$pass = 'MotDePasseSolide';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo 'Database connection error';
    exit;
}

// Exemple d'utilisation :
// require __DIR__ . '/db.php';
// $stmt = $pdo->query('SELECT * FROM users LIMIT 1');
// $row = $stmt->fetch();
