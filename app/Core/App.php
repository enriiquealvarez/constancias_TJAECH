<?php
namespace app\Core;

class App
{
    public static function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = rtrim($GLOBALS['appConfig']['base_path'], '/');
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . ltrim($uri, '/');

        if ($uri === '/' || $uri === '/inicio') {
            (new \app\Controllers\PublicController())->home();
            return;
        }

        if ($uri === '/verificar') {
            (new \app\Controllers\PublicController())->verifyForm();
            return;
        }

        if (preg_match('#^/c/([A-Za-z0-9_-]+)$#', $uri, $m)) {
            (new \app\Controllers\PublicController())->show($m[1]);
            return;
        }

        if ($uri === '/c' && !empty($_GET['token'])) {
            (new \app\Controllers\PublicController())->show($_GET['token']);
            return;
        }

        if ($uri === '/admin/login') {
            (new \app\Controllers\AuthController())->login();
            return;
        }

        if ($uri === '/admin/forgot') {
            (new \app\Controllers\AuthController())->forgot();
            return;
        }

        if ($uri === '/admin/reset') {
            (new \app\Controllers\AuthController())->reset();
            return;
        }

        if ($uri === '/admin/logout') {
            (new \app\Controllers\AuthController())->logout();
            return;
        }

        if (strpos($uri, '/admin/api') === 0) {
            (new \app\Controllers\ApiController())->handle($uri);
            return;
        }

        if ($uri === '/admin' || $uri === '/admin/dashboard') {
            (new \app\Controllers\AdminController())->dashboard();
            return;
        }

        if ($uri === '/admin/courses') {
            (new \app\Controllers\AdminController())->courses();
            return;
        }

        if ($uri === '/admin/participants') {
            (new \app\Controllers\AdminController())->participants();
            return;
        }

        if ($uri === '/admin/certificates') {
            (new \app\Controllers\AdminController())->certificates();
            return;
        }

        if ($uri === '/admin/audit') {
            (new \app\Controllers\AdminController())->audit();
            return;
        }

        if ($uri === '/admin/users') {
            (new \app\Controllers\AdminController())->users();
            return;
        }

        http_response_code(404);
        (new \app\Controllers\PublicController())->notFound();
    }
}
