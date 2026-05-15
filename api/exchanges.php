<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $db->query("SELECT * FROM exchanges ORDER BY name ASC");
        $results = $stmt->fetchAll();
        echo json_encode($results);
    } elseif ($action === 'save') {
        $name = trim($_POST['name'] ?? '');
        if (!$name) throw new Exception("Nombre requerido");
        
        $stmt = $db->prepare("INSERT INTO exchanges (name) VALUES (?)");
        $stmt->execute([$name]);
        echo json_encode(['status' => 'success', 'message' => 'Exchange creado']);
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if (!$id || !$name) throw new Exception("Datos inválidos");

        $stmt = $db->prepare("UPDATE exchanges SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Exchange actualizado']);
    } elseif ($action === 'delete') {
        $id = intval($_GET['id'] ?? 0);
        
        $check = $db->prepare("SELECT COUNT(*) FROM investments WHERE exchange_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            throw new Exception("No se puede eliminar: Este exchange tiene inversiones registradas.");
        }

        $stmt = $db->prepare("DELETE FROM exchanges WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Exchange eliminado']);
    } else {
        $stmt = $db->query("SELECT * FROM exchanges ORDER BY name ASC");
        echo json_encode($stmt->fetchAll());
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
