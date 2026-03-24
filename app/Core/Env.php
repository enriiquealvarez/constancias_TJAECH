<?php
namespace app\Core;

class Env {
    public static function load(string $path): void {
        if (!file_exists($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            if (strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $key = ltrim($key, "\xEF\xBB\xBF");
            if ($key === '') {
                continue;
            }
            $value = trim($value, " \t\n\r\0\x0B\"");
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    public static function get(string $key, $default = null) {
        $val = getenv($key);
        return $val !== false ? $val : ($_ENV[$key] ?? $default);
    }
}
