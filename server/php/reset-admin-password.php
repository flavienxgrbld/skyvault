<?php
// Script pour réinitialiser le mot de passe admin
require __DIR__ . '/db.php';

// Configuration
$email = 'admin@localhost';
$newPassword = 'admin123';

try {
    // Vérifier si l'admin existe
    $stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Mettre à jour le mot de passe
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE admins SET password_hash = ? WHERE email = ?');
        $stmt->execute([$passwordHash, $email]);
        
        echo "✅ Mot de passe réinitialisé avec succès !<br><br>";
        echo "Email: <strong>$email</strong><br>";
        echo "Nouveau mot de passe: <strong>$newPassword</strong><br>";
        echo "Hash généré: <code>$passwordHash</code><br><br>";
        echo "Vous pouvez maintenant vous connecter.";
    } else {
        // Créer un nouvel admin
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash, status) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Administrateur', $email, $passwordHash, 'active']);
        
        echo "✅ Administrateur créé avec succès !<br><br>";
        echo "Email: <strong>$email</strong><br>";
        echo "Mot de passe: <strong>$newPassword</strong><br>";
        echo "Hash généré: <code>$passwordHash</code><br><br>";
        echo "Vous pouvez maintenant vous connecter.";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>
