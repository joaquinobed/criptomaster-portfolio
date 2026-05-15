<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            $coin_id = intval($_GET['moneda'] ?? 0);
            $exchange_id = intval($_GET['exchange'] ?? 0);
            $page = intval($_GET['page'] ?? 1);
            $per_page = 15;
            $offset = ($page - 1) * $per_page;
            
            $where = " WHERE 1=1";
            $params = [];

            if ($coin_id > 0) {
                $where .= " AND i.coin_id = ?";
                $params[] = $coin_id;
            }
            if ($exchange_id > 0) {
                $where .= " AND i.exchange_id = ?";
                $params[] = $exchange_id;
            }

            // Contar total para paginación
            $count_stmt = $db->prepare("SELECT COUNT(*) FROM investments i $where");
            $count_stmt->execute($params);
            $total_rows = $count_stmt->fetchColumn();
            $total_pages = ceil($total_rows / $per_page);

            // Totales globales para el filtro actual
            $totals_stmt = $db->prepare("SELECT SUM(buy) as total_buy, SUM(total) as total_crypto FROM investments i $where");
            $totals_stmt->execute($params);
            $global_totals = $totals_stmt->fetch();

            $query = "SELECT i.*, c.name as coin_name, c.symbol, c.img, c.decimales, c.price as current_price, e.name as exchange_name 
                      FROM investments i 
                      JOIN coins c ON i.coin_id = c.id 
                      JOIN exchanges e ON i.exchange_id = e.id 
                      $where
                      ORDER BY i.created_at DESC, i.id DESC
                      LIMIT ? OFFSET ?";
            
            $stmt = $db->prepare($query);
            $params_with_limit = array_merge($params, [$per_page, $offset]);
            $stmt->execute($params_with_limit);
            $investments = $stmt->fetchAll();

            echo json_encode([
                'data' => $investments,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'total_rows' => $total_rows
                ],
                'totals' => $global_totals
            ]);
            break;

        case 'save':
            $buy = floatval($_POST['compra'] ?? 0);
            $price = floatval($_POST['precio'] ?? 0);
            $date = $_POST['fecha'] ?? date('Y-m-d');
            $coin_id = intval($_POST['moneda'] ?? 0);
            $exchange_id = intval($_POST['exchange'] ?? 0);
            
            if ($buy <= 0 || $price <= 0 || $coin_id <= 0 || $exchange_id <= 0) {
                throw new Exception("Todos los campos son obligatorios y deben ser válidos.");
            }

            $total = $buy / $price;

            $stmt = $db->prepare("INSERT INTO investments (buy, price, total, coin_id, exchange_id, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$buy, $price, $total, $coin_id, $exchange_id, $date]);
            
            echo json_encode(['status' => 'success']);
            break;

        case 'update':
            $id = intval($_POST['edit_id'] ?? 0);
            $buy = floatval($_POST['edit_compra'] ?? 0);
            $price = floatval($_POST['edit_precio'] ?? 0);
            $date = $_POST['edit_fecha'] ?? date('Y-m-d');
            $coin_id = intval($_POST['edit_moneda'] ?? 0);
            $exchange_id = intval($_POST['edit_exchange'] ?? 0);

            if ($id <= 0 || $buy <= 0 || $price <= 0 || $coin_id <= 0 || $exchange_id <= 0) {
                throw new Exception("Datos de edición no válidos.");
            }

            $total = $buy / $price;

            $stmt = $db->prepare("UPDATE investments SET buy = ?, price = ?, total = ?, created_at = ?, coin_id = ?, exchange_id = ? WHERE id = ?");
            if ($stmt->execute([$buy, $price, $total, $date, $coin_id, $exchange_id, $id])) {
                echo json_encode(['status' => 'success']);
            } else {
                throw new Exception("Error al actualizar en la base de datos.");
            }
            break;

        case 'delete':
            $id = intval($_GET['id']);
            $stmt = $db->prepare("DELETE FROM investments WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success']);
            break;

        default:
            throw new Exception("Acción no válida");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
