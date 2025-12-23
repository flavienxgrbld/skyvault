<?php
// Test de création d'utilisateur
header('Content-Type: text/plain');

require __DIR__ . '/db.php';

echo "Test d'insertion d'utilisateur...\n\n";

try {
    // Test d'insertion
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
    $result = $stmt->execute(['Test User', 'test@example.com', 'testpass']);
    
    echo "Insertion: " . ($result ? "SUCCÈS" : "ÉCHEC") . "\n";
    echo "ID inséré: " . $pdo->lastInsertId() . "\n\n";
    
    // Vérifier ce qui a été inséré
    $stmt = $pdo->query('SELECT * FROM users WHERE email = "test@example.com"');
    $user = $stmt->fetch();
    
    if ($user) {
        echo "Utilisateur trouvé dans la DB:\n";
        print_r($user);
    } else {
        echo "Utilisateur NON trouvé dans la DB\n";
    }
    
    // Nettoyer
    $pdo->exec('DELETE FROM users WHERE email = "test@example.com"');
    echo "\nUtilisateur de test supprimé.\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}
