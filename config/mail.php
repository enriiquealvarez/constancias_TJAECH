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
    'mode' => $env('MAIL_MODE', 'log'),
    'host' => $env('MAIL_HOST', 'mail.tjaech.gob.mx'),
    'port' => (int)$env('MAIL_PORT', 465),
    'encryption' => $env('MAIL_ENCRYPTION', 'ssl'),
    'username' => $env('MAIL_USERNAME', 'informatica@tjaech.gob.mx'),
    'password' => $env('MAIL_PASSWORD', ''),
    'from_email' => $env('MAIL_FROM_EMAIL', 'informatica@tjaech.gob.mx'),
    'from_name' => $env('MAIL_FROM_NAME', 'Informatica - Soporte del Area de Informatica'),
    'app_url' => $env('APP_URL', 'http://localhost/constanciasTJAECH'),
    'log_path' => $env('MAIL_LOG_PATH', __DIR__ . '/../storage/mail.log'),
    'log_errors' => $boolEnv('MAIL_LOG_ERRORS', false),
];