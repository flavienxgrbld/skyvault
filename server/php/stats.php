<?php
// Retourne les statistiques réelles de la base de données
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$stats = [
    'modules' => 0,
    'orders' => 0,
    'users' => 0,
    'admins' => 0,
    'revenue' => 0
];

try {
    require __DIR__ . '/db.php';
    
    // Compter les utilisateurs
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
    $stats['users'] = (int) $stmt->fetch()['count'];
    
    // Compter les administrateurs
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM admins');
    $stats['admins'] = (int) $stmt->fetch()['count'];
    
    // Compter les modules
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM modules WHERE active = 1');
    $stats['modules'] = (int) $stmt->fetch()['count'];
    
    // Calcul du revenu (somme des prix des modules actifs)
    $stmt = $pdo->query('SELECT SUM(price) as total FROM modules WHERE active = 1');
    $stats['revenue'] = (float) ($stmt->fetch()['total'] ?? 0);
    
    // Commandes (0 pour l'instant, table à créer)
    $stats['orders'] = 0;
    
} catch (Exception $e) {
    // Garder les valeurs par défaut en cas d'erreur
}

echo json_encode($stats);
