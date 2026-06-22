<?php
header('Content-Type: text/plain; charset=utf-8');

try {
    $envFile = __DIR__ . '/../.env';
    
    if (!file_exists($envFile)) {
        die("Error: .env no encontrado\n");
    }
    
    // Read .env file
    $env = [];
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') === false || $line[0] === '#') {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, '"\'');
        $env[$key] = $value;
    }
    
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $database = $env['DB_DATABASE'] ?? 'tjaechgob_constancias_tja';
    $username = $env['DB_USERNAME'] ?? 'root';
    $password = $env['DB_PASSWORD'] ?? '';
    
    if (!$host || !$database) {
        die("Error: DB_HOST o DB_DATABASE no configurados\n");
    }
    
    echo "Conectando a: $host / $database\n\n";
    
    $pdo = new PDO(
        'mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8mb4',
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Ejecutando migración: Agregar PONENTE al enum del campo type\n";
    echo str_repeat("=", 80) . "\n\n";
    
    // Verificar la definición actual
    $stmt = $pdo->query("DESCRIBE participants");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $currentType = null;
    
    foreach ($cols as $col) {
        if ($col['Field'] === 'type') {
            $currentType = $col['Type'];
            echo "Definición actual: $currentType\n\n";
            break;
        }
    }
    
    // Ejecutar la migración
    echo "Ejecutando: ALTER TABLE participants MODIFY COLUMN type ENUM('INTERNAL','EXTERNAL','PONENTE') NOT NULL DEFAULT 'INTERNAL'\n\n";
    
    $pdo->exec("ALTER TABLE participants MODIFY COLUMN type ENUM('INTERNAL','EXTERNAL','PONENTE') NOT NULL DEFAULT 'INTERNAL'");
    
    echo "✓ Migración ejecutada exitosamente\n\n";
    
    // Verificar que se aplicó
    echo "Verificando...\n";
    $stmt = $pdo->query("DESCRIBE participants");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cols as $col) {
        if ($col['Field'] === 'type') {
            echo "Nueva definición: " . $col['Type'] . "\n";
            
            if (strpos($col['Type'], 'PONENTE') !== false) {
                echo "\n✓ ¡PONENTE agregado correctamente al enum!\n";
            } else {
                echo "\n✗ Error: PONENTE no está en el enum\n";
            }
            break;
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString();
}
