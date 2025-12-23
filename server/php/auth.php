<?php
// Gestion de l'authentification admin
session_start();
header('Content-Type: application/json');

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        require __DIR__ . '/db.php';
        
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            
            // Mettre à jour last_login
            $stmt = $pdo->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?');
            $stmt->execute([$admin['id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
    }
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// Check session
if (isset($_GET['check'])) {
    echo json_encode([
        'authenticated' => isset($_SESSION['admin_id']),
        'user' => $_SESSION['admin_name'] ?? null
    ]);
    exit;
}
