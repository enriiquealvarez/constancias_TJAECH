<?php
header('Content-Type: text/plain; charset=utf-8');

try {
    // Try multiple locations for .env
    $envPaths = [
        __DIR__ . '/../.env',
        __DIR__ . '/../../.env',
        __DIR__ . '/.env',
    ];
    
    $envFile = null;
    foreach ($envPaths as $path) {
        if (file_exists($path)) {
            $envFile = $path;
            break;
        }
    }
    
    if (!$envFile) {
        // Try to use database config from file
        if (file_exists(__DIR__ . '/../config/database.php')) {
            $dbConfig = require __DIR__ . '/../config/database.php';
            $host = $dbConfig['host'] ?? 'localhost';
            $database = $dbConfig['database'] ?? '';
            $username = $dbConfig['username'] ?? 'root';
            $password = $dbConfig['password'] ?? '';
        } else {
            die("Error: No se encontró .env ni config/database.php\n");
        }
    } else {
        $env = parse_ini_file($envFile);
        if (!$env || !isset($env['DB_HOST'])) {
            die("Error: Archivo .env no tiene configuración válida\n");
        }
        $host = $env['DB_HOST'];
        $database = $env['DB_DATABASE'];
        $username = $env['DB_USERNAME'];
        $password = $env['DB_PASSWORD'];
    }

    $pdo = new PDO(
        'mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8mb4',
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Find all triggers on participants table
    $stmt = $pdo->query("SELECT TRIGGER_SCHEMA, TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE, ACTION_STATEMENT FROM INFORMATION_SCHEMA.TRIGGERS WHERE EVENT_OBJECT_TABLE = 'participants'");
    $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($triggers)) {
        echo "No hay triggers en la tabla participants\n";
    } else {
        echo "TRIGGERS ENCONTRADOS:\n";
        echo str_repeat("=", 80) . "\n\n";
        
        foreach ($triggers as $trigger) {
            echo "Nombre: {$trigger['TRIGGER_NAME']}\n";
            echo "Evento: {$trigger['EVENT_MANIPULATION']} en tabla {$trigger['EVENT_OBJECT_TABLE']}\n";
            echo "Schema: {$trigger['TRIGGER_SCHEMA']}\n";
            echo "\nCódigo del TRIGGER:\n";
            echo $trigger['ACTION_STATEMENT'] . "\n";
            echo "\n" . str_repeat("-", 80) . "\n\n";
            
            // Mostrar comando para eliminar
            echo "Para eliminar este trigger, ejecuta:\n";
            echo "DROP TRIGGER IF EXISTS `{$trigger['TRIGGER_NAME']}`;\n";
            echo "\n" . str_repeat("=", 80) . "\n\n";
        }
    }
    
    // Also check if there are any constraints or default values
    echo "\nDEFINICION DEL CAMPO TYPE:\n";
    echo str_repeat("=", 80) . "\n";
    $stmt = $pdo->query("DESCRIBE participants");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        if ($col['Field'] === 'type') {
            echo "Field: " . $col['Field'] . "\n";
            echo "Type: " . $col['Type'] . "\n";
            echo "Null: " . $col['Null'] . "\n";
            echo "Default: " . $col['Default'] . "\n";
            echo "Extra: " . $col['Extra'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString();
}
