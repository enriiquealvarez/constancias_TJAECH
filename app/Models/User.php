<?php
namespace app\Models;

use app\Core\Database;

class User
{
    public static function all($q = '')
    {
        $pdo = Database::connection();
        if ($q) {
            $stmt = $pdo->prepare('SELECT u.id, u.name, u.email, u.status, u.created_at, u.role, GROUP_CONCAT(ur.role) AS roles FROM users u LEFT JOIN user_roles ur ON ur.user_id = u.id WHERE u.name LIKE ? OR u.email LIKE ? GROUP BY u.id ORDER BY u.id DESC');
            $like = '%' . $q . '%';
            $stmt->execute([$like, $like]);
        } else {
            $stmt = $pdo->query('SELECT u.id, u.name, u.email, u.status, u.created_at, u.role, GROUP_CONCAT(ur.role) AS roles FROM users u LEFT JOIN user_roles ur ON ur.user_id = u.id GROUP BY u.id ORDER BY u.id DESC');
        }
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            if ($row['roles']) {
                $row['roles'] = explode(',', $row['roles']);
            } else {
                $row['roles'] = !empty($row['role']) ? [$row['role']] : [];
            }
        }
        return $rows;
    }

    public static function findByEmail($email)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $pdo = Database::connection();
        $primaryRole = $data['roles'][0] ?? 'ADMIN';
        $stmt = $pdo->prepare('INSERT INTO users (name, email, role, status, password_hash) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            trim($data['name'] ?? ''),
            trim($data['email'] ?? ''),
            $primaryRole,
            $data['status'] ?? 'ACTIVE',
            password_hash($data['password'], PASSWORD_DEFAULT),
        ]);
        $id = (int)$pdo->lastInsertId();
        self::setRoles($id, $data['roles'] ?? []);
        return $id;
    }

    public static function update($id, $data)
    {
        $pdo = Database::connection();
        $primaryRole = $data['roles'][0] ?? 'ADMIN';
        if (!empty($data['password'])) {
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ?, status = ?, password_hash = ? WHERE id = ?');
            $stmt->execute([
                trim($data['name'] ?? ''),
                trim($data['email'] ?? ''),
                $primaryRole,
                $data['status'] ?? 'ACTIVE',
                password_hash($data['password'], PASSWORD_DEFAULT),
                $id,
            ]);
            self::setRoles($id, $data['roles'] ?? []);
            return;
        }
        $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?');
        $stmt->execute([
            trim($data['name'] ?? ''),
            trim($data['email'] ?? ''),
            $primaryRole,
            $data['status'] ?? 'ACTIVE',
            $id,
        ]);
        self::setRoles($id, $data['roles'] ?? []);
    }

    public static function setStatus($id, $status)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE users SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public static function delete($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function deleteWithAuditReassign($id, $reassignTo)
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE audit_logs SET user_id = ? WHERE user_id = ?')->execute([$reassignTo, $id]);
            $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function updatePassword($id, $password)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public static function setRoles($id, $roles)
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM user_roles WHERE user_id = ?')->execute([$id]);
        $stmt = $pdo->prepare('INSERT INTO user_roles (user_id, role) VALUES (?, ?)');
        foreach ($roles as $role) {
            $stmt->execute([$id, $role]);
        }
        $pdo->commit();
    }

    public static function rolesByUser($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT role FROM user_roles WHERE user_id = ?');
        $stmt->execute([$id]);
        return array_column($stmt->fetchAll(), 'role');
    }
}
