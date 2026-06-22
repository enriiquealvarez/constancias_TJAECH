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

    echo "Buscando triggers en la tabla participants...\n\n";
    
    // Find all triggers on participants table
    $stmt = $pdo->query("SELECT TRIGGER_NAME FROM INFORMATION_SCHEMA.TRIGGERS WHERE EVENT_OBJECT_TABLE = 'participants'");
    $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($triggers)) {
        echo "✓ No hay triggers en la tabla participants\n";
    } else {
        echo "Se encontraron " . count($triggers) . " trigger(s). Eliminando...\n\n";
        
        foreach ($triggers as $trigger) {
            $triggerName = $trigger['TRIGGER_NAME'];
            echo "Eliminando trigger: $triggerName\n";
            
            try {
                $pdo->exec("DROP TRIGGER IF EXISTS `$triggerName`");
                echo "  ✓ Eliminado correctamente\n\n";
            } catch (Exception $e) {
                echo "  ✗ Error: " . $e->getMessage() . "\n\n";
            }
        }
    }
    
    // Verify no triggers remain
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TRIGGERS WHERE EVENT_OBJECT_TABLE = 'participants'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "Verificación final: " . $result['count'] . " trigger(s) en la tabla participants\n";
    
    if ($result['count'] == 0) {
        echo "✓ ¡Todos los triggers han sido eliminados exitosamente!\n";
    } else {
        echo "✗ Aún hay triggers. Verifica los logs anteriores.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString();
}
