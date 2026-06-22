<?php
header('Content-Type: text/plain; charset=utf-8');

try {
    $env = parse_ini_file(__DIR__ . '/../.env');
    
    if (!$env || !isset($env['DB_HOST'])) {
        die("Error: No se pudo leer el archivo .env\n");
    }

    $pdo = new PDO(
        'mysql:host=' . $env['DB_HOST'] . ';dbname=' . $env['DB_DATABASE'] . ';charset=utf8mb4',
        $env['DB_USERNAME'],
        $env['DB_PASSWORD'],
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
