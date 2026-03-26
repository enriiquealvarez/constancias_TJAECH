<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Core/Env.php';
app\Core\Env::load(__DIR__ . '/../.env');

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/Certificate.php';

use app\Core\Database;
use app\Models\Certificate;

header('Content-Type: text/plain');

try {
    $envPath = __DIR__ . '/../.env';
    echo "--- Diagnostico de Entorno ---\n";
    echo "Buscando .env en: " . realpath($envPath) . "\n";
    if (file_exists($envPath)) {
        echo ".env encontrado.\n";
    } else {
        echo "ALERTA: .env NO ENCONTRADO en esta ruta!\n";
    }

    $GLOBALS['dbConfig'] = require __DIR__ . '/../config/database.php';
    echo "Variables en GLOBALS['dbConfig']:\n";
    echo "HOST: " . $GLOBALS['dbConfig']['host'] . "\n";
    echo "DB: " . $GLOBALS['dbConfig']['database'] . "\n";
    echo "USER: " . $GLOBALS['dbConfig']['username'] . " (len: " . strlen($GLOBALS['dbConfig']['username']) . ")\n";

    $pdo = Database::connection();
    
    echo "\n--- Base de datos conectada ---\n";
    
    echo "\n--- Estructura de tabla 'certificates' ---\n";
    $stmt = $pdo->query("DESCRIBE certificates");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Columna: {$row['Field']} | Tipo: {$row['Type']} | Null: {$row['Null']} | Default: {$row['Default']}\n";
    }
    
    echo "\n--- Datos de la ultima constancia ---\n";
    $last = $pdo->query("SELECT * FROM certificates ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($last) {
        print_r($last);
    } else {
        echo "No hay registros.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
