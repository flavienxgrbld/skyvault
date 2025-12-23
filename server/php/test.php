<?php
// Test de connexion MySQL avec PHP
require __DIR__ . '/db.php';

echo "Test de connexion MySQL...\n\n";

try {
    // Test simple
    $stmt = $pdo->query('SELECT VERSION() as version');
    $result = $stmt->fetch();
    echo "✓ Connexion réussie !\n";
    echo "Version MySQL : " . $result['version'] . "\n\n";
    
    // Test des données
    $stmt = $pdo->query('SELECT * FROM users LIMIT 1');
    $user = $stmt->fetch();
    echo "Premier utilisateur : " . $user['name'] . " (" . $user['email'] . ")\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
}
