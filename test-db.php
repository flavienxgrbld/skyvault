<?php
// Test rapide de connexion DB (à supprimer après test)
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test DB</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .info { color: #3b82f6; }
    </style>
</head>
<body>
    <h2>Test de connexion MySQL</h2>
    
    <?php
    // 1. Vérifier extension
    echo "<p class='info'>1. Extension PDO MySQL: ";
    if (extension_loaded('pdo_mysql')) {
        echo "<span class='success'>✓ Chargée</span></p>";
    } else {
        echo "<span class='error'>✗ Non disponible</span></p>";
        echo "<p class='error'>Activez extension=pdo_mysql dans php.ini et redémarrez Apache</p>";
        exit;
    }
    
    // 2. Tester connexion
    echo "<p class='info'>2. Connexion à la base: ";
    try {
        require __DIR__ . '/server/php/db.php';
        echo "<span class='success'>✓ Connecté</span></p>";
        
        // 3. Requête test
        $stmt = $pdo->query('SELECT VERSION() as version, DATABASE() as dbname');
        $info = $stmt->fetch();
        
        echo "<p class='info'>3. Version MySQL: <span class='success'>{$info['version']}</span></p>";
        echo "<p class='info'>4. Base de données: <span class='success'>{$info['dbname']}</span></p>";
        
        // 5. Compter tables
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p class='info'>5. Nombre de tables: <span class='success'>" . count($tables) . "</span></p>";
        
        if (count($tables) > 0) {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>✗ Erreur</span></p>";
        echo "<p class='error'>Message: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
    
    <hr>
    <p><a href="admin/index.html">→ Retour à l'admin</a></p>
</body>
</html>
