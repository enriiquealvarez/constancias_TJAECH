<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Core/Env.php';
app\Core\Env::load(__DIR__ . '/../.env');

require_once __DIR__ . '/../app/Core/Database.php';
$GLOBALS['dbConfig'] = require __DIR__ . '/../config/database.php';

use app\Core\Database;

header('Content-Type: text/plain');

try {
    $pdo = Database::connection();
    echo "--- Reparación de Base de Datos ---\n";
    
    // 1. Modificar el ENUM para incluir PENDING_REVIEW
    // NOTA: Usamos MODIFY COLUMN con la definicion completa
    echo "Actualizando estructura de tabla 'certificates'...\n";
    $pdo->exec("ALTER TABLE certificates MODIFY COLUMN status ENUM('VERIFIED', 'NOT_VERIFIED', 'PENDING_REVIEW') NOT NULL DEFAULT 'PENDING_REVIEW'");
    echo "Estructura actualizada.\n";
    
    // 2. Corregir registros con estatus vacío o inválido
    echo "Reparando registros con estatus vacío...\n";
    $count = $pdo->exec("UPDATE certificates SET status = 'PENDING_REVIEW' WHERE status = '' OR status IS NULL");
    echo "Registros reparados: $count\n";
    
    echo "\n--- PROCESO COMPLETADO CON EXITO ---\n";
    echo "Ya puedes borrar este archivo y el de debug_db.php";

} catch (Exception $e) {
    echo "ERROR CRITICO: " . $e->getMessage();
}
