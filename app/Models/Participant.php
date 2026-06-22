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
        $email = trim($data['email'] ?? '');
        // Convert empty email to NULL to avoid trigger issues
        if ($email === '') {
            $email = null;
        }
        $stmt->execute([
            trim($data['full_name'] ?? ''),
            $email,
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
                $value = trim($data[$f] ?? '');
                // Convert empty email to NULL to avoid trigger issues
                if ($f === 'email' && $value === '') {
                    $value = null;
                }
                error_log("UPDATE FIELD $f = '$value' (isset=" . (isset($data[$f]) ? '1' : '0') . ")");
                $params[] = $value;
            }
        }
        
        if (empty($fields)) return;
        
        $params[] = $id;
        $sql = "UPDATE participants SET " . implode(', ', $fields) . " WHERE id = ?";
        error_log("UPDATE SQL: $sql with params: " . json_encode($params));
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Verify what was saved
        $verify = $pdo->prepare('SELECT full_name, email, type FROM participants WHERE id = ?');
        $verify->execute([$id]);
        $saved = $verify->fetch(\PDO::FETCH_ASSOC);
        error_log("AFTER UPDATE DB HAS: " . json_encode($saved));
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
