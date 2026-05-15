<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

try {
    $stmt = $db->query("SELECT id, symbol FROM coins WHERE is_manual = 0");
    $coins = $stmt->fetchAll();
    $updated = 0;

    $stables = ['USDT', 'USDC', 'BUSD', 'UST', 'DAI'];

    foreach ($coins as $coin) {
        $symbol = strtoupper($coin['symbol']);
        $price = null;

        if (in_array($symbol, $stables)) {
            $price = 1.0;
        } else {
            $pair = $symbol . 'USDT';
            $url = "https://api.binance.com/api/v3/ticker/price?symbol=$pair";
            $json = @file_get_contents($url);
            if ($json) {
                $data = json_decode($json, true);
                if (isset($data['price'])) {
                    $price = floatval($data['price']);
                }
            }
        }

        // Solo actualizar si obtuvimos un precio válido
        if ($price !== null && $price > 0) {
            $upd = $db->prepare("UPDATE coins SET price = ? WHERE id = ?");
            $upd->execute([$price, $coin['id']]);
            $updated++;
        }
    }

    echo json_encode(['status' => 'success', 'updated' => $updated]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
