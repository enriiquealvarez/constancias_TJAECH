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
        // Try to use hardcoded values or read from config
        echo "Archivo .env no encontrado. Intentando usar configuración del sistema...\n\n";
        
        // Try to use database config from file
        if (file_exists(__DIR__ . '/../config/database.php')) {
            $dbConfig = require __DIR__ . '/../config/database.php';
            $host = $dbConfig['host'] ?? 'localhost';
            $database = $dbConfig['database'] ?? '';
            $username = $dbConfig['username'] ?? 'root';
            $password = $dbConfig['password'] ?? '';
        } else {
            die("Error: No se encontró .env ni database.php\n");
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
