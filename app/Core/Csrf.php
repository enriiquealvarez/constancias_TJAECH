<?php
namespace app\Core;

class Csrf
{
    public static function token()
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verify($token)
    {
        return hash_equals($_SESSION['_csrf'] ?? '', $token ?? '');
    }
}
