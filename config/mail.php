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
    'mode' => 'smtp',
    'host' => 'mail.tjaech.gob.mx',
    'port' => 465,
    'encryption' => 'ssl',
    'username' => 'informatica@tjaech.gob.mx',
    'password' => 'UKvYMPp%wumX',
    'from_email' => 'informatica@tjaech.gob.mx',
    'from_name' => 'TJAECH Constancias Oficiales',
    'app_url' => 'http://localhost/constanciasTJAECH',
    'log_path' => __DIR__ . '/../storage/mail.log',
    'log_errors' => true,
];