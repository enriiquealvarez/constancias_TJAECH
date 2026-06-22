<?php
header('Content-Type: text/plain; charset=utf-8');

try {
    // Try multiple locations for .env
    $envPaths = [
        __DIR__ . '/../.env',
        __DIR__ . '/../../.env',
        __DIR__ . '/.env',
    ];
    
    $host = $database = $username = $password = null;
    
    foreach ($envPaths as $path) {
        if (file_exists($path)) {
            echo "Encontrado archivo .env en: $path\n";
            $env = parse_ini_file($path);
            
            if ($env) {
                $host = $env['DB_HOST'] ?? null;
                $database = $env['DB_DATABASE'] ?? null;
                $username = $env['DB_USERNAME'] ?? null;
                $password = $env['DB_PASSWORD'] ?? null;
                
                if ($host && $database && $username !== null) {
                    echo "✓ Configuración válida encontrada\n\n";
                    break;
                }
            }
        }
    }
    
    // If not found in .env, try config/database.php
    if (!$host && file_exists(__DIR__ . '/../config/database.php')) {
        echo "Intentando usar config/database.php\n";
        $dbConfig = require __DIR__ . '/../config/database.php';
        $host = $dbConfig['host'] ?? null;
        $database = $dbConfig['database'] ?? null;
        $username = $dbConfig['username'] ?? null;
        $password = $dbConfig['password'] ?? null;
        
        if ($host && $database && $username !== null) {
            echo "✓ Configuración válida encontrada en config/database.php\n\n";
        }
    }
    
    if (!$host || !$database) {
        die("Error: No se encontró configuración de base de datos válida\n");
    }

    echo "Conectando a: $host / $database\n\n";
    
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
        echo "✓ No hay triggers en la tabla participants\n";
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
