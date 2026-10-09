<?php

$env = static function ($key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
};
$boolEnv = static function ($key, $default = false) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    $value = strtolower(trim($value));
    return in_array($value, ['1', 'true', 'yes', 'on'], true);
};

return [
    'mode' => $env('MAIL_MODE', 'smtp'),
    'host' => $env('MAIL_HOST', 'mail.tjaech.gob.mx'),
    'port' => (int)$env('MAIL_PORT', 465),
    'encryption' => $env('MAIL_ENCRYPTION', 'ssl'),
    // Las credenciales viven en .env (no en el repositorio).
    'username' => $env('MAIL_USERNAME', 'ija@tjaech.gob.mx'),
    'password' => $env('MAIL_PASSWORD', ''),
    'from_email' => $env('MAIL_FROM_EMAIL', 'ija@tjaech.gob.mx'),
    'from_name' => $env('MAIL_FROM_NAME', 'TJAECH Constancias Oficiales'),
    'app_url' => 'http://localhost/constanciasTJAECH',
    'log_path' => __DIR__ . '/../storage/mail.log',
    'log_errors' => true,
];