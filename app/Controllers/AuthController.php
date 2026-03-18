<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Core\Csrf;
use app\Core\Mailer;
use app\Models\User;
use app\Models\AuditLog;
use app\Models\PasswordReset;

class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = json_decode(file_get_contents('php://input'), true);
            $email = trim($payload['email'] ?? '');
            $password = $payload['password'] ?? '';
            $csrf = $payload['csrf'] ?? '';

            if (!Csrf::verify($csrf)) {
                $this->json(['ok' => false, 'message' => 'Token CSRF invalido.'], 419);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
                $this->json(['ok' => false, 'message' => 'Credenciales incompletas.'], 422);
            }

            $user = User::findByEmail($email);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $this->json(['ok' => false, 'message' => 'Credenciales incorrectas.'], 401);
            }
            if (($user['status'] ?? 'ACTIVE') !== 'ACTIVE') {
                $this->json(['ok' => false, 'message' => 'Usuario deshabilitado.'], 403);
            }

            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'roles' => [],
            ];
            $roles = User::rolesByUser($user['id']);
            if (!$roles && !empty($user['role'])) {
                $roles = [$user['role']];
            }
            $_SESSION['user']['roles'] = $roles;
            AuditLog::add($user['id'], 'LOGIN', 'users', $user['id']);

            $this->json(['ok' => true, 'redirect' => base_url('admin')]);
        }

        $this->render('admin/login', [
            'title' => 'Acceso administrativo',
            'csrf' => Csrf::token(),
        ], 'public');
    }

    public function logout()
    {
        if (!empty($_SESSION['user']['id'])) {
            AuditLog::add($_SESSION['user']['id'], 'LOGOUT', 'users', $_SESSION['user']['id']);
        }
        session_destroy();
        header('Location: ' . base_url('admin/login'));
        exit;
    }

    public function forgot()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = json_decode(file_get_contents('php://input'), true);
            $email = trim($payload['email'] ?? '');
            $csrf = $payload['csrf'] ?? '';

            if (!Csrf::verify($csrf)) {
                $this->json(['ok' => false, 'message' => 'Token CSRF invalido.'], 419);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->json(['ok' => false, 'message' => 'Correo invalido.'], 422);
            }

            $user = User::findByEmail($email);
            if ($user && ($user['status'] ?? 'ACTIVE') === 'ACTIVE') {
                $token = (string)random_int(100000, 999999);
                PasswordReset::create($user['id'], $token, 15);
                try {
                    $this->sendResetEmail($user, $token);
                } catch (\Throwable $e) {
                    $cfg = require __DIR__ . '/../../config/mail.php';
                    if (!empty($cfg['log_errors'])) {
                        $path = $cfg['log_path'] ?? __DIR__ . '/../../storage/mail.log';
                        $line = '[' . date('Y-m-d H:i:s') . '] SMTP error: ' . $e->getMessage() . PHP_EOL;
                        @file_put_contents($path, $line, FILE_APPEND);
                    }
                    $this->json(['ok' => false, 'message' => 'No se pudo enviar el correo. Verifica la configuracion SMTP.'], 500);
                }
                AuditLog::add($user['id'], 'RESET_REQUEST', 'users', $user['id']);
                $cfg = require __DIR__ . '/../../config/mail.php';
                if (($cfg['mode'] ?? 'smtp') === 'log') {
                    $this->json(['ok' => true, 'message' => 'Codigo de prueba (modo local): ' . $token]);
                }
            }

            $this->json(['ok' => true, 'message' => 'Si el correo existe, se envio un codigo de verificacion.']);
        }

        $this->render('admin/forgot', [
            'title' => 'Recuperar acceso',
            'csrf' => Csrf::token(),
        ], 'public');
    }

    public function reset()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = json_decode(file_get_contents('php://input'), true);
            $email = trim($payload['email'] ?? '');
            $token = trim($payload['token'] ?? '');
            $password = $payload['password'] ?? '';
            $confirm = $payload['confirm'] ?? '';
            $csrf = $payload['csrf'] ?? '';

            if (!Csrf::verify($csrf)) {
                $this->json(['ok' => false, 'message' => 'Token CSRF invalido.'], 419);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->json(['ok' => false, 'message' => 'Correo invalido.'], 422);
            }
            if (strlen($token) !== 6) {
                $this->json(['ok' => false, 'message' => 'Codigo invalido.'], 422);
            }
            if (strlen($password) < 8 || $password !== $confirm) {
                $this->json(['ok' => false, 'message' => 'La contrasena no es valida.'], 422);
            }

            $user = User::findByEmail($email);
            if (!$user) {
                $this->json(['ok' => false, 'message' => 'No se encontro el usuario.'], 404);
            }
            $reset = PasswordReset::verify($user['id'], $token);
            if (!$reset) {
                $this->json(['ok' => false, 'message' => 'Codigo expirado o invalido.'], 422);
            }
            User::updatePassword($user['id'], $password);
            PasswordReset::markUsed($reset['id']);
            AuditLog::add($user['id'], 'RESET_PASSWORD', 'users', $user['id']);
            $this->json(['ok' => true, 'redirect' => base_url('admin/login')]);
        }

        $this->render('admin/reset', [
            'title' => 'Restablecer contrasena',
            'csrf' => Csrf::token(),
        ], 'public');
    }

