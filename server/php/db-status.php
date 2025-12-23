<?php
// Retourne le statut de connexion à la base de données
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$status = [
    'connected' => false,
    'message' => '',
    'details' => []
];

// Vérifier si PDO MySQL est disponible
if (!extension_loaded('pdo_mysql')) {
    $status['message'] = 'Extension PDO MySQL non activée';
    echo json_encode($status);
    exit;
}

try {
    require __DIR__ . '/db.php';
    
    // Test de connexion
    $stmt = $pdo->query('SELECT VERSION() as version, DATABASE() as dbname');
    $info = $stmt->fetch();
    
    $status['connected'] = true;
    $status['message'] = 'Connexion réussie';
    $status['details'] = [
        'version' => $info['version'],
        'database' => $info['dbname'],
        'host' => '127.0.0.1'
    ];
    
    // Compter les tables
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $status['details']['tables'] = count($tables);
    
} catch (Exception $e) {
    $status['connected'] = false;
    $status['message'] = $e->getMessage();
}

echo json_encode($status);
