<?php
$cfg = require __DIR__ . '/../config/database.php';

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $cfg['host'], $cfg['database']);
    $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Check if column exists first
    $stmt = $pdo->query("SHOW COLUMNS FROM courses LIKE 'speaker_background_image'");
    if ($stmt->fetch()) {
        echo "<h1>La columna ya existe.</h1>";
    } else {
        $pdo->exec("ALTER TABLE courses ADD COLUMN speaker_background_image VARCHAR(255) NULL AFTER background_image");
        echo "<h1>¡Base de datos de Cursos actualizada con éxito!</h1>";
        echo "<p>La columna de la plantilla de reconocimiento se ha agregado correctamente. Por favor, vuelve al panel y guarda el curso nuevamente.</p>";
    }
    
    echo "<a href='/admin/courses'>Volver a Cursos</a>";
} catch (Exception $e) {
    echo "<h1>Error al actualizar</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
