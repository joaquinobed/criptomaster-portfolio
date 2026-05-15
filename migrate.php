<?php
/**
 * Script de migración de datos (MySQL a SQLite)
 * Este archivo es llamado automáticamente por config.php si la base de datos está vacía.
 */

// Si no existe la conexión $db, intentamos cargarla (para ejecución manual)
if (!isset($db)) {
    require_once __DIR__ . '/config.php';
}

// Limpiar antes de migrar para evitar duplicados si se corre varias veces
$db->exec("DELETE FROM investments");
$db->exec("DELETE FROM coins");
$db->exec("DELETE FROM exchanges");
$db->exec("DELETE FROM perfil");

// Migración de Monedas (Coins)
$coins = [
    [1, 'Bitcoin', 'BTC', 'btc.png', 81444.67, 8, 1],
    [2, 'Cardano', 'ADA', 'ada.png', 0.2809, 4, 2],
    [3, 'Axie Infinity', 'AXS', 'axs.png', 1.423, 4, 3],
    [4, 'Oasis Network', 'ROSE', 'rose.png', 0.01145, 4, 4],
    [5, 'Polkadot', 'DOT', 'dot.png', 1.364, 4, 5],
    [6, 'The Sandbox', 'SAND', 'sand.png', 0.0817, 4, 6],
    [7, 'USDC', 'USDC', 'usdc.png', 1.00016, 4, 7],
    [8, 'BNB', 'BNB', 'bnb.png', 660.03, 4, 8],
    [9, 'Theta Token', 'THETA', 'theta.png', 0.239, 4, 9],
    [10, 'Solana', 'SOL', 'sol.png', 97.12, 4, 10],
    [11, 'Klever', 'KLV', 'klv.png', 0.0022, 4, 11],
    [12, 'USDT', 'USDT', 'usdt.png', 1, 4, 12],
    [13, 'Tron', 'TRX', 'trx.png', 0.3511, 4, 13],
    [14, 'Aptos', 'APT', 'apt.png', 1.125, 4, 13]
];

$stmt = $db->prepare("INSERT INTO coins (id, name, symbol, img, price, decimales, market_cap_rank) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($coins as $c) $stmt->execute($c);

// Migración de Exchanges
$exchanges = [
    [1, 'Binance'],
    [2, 'Coinbase'],
    [3, 'Kraken']
];

$stmt = $db->prepare("INSERT INTO exchanges (id, name) VALUES (?, ?)");
foreach ($exchanges as $e) $stmt->execute($e);

// Migración de Inversiones (Investments)
$investments = []; // Limpiamos las inversiones personales

$stmt = $db->prepare("INSERT INTO investments (id, buy, price, total, exchange_id, coin_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($investments as $i) $stmt->execute($i);

// Migración de Perfil
$db->exec("INSERT INTO perfil (id, nombre_comercial, propietario, telefono, direccion, email, web) VALUES 
(1, 'Mi Portafolio Cripto', 'Usuario', '+000-000-000', 'Dirección', 'email@example.com', 'www.example.com')");

echo "Migración completada con éxito. " . count($investments) . " registros migrados.";
