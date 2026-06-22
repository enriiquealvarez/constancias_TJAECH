<?php
// Migration script to add PONENTE to participants type enum

// Load environment
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && $line[0] !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $_ENV[$key] = $value;
        }
    }
}

try {
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $database = $_ENV['DB_DATABASE'] ?? 'tjaechgob_constancias_tja';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $sql = "ALTER TABLE participants MODIFY COLUMN type ENUM('INTERNAL','EXTERNAL','PONENTE') NOT NULL DEFAULT 'INTERNAL'";
    $pdo->exec($sql);
    
    echo "✓ Migración exitosa: Se agregó 'PONENTE' al campo type de la tabla participants\n";
} catch (\Exception $e) {
    echo "✗ Error en migración: " . $e->getMessage() . "\n";
    exit(1);
}
