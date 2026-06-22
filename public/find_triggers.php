<?php
header('Content-Type: text/plain; charset=utf-8');

try {
    $envFile = __DIR__ . '/../.env';
    
    if (!file_exists($envFile)) {
        die("Error: .env no encontrado en $envFile\n");
    }
    
    echo "Leyendo .env desde: $envFile\n\n";
    
    // Read .env file line by line
    $env = [];
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') === false || $line[0] === '#') {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Remove quotes if present
        $value = trim($value, '"\'');
        $env[$key] = $value;
    }
    
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $database = $env['DB_DATABASE'] ?? 'tjaechgob_constancias_tja';
    $username = $env['DB_USERNAME'] ?? 'root';
    $password = $env['DB_PASSWORD'] ?? '';
    
    if (!$host || !$database) {
        die("Error: DB_HOST o DB_DATABASE no configurados en .env\n");
    }
    
    echo "Host: $host\n";
    echo "Database: $database\n";
    echo "Username: $username\n";
    echo "Password: " . (strlen($password) > 0 ? "***" : "(vacío)") . "\n\n";
    
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
            
            echo "Para eliminar este trigger, ejecuta:\n";
            echo "DROP TRIGGER IF EXISTS `{$trigger['TRIGGER_NAME']}`;\n";
            echo "\n" . str_repeat("=", 80) . "\n\n";
        }
    }
    
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
