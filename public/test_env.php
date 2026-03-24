<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Diagnosticando Error 500...</h3>";

$envPath = __DIR__ . '/../app/Core/Env.php';
if (file_exists($envPath)) {
    echo "✅ Archivo Env.php encontrado.<br>";
    require_once $envPath;
} else {
    echo "❌ Archivo Env.php NO encontrado en: $envPath<br>";
    exit;
}

$dotEnvPath = __DIR__ . '/../.env';
if (file_exists($dotEnvPath)) {
    echo "✅ Archivo .env encontrado.<br>";
    try {
        \app\Core\Env::load($dotEnvPath);
        echo "✅ Carga de .env exitosa.<br>";
    } catch (\Throwable $e) {
        echo "❌ Error cargando .env: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Archivo .env NO encontrado en: $dotEnvPath<br>";
}

echo "<h4>Verificando Variables de Entorno:</h4>";
echo "DB_HOST: " . (\app\Core\Env::get('DB_HOST') ?: 'NO DEFINIDO') . "<br>";
echo "DB_DATABASE: " . (\app\Core\Env::get('DB_DATABASE') ?: 'NO DEFINIDO') . "<br>";
echo "DB_USERNAME: " . (\app\Core\Env::get('DB_USERNAME') ?: 'NO DEFINIDO') . "<br>";
$pass = \app\Core\Env::get('DB_PASSWORD') ?: '';
echo "DB_PASSWORD_LENGTH: " . strlen($pass) . "<br>";
if (strlen($pass) > 0) {
    echo "DB_PASSWORD_START: " . $pass[0] . "<br>";
    echo "DB_PASSWORD_END: " . substr($pass, -1) . "<br>";
}

echo "<h4>Probando Conexión a Base de Datos:</h4>";
try {
    $dbConfig = require __DIR__ . '/../config/database.php';
    echo "✅ config/database.php cargado.<br>";
    $host = $dbConfig['host'];
    $user = $dbConfig['username'];
    $pass = $dbConfig['password'];
    $db = $dbConfig['database'];
    
    echo "<h4>Probando con PDO:</h4>";
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $db);
    $pdo = new \PDO($dsn, $user, $pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    echo "✅ PDO: Conexión EXITOSA.<br>";
    
    $evalDb = $dbConfig['eval_database'] ?? '';
    if ($evalDb) {
        try {
            $pdo->query("SELECT 1 FROM `{$evalDb}`.cursos LIMIT 1");
            echo "✅ Acceso a base de datos de Evaluaciones EXITOSA.<br>";
        } catch (\Throwable $e) {
            echo "❌ Error accediendo a Evaluaciones: " . $e->getMessage() . "<br>";
        }
    }

    echo "<h4>Probando con MySQLi:</h4>";
    $mysqli = new \mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_error) {
        echo "❌ MySQLi: Error (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "<br>";
    } else {
        echo "✅ MySQLi: Conexión EXITOSA.<br>";
        $mysqli->close();
    }
} catch (\Throwable $e) {
    echo "❌ Error de Conexión: " . $e->getMessage() . "<br>";
}
