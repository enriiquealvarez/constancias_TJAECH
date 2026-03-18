<?php
namespace app\Core;

class Auth
{
    private static $roleMap = [
        'ADMIN' => ['*'],
        'COURSES' => ['manage_courses', 'view_courses'],
        'PARTICIPANTS' => ['manage_participants', 'view_participants'],
        'CERTIFICATES' => ['manage_certificates', 'view_certificates'],
        'READONLY' => ['view_courses', 'view_participants', 'view_certificates'],
    ];

    public static function check()
    {
        return !empty($_SESSION['user']);
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function roles()
    {
        return $_SESSION['user']['roles'] ?? [];
    }

    public static function can($capability)
    {
        $roles = self::roles();
        if (!$roles) {
            return false;
        }
        foreach ($roles as $role) {
            if (!isset(self::$roleMap[$role])) {
                continue;
            }
            $caps = self::$roleMap[$role];
            if (in_array('*', $caps, true) || in_array($capability, $caps, true)) {
                return true;
            }
        }
        return false;
    }

    public static function requireAuth()
    {
        if (!self::check()) {
            header('Location: ' . base_url('admin/login'));
            exit;
        }
    }
}
