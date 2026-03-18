<?php
namespace app\Models;

use app\Core\Database;

class AuditLog
{
    public static function all($q = '', $page = 1, $perPage = 10, $sort = 'created_at', $dir = 'desc', $filters = [])
    {
        $pdo = Database::connection();
        $sortMap = [
            'created_at' => 'a.created_at',
            'action' => 'a.action',
            'entity' => 'a.entity',
            'user_name' => 'user_name',
        ];
        $sort = $sortMap[$sort] ?? $sortMap['created_at'];
        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];

        if ($q) {
            $where[] = '(a.action LIKE ? OR a.entity LIKE ? OR u.name LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['action'])) {
            $where[] = 'a.action = ?';
            $params[] = $filters['action'];
        }
        if (!empty($filters['entity'])) {
            $where[] = 'a.entity = ?';
            $params[] = $filters['entity'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'a.created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'a.created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $count = $pdo->prepare("SELECT COUNT(*) AS c FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id {$whereSql}");
        $count->execute($params);
        $total = (int)($count->fetch()['c'] ?? 0);
        $stmt = $pdo->prepare("SELECT a.*, COALESCE(u.name, 'Usuario eliminado') AS user_name FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id {$whereSql} ORDER BY {$sort} {$dir} LIMIT ? OFFSET ?");
        $bindIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($bindIndex, $param, \PDO::PARAM_STR);
            $bindIndex++;
        }
        $stmt->bindValue($bindIndex++, (int)$perPage, \PDO::PARAM_INT);
        $stmt->bindValue($bindIndex, (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();
        return [
            'ok' => true,
            'data' => $stmt->fetchAll(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'pages' => $perPage > 0 ? (int)ceil($total / $perPage) : 1,
            ],
        ];
    }

    public static function add($userId, $action, $entity, $entityId)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, entity, entity_id, ip, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $userId,
            $action,
            $entity,
            $entityId,
            $_SERVER['REMOTE_ADDR'] ?? 'CLI',
        ]);
    }

    public static function exportRows($filters = [])
    {
        $pdo = Database::connection();
        $where = [];
        $params = [];
        if (!empty($filters['q'])) {
            $where[] = '(a.action LIKE ? OR a.entity LIKE ? OR u.name LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['action'])) {
            $where[] = 'a.action = ?';
            $params[] = $filters['action'];
        }
        if (!empty($filters['entity'])) {
            $where[] = 'a.entity = ?';
            $params[] = $filters['entity'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'a.created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'a.created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $pdo->prepare("SELECT a.id, COALESCE(u.name, 'Usuario eliminado') AS user_name, a.action, a.entity, a.entity_id, a.ip, a.created_at FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id {$whereSql} ORDER BY a.id DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
