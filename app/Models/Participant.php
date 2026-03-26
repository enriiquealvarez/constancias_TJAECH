<?php
namespace app\Models;

use app\Core\Database;

class Participant
{
    public static function all($q = '')
    {
        $pdo = Database::connection();
        if ($q) {
            $stmt = $pdo->prepare("SELECT * FROM participants WHERE full_name LIKE ? OR email LIKE ? ORDER BY id DESC");
            $like = '%' . $q . '%';
            $stmt->execute([$like, $like]);
        } else {
            $stmt = $pdo->query('SELECT * FROM participants ORDER BY id DESC');
        }
        return $stmt->fetchAll();
    }

    public static function create($data)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO participants (full_name, email, type) VALUES (?, ?, ?)');
        $stmt->execute([
            trim($data['full_name'] ?? ''),
            trim($data['email'] ?? ''),
            $data['type'] ?? 'INTERNAL',
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update($id, $data)
    {
        $pdo = Database::connection();
        $fields = [];
        $params = [];
        
        foreach (['full_name', 'email', 'type'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = trim($data[$f] ?? '');
            }
        }
        
        if (empty($fields)) return;
        
        $params[] = $id;
        $sql = "UPDATE participants SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete($id)
    {
        $pdo = Database::connection();
        $stmtDelCerts = $pdo->prepare('DELETE FROM certificates WHERE participant_id = ?');
        $stmtDelCerts->execute([$id]);
        
        $stmt = $pdo->prepare('DELETE FROM participants WHERE id = ?');
        $stmt->execute([$id]);
    }
}
