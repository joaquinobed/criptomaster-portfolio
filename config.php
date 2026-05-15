<?php
/**
 * Configuración de la base de datos y constantes
 */

define('DB_FILE', __DIR__ . '/database.sqlite');

try {
    // Conexión via PDO para SQLite
    $db = new PDO("sqlite:" . DB_FILE);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Crear tablas si no existen (Migración automática)
    $db->exec("CREATE TABLE IF NOT EXISTS coins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        symbol TEXT NOT NULL,
        img TEXT,
        price REAL DEFAULT 0,
        decimales INTEGER DEFAULT 6,
        market_cap_rank INTEGER,
        is_manual INTEGER DEFAULT 0
    )");

    // Intentar agregar la columna is_manual si no existe (para instalaciones existentes)
    try {
        $db->exec("ALTER TABLE coins ADD COLUMN is_manual INTEGER DEFAULT 0");
    } catch (Exception $e) {
        // La columna probablemente ya existe
    }

    $db->exec("CREATE TABLE IF NOT EXISTS exchanges (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS investments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        buy REAL NOT NULL,
        price REAL NOT NULL,
        total REAL NOT NULL,
        exchange_id INTEGER,
        coin_id INTEGER,
        created_at DATE DEFAULT CURRENT_DATE,
        FOREIGN KEY(exchange_id) REFERENCES exchanges(id),
        FOREIGN KEY(coin_id) REFERENCES coins(id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS perfil (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre_comercial TEXT,
        propietario TEXT,
        telefono TEXT,
        direccion TEXT,
        email TEXT,
        web TEXT
    )");

    // Semilla inicial si está vacío (Migración con los datos proporcionados por el usuario)
    $count = $db->query("SELECT COUNT(*) FROM coins")->fetchColumn();
    if ($count == 0) {
        require_once __DIR__ . '/migrate.php';
    }

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

/**
 * Helper para obtener IDs (compatible con la lógica original)
 */
function get_data($table, $field, $target_field, $id, $db) {
    $stmt = $db->prepare("SELECT $field FROM $table WHERE $target_field = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    return $res ? $res[$field] : '';
}
