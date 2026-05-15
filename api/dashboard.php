<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

try {
    $exchange_id = intval($_GET['exchange'] ?? 0);
    $str = "";
    $params = [];
    if ($exchange_id > 0) {
        $str = " AND i.exchange_id = ?";
        $params[] = $exchange_id;
    }

    // Obtener resumen por moneda
    $query = "SELECT 
                c.id, c.name, c.symbol, c.img, c.price as current_price, c.decimales, c.is_manual,
                SUM(i.total) as total_balance,
                SUM(i.buy) as total_invested
              FROM coins c
              JOIN investments i ON c.id = i.coin_id
              WHERE 1=1 $str
              GROUP BY c.id";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $resumen = $stmt->fetchAll();

    $stats = [];
    $total_portfolio_value = 0;
    $total_portfolio_invested = 0;

    foreach ($resumen as $row) {
        $value_usd = $row['total_balance'] * $row['current_price'];
        $utility = $value_usd - $row['total_invested'];
        $percent = $row['total_invested'] > 0 ? ($utility / $row['total_invested']) * 100 : 0;
        $avg_buy_price = $row['total_invested'] / $row['total_balance'];

        $total_portfolio_value += $value_usd;
        $total_portfolio_invested += $row['total_invested'];

        $stats[] = [
            'name' => $row['name'],
            'symbol' => $row['symbol'],
            'img' => $row['img'],
            'balance' => $row['total_balance'],
            'decimales' => $row['decimales'],
            'current_price' => $row['current_price'],
            'avg_price' => $avg_buy_price,
            'investment' => $row['total_invested'],
            'value_usd' => $value_usd,
            'utility' => $utility,
            'percent' => $percent,
            'is_manual' => $row['is_manual'],
            'id' => $row['id']
        ];
    }

    $total_utility = $total_portfolio_value - $total_portfolio_invested;
    $total_percent = $total_portfolio_invested > 0 ? ($total_utility / $total_portfolio_invested) * 100 : 0;

    echo json_encode([
        'coins' => $stats,
        'totals' => [
            'value' => $total_portfolio_value,
            'invested' => $total_portfolio_invested,
            'utility' => $total_utility,
            'percent' => $total_percent
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
