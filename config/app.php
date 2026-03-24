<?php
use app\Core\Env;
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

return [
    'base_url' => Env::get('APP_URL', $scheme . '://' . $host),
    'base_path' => Env::get('APP_BASE_PATH', ''),
    'session_name' => Env::get('SESSION_NAME', 'tja_session'),
    'session_secure' => Env::get('SESSION_SECURE') !== null ? filter_var(Env::get('SESSION_SECURE'), FILTER_VALIDATE_BOOLEAN) : ($scheme === 'https'),
    'webhook_secret' => Env::get('WEBHOOK_SECRET', 'TJA_SECRET_KEY_998877665544332211'),
];

