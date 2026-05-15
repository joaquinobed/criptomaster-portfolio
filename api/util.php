<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';

if ($type === 'coins') {
    $stmt = $db->query("SELECT * FROM coins ORDER BY market_cap_rank ASC");
    echo json_encode($stmt->fetchAll());
} elseif ($type === 'exchanges') {
    $stmt = $db->query("SELECT * FROM exchanges ORDER BY name ASC");
    echo json_encode($stmt->fetchAll());
}