private function sendResetEmail($user, $token)
{
    $cfg = require __DIR__ . '/../../config/mail.php';
    $appUrl = rtrim($cfg['app_url'] ?? '', '/');

    // URLs de logos (si app_url no está definido, se omiten)
    $logoTja = $appUrl ? $appUrl . '/assets/img/logo-tja.png' : '';
    $logoJh  = $appUrl ? $appUrl . '/assets/img/justicia-humanismo.png' : '';

    // Identidad
    $tribunal = 'Tribunal de Justicia Administrativa del Estado de Chiapas';
    $sistema  = 'Sistema de Verificación de Constancias';

    // Mensajes
    $subject = 'Recuperación de acceso – ' . $sistema;
    $name = trim((string)($user['name'] ?? ''));
    $saludo = $name !== '' ? ('Hola ' . $name . ',') : 'Hola,';

    // Preheader (texto que se ve en el preview del correo)
    $preheader = 'Tu código de verificación es ' . $token . '. Vigencia: 15 minutos.';

    // Colores (inline para correos)
    $cNavy   = '#111426';
    $cBlue   = '#1B446E';
    $cGold   = '#C4AF6B';
    $cMag    = '#C71F69';
    $bg      = '#FFFFFF';
    $muted   = '#1B446E';
    $border  = '#C4AF6B';

        // IMPORTANTE: usa tablas y lineas cortas para compatibilidad SMTP
        $lines = [];
        $lines[] = '<!doctype html>';
        $lines[] = '<html>';
        $lines[] = '<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>';
        $lines[] = '<body style="margin:0; padding:0; background:' . $bg . '; font-family:Arial, Helvetica, sans-serif; color:' . $cNavy . ';">';
        $lines[] = '<div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">' . htmlspecialchars($preheader) . '</div>';
        $lines[] = '<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:' . $bg . '; padding:24px 0;">';
        $lines[] = '<tr><td align="center">';
        $lines[] = '<table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width:600px; width:100%; background:#ffffff; border:1px solid ' . $border . '; border-radius:16px; overflow:hidden;">';
        $lines[] = '<tr><td style="background:' . $cBlue . '; padding:18px 24px; border-bottom:4px solid ' . $cGold . ';">';
        $lines[] = '<table role="presentation" cellpadding="0" cellspacing="0" width="100%"><tr>';
        $lines[] = '<td align="left" style="vertical-align:middle;">';
        if ($logoTja) {
            $lines[] = '<img src="' . htmlspecialchars($logoTja) . '" alt="TJA Chiapas" style="height:54px; width:auto; display:block;">';
        } else {
            $lines[] = '<div style="color:#ffffff; font-weight:bold; font-size:16px;">TJA Chiapas</div>';
        }
        $lines[] = '</td>';
        $lines[] = '<td align="right" style="vertical-align:middle; color:#ffffff; font-size:12px; line-height:1.3;">';
        $lines[] = '<div style="font-weight:bold;">' . htmlspecialchars($sistema) . '</div>';
        $lines[] = '<div style="opacity:.95;">' . htmlspecialchars($tribunal) . '</div>';
        $lines[] = '</td>';
        $lines[] = '</tr></table>';
        $lines[] = '</td></tr>';
        $lines[] = '<tr><td style="padding:24px;">';
        $lines[] = '<h1 style="margin:0 0 10px; font-size:20px; color:' . $cNavy . ';">Recuperación de acceso</h1>';
        $lines[] = '<p style="margin:0 0 12px; color:' . $muted . '; font-size:14px; line-height:1.6;">' . htmlspecialchars($saludo) . '</p>';
        $lines[] = '<p style="margin:0 0 18px; color:' . $muted . '; font-size:14px; line-height:1.6;">';
        $lines[] = 'Se recibió una solicitud para restablecer la contraseña de tu cuenta en el <strong>' . htmlspecialchars($sistema) . '</strong>.';
        $lines[] = 'Para continuar, ingresa el siguiente <strong>código de verificación</strong>:';
        $lines[] = '</p>';
        $lines[] = '<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:18px 0;">';
        $lines[] = '<tr><td align="center">';
        $lines[] = '<div style="display:inline-block; background:' . $bg . '; border:1px dashed ' . $cGold . '; border-radius:14px; padding:14px 20px;">';
        $lines[] = '<div style="font-size:28px; font-weight:bold; letter-spacing:6px; color:' . $cNavy . ';">' . htmlspecialchars($token) . '</div>';
        $lines[] = '</div>';
        $lines[] = '</td></tr></table>';
        $lines[] = '<p style="margin:0 0 6px; color:' . $muted . '; font-size:13px; line-height:1.6;">Vigencia del código: <strong>15 minutos</strong>.</p>';
        $lines[] = '<p style="margin:0; color:' . $muted . '; font-size:13px; line-height:1.6;">';
        $lines[] = 'Si tú no solicitaste este cambio, puedes ignorar este mensaje. Por seguridad, no compartas este código con nadie.';
        $lines[] = '</p>';
        $lines[] = '</td></tr>';
        $lines[] = '<tr><td style="padding:18px 24px; border-top:1px solid ' . $border . '; background:#ffffff;">';
        $lines[] = '<table role="presentation" cellpadding="0" cellspacing="0" width="100%"><tr>';
        $lines[] = '<td align="left" style="vertical-align:middle;">';
        $lines[] = '<div style="font-size:12px; color:' . $muted . '; line-height:1.5;">';
        $lines[] = '<strong style="color:' . $cNavy . ';">Área de Informática</strong><br>';
        $lines[] = htmlspecialchars($tribunal) . '<br>';
        $lines[] = '<a href="mailto:informatica@tjaechg.gob.mx" style="color:' . $cBlue . '; text-decoration:none;">informatica@tjaechg.gob.mx</a>';
        if ($appUrl) {
            $lines[] = '&nbsp;|&nbsp; <a href="' . htmlspecialchars($appUrl) . '" style="color:' . $cBlue . '; text-decoration:none;">' . htmlspecialchars(parse_url($appUrl, PHP_URL_HOST) ?: $appUrl) . '</a>';
        }
        $lines[] = '</div>';
        $lines[] = '</td>';
        $lines[] = '<td align="right" style="vertical-align:middle;">';
        if ($logoJh) {
            $lines[] = '<img src="' . htmlspecialchars($logoJh) . '" alt="Justicia con Humanismo" style="height:44px; width:auto; display:block;">';
        } else {
            $lines[] = '<div style="font-size:12px; color:' . $cMag . '; font-style:italic;">Justicia con Humanismo</div>';
        }
        $lines[] = '</td>';
        $lines[] = '</tr></table>';
        $lines[] = '<div style="margin-top:12px; font-size:11px; color:' . $muted . '; line-height:1.5;">';
        $lines[] = 'Este correo fue generado automáticamente; por favor, no respondas a este mensaje.';
        $lines[] = '</div>';
        $lines[] = '</td></tr>';
        $lines[] = '</table>';
        $lines[] = '</td></tr></table>';
        $lines[] = '</body></html>';
        $html = implode("\r\n", $lines);

    $text = "Recuperación de acceso - {$sistema}\n\n"
          . "{$saludo}\n"
          . "Tu código de verificación es: {$token}\n"
          . "Vigencia: 15 minutos.\n"
          . "Si no solicitaste este cambio, ignora este mensaje.\n\n"
          . "Área de Informática – {$tribunal}\n"
          . "informatica@tjaechg.gob.mx\n";

    if (!Mailer::send($user['email'], $subject, $html, $text)) {
        throw new \RuntimeException('No se pudo enviar el correo');
    }
}

}
