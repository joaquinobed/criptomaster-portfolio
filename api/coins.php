<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $db->query("SELECT * FROM coins ORDER BY name ASC");
        $results = $stmt->fetchAll();
        echo json_encode($results);
    } elseif ($action === 'save') {
        $name = trim($_POST['name'] ?? '');
        $symbol = strtoupper(trim($_POST['symbol'] ?? ''));
        $decimales = intval($_POST['decimales'] ?? 6);
        $img = trim($_POST['img'] ?? ($symbol . '.png'));

        if (!$name || !$symbol) throw new Exception("Nombre y Símbolo son requeridos");

        $stmt = $db->prepare("INSERT INTO coins (name, symbol, decimales, img) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $symbol, $decimales, $img]);
        echo json_encode(['status' => 'success', 'message' => 'Moneda creada']);

    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $symbol = strtoupper(trim($_POST['symbol'] ?? ''));
        $decimales = intval($_POST['decimales'] ?? 6);
        $img = trim($_POST['img'] ?? '');

        if (!$id || !$name || !$symbol) throw new Exception("Datos incompletos");

        $stmt = $db->prepare("UPDATE coins SET name = ?, symbol = ?, decimales = ?, img = ? WHERE id = ?");
        $stmt->execute([$name, $symbol, $decimales, $img, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Moneda actualizada']);

    } elseif ($action === 'delete') {
        $id = intval($_GET['id'] ?? 0);
        
        $check = $db->prepare("SELECT COUNT(*) FROM investments WHERE coin_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            throw new Exception("No se puede eliminar: Esta moneda tiene inversiones registradas.");
        }

        $stmt = $db->prepare("DELETE FROM coins WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Moneda eliminada']);

    } elseif ($action === 'update_price') {
        $id = intval($_POST['id']);
        $price = floatval($_POST['price']);
        
        $stmt = $db->prepare("UPDATE coins SET price = ?, is_manual = 1 WHERE id = ?");
        $stmt->execute([$price, $id]);
        
        echo json_encode(['status' => 'success', 'message' => 'Precio actualizado y bloqueado para sincronización']);
    } elseif ($action === 'enable_sync') {
        $id = intval($_POST['id']);
        $stmt = $db->prepare("UPDATE coins SET is_manual = 0 WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Sincronización reactivada']);
    } else {
        $stmt = $db->query("SELECT * FROM coins ORDER BY name ASC");
        echo json_encode($stmt->fetchAll());
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
