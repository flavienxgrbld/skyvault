<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

try {
    // ===== INVOICES =====
    if (preg_match('#/invoices/?$#', $path) && $method === 'GET') {
        // Liste des factures avec infos client
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? null;
        
        $sql = "SELECT i.*, c.name as client_name, c.email as client_email 
                FROM invoices i 
                JOIN clients c ON i.client_id = c.id 
                WHERE 1=1";
        $params = [];
        
        if ($status) {
            $sql .= " AND i.status = ?";
            $params[] = $status;
        }
        if ($search) {
            $sql .= " AND (i.invoice_number LIKE ? OR c.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY i.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll());
        exit;
    }
    
    if (preg_match('#/invoices/(\d+)$#', $path, $matches) && $method === 'GET') {
        // Détails d'une facture avec lignes et client
        $id = $matches[1];
        
        $stmt = $pdo->prepare("SELECT i.*, c.* FROM invoices i JOIN clients c ON i.client_id = c.id WHERE i.id = ?");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch();
        
        if (!$invoice) {
            http_response_code(404);
            echo json_encode(['error' => 'Invoice not found']);
            exit;
        }
        
        // Récupérer les lignes
        $stmt = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY position");
        $stmt->execute([$id]);
        $invoice['items'] = $stmt->fetchAll();
        
        echo json_encode($invoice);
        exit;
    }
    
    if (preg_match('#/invoices/?$#', $path) && $method === 'POST') {
        // Créer une facture
        $clientId = $input['client_id'] ?? null;
        $issueDate = $input['issue_date'] ?? date('Y-m-d');
        $dueDate = $input['due_date'] ?? date('Y-m-d', strtotime('+30 days'));
        $items = $input['items'] ?? [];
        
        if (!$clientId || empty($items)) {
            http_response_code(400);
            echo json_encode(['error' => 'client_id and items are required']);
            exit;
        }
        
        // Générer numéro de facture
        $year = date('Y');
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM invoices WHERE invoice_number LIKE 'FAC-$year-%'");
        $count = $stmt->fetch()['count'] + 1;
        $invoiceNumber = sprintf('FAC-%s-%03d', $year, $count);
        
        // Calculer totaux
        $subtotal = 0;
        $taxAmount = 0;
        foreach ($items as $item) {
            $qty = floatval($item['quantity'] ?? 1);
            $price = floatval($item['unit_price'] ?? 0);
            $taxRate = floatval($item['tax_rate'] ?? 20) / 100;
            $lineTotal = $qty * $price;
            $subtotal += $lineTotal;
            $taxAmount += $lineTotal * $taxRate;
        }
        $total = $subtotal + $taxAmount;
        
        // Insérer la facture
        $stmt = $pdo->prepare("INSERT INTO invoices (invoice_number, client_id, status, issue_date, due_date, subtotal, tax_amount, total, notes, payment_terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $invoiceNumber,
            $clientId,
            $input['status'] ?? 'draft',
            $issueDate,
            $dueDate,
            $subtotal,
            $taxAmount,
            $total,
            $input['notes'] ?? null,
            $input['payment_terms'] ?? null
        ]);
        
        $invoiceId = $pdo->lastInsertId();
        
        // Insérer les lignes
        $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, product_id, description, quantity, unit_price, tax_rate, line_total, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($items as $index => $item) {
            $qty = floatval($item['quantity'] ?? 1);
            $price = floatval($item['unit_price'] ?? 0);
            $taxRate = floatval($item['tax_rate'] ?? 20);
            $lineTotal = $qty * $price;
            
            $stmt->execute([
                $invoiceId,
                $item['product_id'] ?? null,
                $item['description'],
                $qty,
                $price,
                $taxRate,
                $lineTotal,
                $index
            ]);
        }
        
        echo json_encode(['id' => $invoiceId, 'invoice_number' => $invoiceNumber]);
        exit;
    }
    
    if (preg_match('#/invoices/(\d+)$#', $path, $matches) && $method === 'PUT') {
        // Mettre à jour une facture
        $id = $matches[1];
        $items = $input['items'] ?? [];
        
        // Recalculer totaux
        $subtotal = 0;
        $taxAmount = 0;
        foreach ($items as $item) {
            $qty = floatval($item['quantity'] ?? 1);
            $price = floatval($item['unit_price'] ?? 0);
            $taxRate = floatval($item['tax_rate'] ?? 20) / 100;
            $lineTotal = $qty * $price;
            $subtotal += $lineTotal;
            $taxAmount += $lineTotal * $taxRate;
        }
        $total = $subtotal + $taxAmount;
        
        // Mettre à jour la facture
        $stmt = $pdo->prepare("UPDATE invoices SET client_id = ?, status = ?, issue_date = ?, due_date = ?, subtotal = ?, tax_amount = ?, total = ?, notes = ?, payment_terms = ? WHERE id = ?");
        $stmt->execute([
            $input['client_id'],
            $input['status'] ?? 'draft',
            $input['issue_date'],
            $input['due_date'],
            $subtotal,
            $taxAmount,
            $total,
            $input['notes'] ?? null,
            $input['payment_terms'] ?? null,
            $id
        ]);
        
        // Supprimer et recréer les lignes
        $pdo->prepare("DELETE FROM invoice_items WHERE invoice_id = ?")->execute([$id]);
        
        $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, product_id, description, quantity, unit_price, tax_rate, line_total, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($items as $index => $item) {
            $qty = floatval($item['quantity'] ?? 1);
            $price = floatval($item['unit_price'] ?? 0);
            $taxRate = floatval($item['tax_rate'] ?? 20);
            $lineTotal = $qty * $price;
            
            $stmt->execute([
                $id,
                $item['product_id'] ?? null,
                $item['description'],
                $qty,
                $price,
                $taxRate,
                $lineTotal,
                $index
            ]);
        }
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    if (preg_match('#/invoices/(\d+)$#', $path, $matches) && $method === 'DELETE') {
        $id = $matches[1];
        $stmt = $pdo->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // ===== CLIENTS =====
    if (preg_match('#/clients/?$#', $path) && $method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM clients ORDER BY name");
        echo json_encode($stmt->fetchAll());
        exit;
    }
    
    if (preg_match('#/clients/?$#', $path) && $method === 'POST') {
        $stmt = $pdo->prepare("INSERT INTO clients (name, email, phone, address, city, postal_code, country, siret, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $input['name'],
            $input['email'] ?? null,
            $input['phone'] ?? null,
            $input['address'] ?? null,
            $input['city'] ?? null,
            $input['postal_code'] ?? null,
            $input['country'] ?? 'France',
            $input['siret'] ?? null,
            $input['notes'] ?? null
        ]);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        exit;
    }
    
    if (preg_match('#/clients/(\d+)$#', $path, $matches) && $method === 'PUT') {
        $id = $matches[1];
        $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, country = ?, siret = ?, notes = ? WHERE id = ?");
        $stmt->execute([
            $input['name'],
            $input['email'] ?? null,
            $input['phone'] ?? null,
            $input['address'] ?? null,
            $input['city'] ?? null,
            $input['postal_code'] ?? null,
            $input['country'] ?? 'France',
            $input['siret'] ?? null,
            $input['notes'] ?? null,
            $id
        ]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // ===== PRODUCTS =====
    if (preg_match('#/products/?$#', $path) && $method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY name");
        echo json_encode($stmt->fetchAll());
        exit;
    }
    
    if (preg_match('#/products/?$#', $path) && $method === 'POST') {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, type, price, unit, tax_rate, reference) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $input['name'],
            $input['description'] ?? null,
            $input['type'] ?? 'service',
            $input['price'] ?? 0,
            $input['unit'] ?? 'unité',
            $input['tax_rate'] ?? 20,
            $input['reference'] ?? null
        ]);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        exit;
    }
    
    // ===== STATS =====
    if (preg_match('#/stats/?$#', $path) && $method === 'GET') {
        $stats = [];
        
        // Total des factures par statut
        $stmt = $pdo->query("SELECT status, COUNT(*) as count, SUM(total) as sum FROM invoices GROUP BY status");
        $stats['by_status'] = $stmt->fetchAll();
        
        // CA du mois
        $stmt = $pdo->query("SELECT SUM(total) as revenue FROM invoices WHERE MONTH(issue_date) = MONTH(CURRENT_DATE) AND status = 'paid'");
        $stats['monthly_revenue'] = $stmt->fetch()['revenue'] ?? 0;
        
        // Nombre de clients
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM clients");
        $stats['total_clients'] = $stmt->fetch()['count'];
        
        echo json_encode($stats);
        exit;
    }
    
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
