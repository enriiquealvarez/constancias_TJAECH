<?php
// Front controller
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

spl_autoload_register(function ($class) {
    $path = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';

session_name($appConfig['session_name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => $appConfig['session_secure'],
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$GLOBALS['appConfig'] = $appConfig;
$GLOBALS['dbConfig'] = $dbConfig;

// Helpers
function base_url($path = '')
{
    $base = rtrim($GLOBALS['appConfig']['base_url'], '/');
    return $base . '/' . ltrim($path, '/');
}

\app\Core\App::run();
