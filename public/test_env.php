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
    echo "<h4>3. Verificando Webhook (Seguridad):</h4>";
    $webhookSecret = \app\Core\Env::get('WEBHOOK_SECRET') ? 'CONFIGURADO ✅' : 'NO CONFIGURADO ❌';
    echo "WEBHOOK_SECRET: $webhookSecret <br>";

    echo "<h4>4. Prueba de Correo:</h4>";
    if (isset($_GET['test_mail'])) {
        try {
            require_once __DIR__ . '/../app/Core/Mailer.php';
            $testEmail = $_GET['test_mail'];
            \app\Core\Mailer::send($testEmail, "Prueba de Constancias", "<h1>Prueba Exitosa</h1><p>Si recibes esto, el SMTP en producción funciona.</p>", "Prueba Exitosa");
            echo "✅ Correo de prueba enviado a $testEmail. Revisa tu bandeja de entrada.";
        } catch (\Throwable $e) {
            echo "❌ Error enviando correo: " . $e->getMessage();
        }
    } else {
        echo "<a href='?test_mail=tu_correo@ejemplo.com' style='padding: 10px; background: #1b3f66; color: white; text-decoration: none; border-radius: 5px;'>Probar Envío de Correo</a> (Cambia el correo en la URL)<br>";
    }

} catch (\Throwable $e) {
    echo "❌ Error en diagnóstico: " . $e->getMessage() . "<br>";
}
