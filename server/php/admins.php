<?php
// API pour récupérer et gérer les administrateurs
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    // GET - Récupérer tous les administrateurs
    if ($method === 'GET') {
        $stmt = $pdo->query('SELECT id, name, email, status, last_login, created_at FROM admins ORDER BY created_at DESC');
        $admins = $stmt->fetchAll();
        echo json_encode($admins);
    }
    
    // POST - Créer un nouvel administrateur
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $passwordHash = !empty($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null;
        
        $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['email'],
            $passwordHash,
            $data['status'] ?? 'active'
        ]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    }
    
    // PUT - Modifier un administrateur
    elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['password'])) {
            // Mise à jour avec nouveau mot de passe
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE admins SET name=?, email=?, password_hash=?, status=? WHERE id=?');
            $stmt->execute([
                $data['name'],
                $data['email'],
                $passwordHash,
                $data['status'] ?? 'active',
                $data['id']
            ]);
        } else {
            // Mise à jour sans changer le mot de passe
            $stmt = $pdo->prepare('UPDATE admins SET name=?, email=?, status=? WHERE id=?');
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['status'] ?? 'active',
                $data['id']
            ]);
        }
        
        echo json_encode(['success' => true]);
    }
    
    // DELETE - Supprimer un administrateur
    elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM admins WHERE id = ?');
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
