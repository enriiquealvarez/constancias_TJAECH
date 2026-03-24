<?php
$cfg = require __DIR__ . '/../config/database.php';

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $cfg['host'], $cfg['database']);
    $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("ALTER TABLE participants MODIFY COLUMN type ENUM('INTERNAL', 'EXTERNAL', 'PONENTE') NOT NULL DEFAULT 'INTERNAL'");
    
    echo "<h1>¡Base de datos actualizada con exito!</h1>";
    echo "<p>El permiso para PONENTE se ha agregado correctamente. Por favor, vuelve al panel y guarda tu registro.</p>";
    echo "<a href='/admin/participants'>Volver al panel</a>";
} catch (Exception $e) {
    echo "<h1>Error al actualizar</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
