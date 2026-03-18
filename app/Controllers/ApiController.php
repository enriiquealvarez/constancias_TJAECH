<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Core\Auth;
use app\Core\Csrf;
use app\Core\Database;
use app\Models\Course;
use app\Models\Participant;
use app\Models\Certificate;
use app\Models\AuditLog;
use app\Models\User;

class ApiController extends Controller
{
    public function handle($uri)
    {
        Auth::requireAuth();

        $method = $_SERVER['REQUEST_METHOD'];
        $path = str_replace('/admin/api', '', $uri);
        $path = '/' . ltrim($path, '/');

        if ($method === 'GET' && $path === '/metrics') {
            $this->json(Certificate::metrics());
        }

        if ($path === '/courses') {
            if ($method === 'GET') {
                if (!Auth::can('manage_courses') && !Auth::can('view_courses')) {
                    $this->json(['ok' => false, 'message' => 'No autorizado.'], 403);
                }
                $q = trim($_GET['q'] ?? '');
                $this->json(['ok' => true, 'data' => Course::all($q)]);
            }
            if ($method === 'POST') {
                $this->requireCapability('manage_courses');
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                $this->validateCourse($payload);
                $id = Course::create($payload);
                AuditLog::add($_SESSION['user']['id'], 'CREATE', 'courses', $id);
                $this->json(['ok' => true, 'id' => $id]);
            }
        }

        if (preg_match('#^/courses/(\d+)$#', $path, $m)) {
            $this->requireCapability('manage_courses');
            $id = (int)$m[1];
            if ($method === 'PUT') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                $this->validateCourse($payload);
                Course::update($id, $payload);
                AuditLog::add($_SESSION['user']['id'], 'UPDATE', 'courses', $id);
                $this->json(['ok' => true]);
            }
            if ($method === 'DELETE') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                Course::delete($id);
                AuditLog::add($_SESSION['user']['id'], 'DELETE', 'courses', $id);
                $this->json(['ok' => true]);
            }
        }

        if ($path === '/participants') {
            if ($method === 'GET') {
                if (!Auth::can('manage_participants') && !Auth::can('view_participants')) {
                    $this->json(['ok' => false, 'message' => 'No autorizado.'], 403);
                }
                $q = trim($_GET['q'] ?? '');
                $this->json(['ok' => true, 'data' => Participant::all($q)]);
            }
            if ($method === 'POST') {
                $this->requireCapability('manage_participants');
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                $this->validateParticipant($payload);
                $id = Participant::create($payload);
                AuditLog::add($_SESSION['user']['id'], 'CREATE', 'participants', $id);
                $this->json(['ok' => true, 'id' => $id]);
            }
        }

        if (preg_match('#^/participants/(\d+)$#', $path, $m)) {
            $this->requireCapability('manage_participants');
            $id = (int)$m[1];
            if ($method === 'PUT') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                $this->validateParticipant($payload);
                Participant::update($id, $payload);
                AuditLog::add($_SESSION['user']['id'], 'UPDATE', 'participants', $id);
                $this->json(['ok' => true]);
            }
            if ($method === 'DELETE') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                Participant::delete($id);
                AuditLog::add($_SESSION['user']['id'], 'DELETE', 'participants', $id);
                $this->json(['ok' => true]);
            }
        }

        if ($path === '/certificates') {
            if ($method === 'GET') {
                if (!Auth::can('manage_certificates') && !Auth::can('view_certificates')) {
                    $this->json(['ok' => false, 'message' => 'No autorizado.'], 403);
                }
                $q = trim($_GET['q'] ?? '');
                $this->json(['ok' => true, 'data' => Certificate::all($q)]);
            }
            if ($method === 'POST') {
                $this->requireCapability('manage_certificates');
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                $this->validateCertificate($payload);
                $payload['status'] = $payload['status'] ?? 'NOT_VERIFIED';
                $id = Certificate::create($payload);
                AuditLog::add($_SESSION['user']['id'], 'CREATE', 'certificates', $id);
                $this->json(['ok' => true, 'id' => $id]);
            }
        }

        if ($path === '/certificates/issue' && $method === 'POST') {
            $this->requireCapability('manage_certificates');
            $payload = $this->jsonPayload();
            $this->csrfGuard($payload);
            $this->validateCertificate($payload);
            $id = Certificate::issue($payload);
            $token = Certificate::getToken($id);
            AuditLog::add($_SESSION['user']['id'], 'ISSUE', 'certificates', $id);
            $this->json(['ok' => true, 'id' => $id, 'token' => $token]);
        }

        if (preg_match('#^/certificates/(\d+)$#', $path, $m)) {
            $this->requireCapability('manage_certificates');
            $id = (int)$m[1];
            if ($method === 'PUT') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                $this->validateCertificate($payload);
                Certificate::update($id, $payload);
                AuditLog::add($_SESSION['user']['id'], 'UPDATE', 'certificates', $id);
                $this->json(['ok' => true]);
            }
            if ($method === 'DELETE') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                Certificate::delete($id);
                AuditLog::add($_SESSION['user']['id'], 'DELETE', 'certificates', $id);
                $this->json(['ok' => true]);
            }
        }

        if (preg_match('#^/certificates/status/(\d+)$#', $path, $m) && $method === 'POST') {
            $this->requireCapability('manage_certificates');
            $payload = $this->jsonPayload();
            $this->csrfGuard($payload);
            Certificate::setStatus((int)$m[1], $payload['status'] ?? 'NOT_VERIFIED');
            AuditLog::add($_SESSION['user']['id'], 'STATUS', 'certificates', (int)$m[1]);
            $this->json(['ok' => true]);
        }

        if ($path === '/certificates/export' && $method === 'GET') {
            $this->requireCapability('manage_certificates');
            $this->exportCsv();
        }

        if ($path === '/audit' && $method === 'GET') {
            $this->requireCapability('view_audit');
            $q = trim($_GET['q'] ?? '');
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = max(5, min(50, (int)($_GET['per_page'] ?? 10)));
            $sort = $_GET['sort'] ?? 'created_at';
            $dir = $_GET['dir'] ?? 'desc';
            $filters = [
                'action' => $_GET['action'] ?? '',
                'entity' => $_GET['entity'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
            ];
            $this->json(AuditLog::all($q, $page, $perPage, $sort, $dir, $filters));
        }

        if ($path === '/audit/export' && $method === 'GET') {
            $this->requireCapability('view_audit');
            $filters = [
                'q' => trim($_GET['q'] ?? ''),
                'action' => $_GET['action'] ?? '',
                'entity' => $_GET['entity'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
            ];
            $this->exportAuditCsv($filters);
        }

        if ($path === '/debug/db' && $method === 'GET') {
            $this->json(['ok' => false, 'message' => 'Ruta no encontrada'], 404);
        }

        if ($path === '/users') {
            $this->requireCapability('manage_users');
            if ($method === 'GET') {
                $q = trim($_GET['q'] ?? '');
                $this->json(['ok' => true, 'data' => User::all($q)]);
            }
            if ($method === 'POST') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                $this->validateUser($payload, true);
                $id = User::create($payload);
                AuditLog::add($_SESSION['user']['id'], 'CREATE', 'users', $id);
                $this->json(['ok' => true, 'id' => $id]);
            }
        }

        if (preg_match('#^/users/(\d+)$#', $path, $m)) {
            $this->requireCapability('manage_users');
            $id = (int)$m[1];
            if ($method === 'PUT') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                $this->validateUser($payload, false);
                User::update($id, $payload);
                AuditLog::add($_SESSION['user']['id'], 'UPDATE', 'users', $id);
                $this->json(['ok' => true]);
            }
            if ($method === 'DELETE') {
                $payload = $this->jsonPayload();
                $this->csrfGuard($payload);
                if ((int)$_SESSION['user']['id'] === $id) {
                    $this->json(['ok' => false, 'message' => 'No puedes eliminar tu propio usuario.'], 422);
                }
                if (!empty($payload['reassign_audit'])) {
                    $reassignTo = (int)($_SESSION['user']['id'] ?? 0);
                    if ($reassignTo <= 0 || $reassignTo === $id) {
                        $this->json(['ok' => false, 'message' => 'No se pudo reasignar la auditoria.'], 422);
                    }
                    try {
                        User::deleteWithAuditReassign($id, $reassignTo);
                        AuditLog::add($_SESSION['user']['id'], 'DELETE_FORCE', 'users', $id);
                        $this->json(['ok' => true]);
                    } catch (\Throwable $e) {
                        $this->json(['ok' => false, 'message' => 'No se pudo eliminar el usuario.'], 500);
                    }
                }
                try {
                    User::delete($id);
                    AuditLog::add($_SESSION['user']['id'], 'DELETE', 'users', $id);
                    $this->json(['ok' => true]);
                } catch (\Throwable $e) {
                    $this->json(['ok' => false, 'message' => 'No se puede eliminar: el usuario tiene registros asociados. Deshabilitalo en su lugar.'], 409);
                }
            }
        }

        if (preg_match('#^/users/status/(\d+)$#', $path, $m) && $method === 'POST') {
            $this->requireCapability('manage_users');
            $payload = $this->jsonPayload();
            $this->csrfGuard($payload);
            $id = (int)$m[1];
            if ((int)$_SESSION['user']['id'] === $id) {
                $this->json(['ok' => false, 'message' => 'No puedes deshabilitar tu propio usuario.'], 422);
            }
            $status = $payload['status'] ?? 'ACTIVE';
            User::setStatus($id, $status);
            AuditLog::add($_SESSION['user']['id'], 'STATUS', 'users', $id);
            $this->json(['ok' => true]);
        }

        $this->json(['ok' => false, 'message' => 'Ruta no encontrada'], 404);
    }

    private function jsonPayload()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        return is_array($payload) ? $payload : [];
    }

    private function csrfGuard($payload)
    {
        if (!Csrf::verify($payload['csrf'] ?? '')) {
            $this->json(['ok' => false, 'message' => 'Token CSRF invalido.'], 419);
        }
    }

    private function requireCapability($cap)
    {
        if (!Auth::can($cap)) {
            $this->json(['ok' => false, 'message' => 'No autorizado.'], 403);
        }
    }

    private function validateCourse($payload)
    {
        if (empty(trim($payload['name'] ?? ''))) {
            $this->json(['ok' => false, 'message' => 'El nombre del curso es obligatorio.'], 422);
        }
    }

    private function validateParticipant($payload)
    {
        if (empty(trim($payload['full_name'] ?? ''))) {
            $this->json(['ok' => false, 'message' => 'El nombre completo es obligatorio.'], 422);
        }
        if (!empty($payload['email']) && !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->json(['ok' => false, 'message' => 'Correo invalido.'], 422);
        }
    }

    private function validateCertificate($payload)
    {
        if (empty($payload['participant_id']) || empty($payload['course_id'])) {
            $this->json(['ok' => false, 'message' => 'Participante y curso son obligatorios.'], 422);
        }
    }

    private function validateUser($payload, $isCreate)
    {
        if (empty(trim($payload['name'] ?? ''))) {
            $this->json(['ok' => false, 'message' => 'El nombre es obligatorio.'], 422);
        }
        if (!filter_var($payload['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $this->json(['ok' => false, 'message' => 'Correo invalido.'], 422);
        }
        if ($isCreate && empty($payload['password'])) {
            $this->json(['ok' => false, 'message' => 'La contrasena es obligatoria.'], 422);
        }
        $allowed = ['ADMIN', 'COURSES', 'PARTICIPANTS', 'CERTIFICATES', 'READONLY'];
        if (empty($payload['roles']) || !is_array($payload['roles'])) {
            $this->json(['ok' => false, 'message' => 'Selecciona al menos un rol.'], 422);
        }
        foreach ($payload['roles'] as $role) {
            if (!in_array($role, $allowed, true)) {
                $this->json(['ok' => false, 'message' => 'Rol invalido.'], 422);
            }
        }
        $allowedStatus = ['ACTIVE', 'DISABLED'];
        if (!empty($payload['status']) && !in_array($payload['status'], $allowedStatus, true)) {
            $this->json(['ok' => false, 'message' => 'Estatus invalido.'], 422);
        }
    }

    private function exportCsv()
    {
        $rows = Certificate::exportRows();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="constancias.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Participante', 'Curso', 'Tipo', 'Estatus', 'Token', 'Creado']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['id'], $r['participant'], $r['course'], $r['doc_type'], $r['status'], $r['token'], $r['created_at']]);
        }
        fclose($out);
        exit;
    }

    private function exportAuditCsv($filters = [])
    {
        $rows = AuditLog::exportRows($filters);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="auditoria.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Usuario', 'Accion', 'Entidad', 'Entidad ID', 'IP', 'Fecha']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['id'], $r['user_name'], $r['action'], $r['entity'], $r['entity_id'], $r['ip'], $r['created_at']]);
        }
        fclose($out);
        exit;
    }

    private function debugDb()
    {
        $pdo = Database::connection();
        $cfg = $GLOBALS['dbConfig'] ?? [];
        $meta = [];
        $count = [];
        $auth = [
            'user' => null,
            'current_user' => null,
            'grants' => [],
            'error' => null,
        ];
        $qualified = [
            'db_exists' => null,
            'participants_total' => null,
            'error' => null,
        ];
        try {
            $metaStmt = $pdo->query('SELECT DATABASE() AS db, @@hostname AS host, @@port AS port');
            $meta = $metaStmt->fetch() ?: [];
        } catch (\Throwable $e) {
            $meta = ['error' => $e->getMessage()];
        }
        try {
            $countStmt = $pdo->query('SELECT COUNT(*) AS total FROM participants');
            $count = $countStmt->fetch() ?: [];
        } catch (\Throwable $e) {
            $count = ['error' => $e->getMessage()];
        }
        try {
            $authStmt = $pdo->query('SELECT USER() AS user');
            $authRow = $authStmt->fetch() ?: [];
            $auth['user'] = $authRow['user'] ?? null;
            $grantsStmt = $pdo->query('SHOW GRANTS');
            $auth['grants'] = $grantsStmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Throwable $e) {
            $auth['error'] = $e->getMessage();
        }
        if (!empty($cfg['database'])) {
            try {
                $dbName = preg_replace('/[^A-Za-z0-9_]+/', '', $cfg['database']);
                $existsStmt = $pdo->query("SHOW DATABASES LIKE " . $pdo->quote($dbName));
                $qualified['db_exists'] = (bool)$existsStmt->fetch();
                $qStmt = $pdo->query("SELECT COUNT(*) AS total FROM `{$dbName}`.`participants`");
                $qCount = $qStmt->fetch();
                $qualified['participants_total'] = isset($qCount['total']) ? (int)$qCount['total'] : null;
            } catch (\Throwable $e) {
                $qualified['error'] = $e->getMessage();
            }
        }
        $this->json([
            'ok' => true,
            'config' => [
                'host' => $cfg['host'] ?? null,
                'database' => $cfg['database'] ?? null,
                'username' => $cfg['username'] ?? null,
            ],
            'db' => $meta['db'] ?? null,
            'host' => $meta['host'] ?? null,
            'port' => $meta['port'] ?? null,
            'connection' => $pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS),
            'participants_total' => isset($count['total']) ? (int)$count['total'] : null,
            'participants_error' => $count['error'] ?? null,
            'auth' => $auth,
            'qualified' => $qualified,
        ]);
    }
}
