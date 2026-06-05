<?php
$cfg = require __DIR__ . '/../config/database.php';

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $cfg['host'], $cfg['database']);
    $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $messages = [];

    // 1. Check/Add cert_text_template to courses
    $stmt = $pdo->query("SHOW COLUMNS FROM courses LIKE 'cert_text_template'");
    if ($stmt->fetch()) {
        $messages[] = "La columna 'cert_text_template' ya existe en la tabla 'courses'.";
    } else {
        $pdo->exec("ALTER TABLE courses ADD COLUMN cert_text_template TEXT NULL;");
        $messages[] = "Columna 'cert_text_template' agregada con éxito a la tabla 'courses'.";
    }

    // 2. Check/Add email_received to certificates
    $stmt = $pdo->query("SHOW COLUMNS FROM certificates LIKE 'email_received'");
    if ($stmt->fetch()) {
        $messages[] = "La columna 'email_received' ya existe en la tabla 'certificates'.";
    } else {
        $pdo->exec("ALTER TABLE certificates ADD COLUMN email_received TINYINT(1) NOT NULL DEFAULT 0;");
        $messages[] = "Columna 'email_received' agregada con éxito a la tabla 'certificates'.";
    }

    // 3. Check/Add email_received_at to certificates
    $stmt = $pdo->query("SHOW COLUMNS FROM certificates LIKE 'email_received_at'");
    if ($stmt->fetch()) {
        $messages[] = "La columna 'email_received_at' ya existe en la tabla 'certificates'.";
    } else {
        $pdo->exec("ALTER TABLE certificates ADD COLUMN email_received_at TIMESTAMP NULL DEFAULT NULL;");
        $messages[] = "Columna 'email_received_at' agregada con éxito a la tabla 'certificates'.";
    }

    echo "<h1>Actualización de Base de Datos - Estado:</h1>";
    echo "<ul>";
    foreach ($messages as $msg) {
        echo "<li>" . htmlspecialchars($msg) . "</li>";
    }
    echo "</ul>";
    echo "<p><strong>¡Base de datos actualizada correctamente!</strong> Ahora puedes volver al panel e intentar editar el curso de nuevo.</p>";
    echo "<p><a href='/admin/courses'>Volver a Cursos</a></p>";

} catch (Exception $e) {
    echo "<h1>Error al actualizar la base de datos</h1>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='/admin/courses'>Volver a Cursos</a></p>";
}
