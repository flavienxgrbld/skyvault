<?php
// API pour récupérer et gérer les modules
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    // GET - Récupérer tous les modules
    if ($method === 'GET') {
        $stmt = $pdo->query('SELECT id, slug, title, description, price, path, category, active FROM modules ORDER BY category, title');
        $modules = $stmt->fetchAll();
        echo json_encode($modules);
    }
    
    // POST - Créer un nouveau module
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare('INSERT INTO modules (slug, title, description, price, path, category) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['slug'],
            $data['title'],
            $data['description'],
            $data['price'],
            $data['path'],
            $data['category'] ?? null
        ]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    }
    
    // PUT - Modifier un module
    elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare('UPDATE modules SET slug=?, title=?, description=?, price=?, path=?, category=?, active=? WHERE id=?');
        $stmt->execute([
            $data['slug'],
            $data['title'],
            $data['description'],
            $data['price'],
            $data['path'],
            $data['category'] ?? null,
            $data['active'] ?? 1,
            $data['id']
        ]);
        
        echo json_encode(['success' => true]);
    }
    
    // DELETE - Supprimer un module
    elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM modules WHERE id = ?');
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
