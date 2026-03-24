<?php
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

return [
    'base_url' => getenv('APP_URL') ?: ($scheme . '://' . $host),
    'base_path' => '',
    'session_name' => 'tja_session',
    'session_secure' => getenv('SESSION_SECURE') !== false ? filter_var(getenv('SESSION_SECURE'), FILTER_VALIDATE_BOOLEAN) : ($scheme === 'https'),
    'webhook_secret' => getenv('WEBHOOK_SECRET') ?: 'TJA_SECRET_KEY_998877665544332211',
];

