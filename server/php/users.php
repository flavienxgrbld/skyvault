<?php
// API pour récupérer et gérer les utilisateurs
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    // GET - Récupérer tous les utilisateurs
    if ($method === 'GET') {
        $stmt = $pdo->query('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC');
        $users = $stmt->fetchAll();
        echo json_encode($users);
    }
    
    // POST - Créer un nouveau utilisateur
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'] ?? null // En production: password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    }
    
    // PUT - Modifier un utilisateur
    elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['password'])) {
            // Mise à jour avec nouveau mot de passe
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, password_hash=? WHERE id=?');
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['password'], // En production: password_hash($data['password'], PASSWORD_BCRYPT)
                $data['id']
            ]);
        } else {
            // Mise à jour sans changer le mot de passe
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=? WHERE id=?');
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['id']
            ]);
        }
        
        echo json_encode(['success' => true]);
    }
    
    // DELETE - Supprimer un utilisateur
    elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
