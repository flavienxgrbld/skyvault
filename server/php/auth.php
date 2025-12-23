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
        
        $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Pour cette démo: accepter "admin" comme mot de passe
        // En production, utiliser password_verify($password, $user['password_hash'])
        if ($user && $password === 'admin') {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            
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
