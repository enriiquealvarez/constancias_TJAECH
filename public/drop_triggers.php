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
