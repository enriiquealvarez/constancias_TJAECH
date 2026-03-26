<?php
namespace app\Models;

use app\Core\Database;

class Certificate
{
    public static function metrics()
    {
        $pdo = Database::connection();
        $total = $pdo->query('SELECT COUNT(c.id) AS c FROM certificates c JOIN participants p ON p.id = c.participant_id JOIN courses crs ON crs.id = c.course_id')->fetch()['c'] ?? 0;
        $verified = $pdo->query("SELECT COUNT(c.id) AS c FROM certificates c JOIN participants p ON p.id = c.participant_id JOIN courses crs ON crs.id = c.course_id WHERE c.status = 'VERIFIED'");
        $verifiedCount = $verified->fetch()['c'] ?? 0;
        $not = $pdo->query("SELECT COUNT(c.id) AS c FROM certificates c JOIN participants p ON p.id = c.participant_id JOIN courses crs ON crs.id = c.course_id WHERE c.status = 'NOT_VERIFIED'");
        $notCount = $not->fetch()['c'] ?? 0;
        $pending = $pdo->query("SELECT COUNT(c.id) AS c FROM certificates c JOIN participants p ON p.id = c.participant_id JOIN courses crs ON crs.id = c.course_id WHERE c.status = 'PENDING_REVIEW'");
        $pendingCount = $pending->fetch()['c'] ?? 0;
        return [
            'ok' => true,
            'total' => (int)$total,
            'verified' => (int)$verifiedCount,
            'not_verified' => (int)$notCount,
            'pending_review' => (int)$pendingCount,
        ];
    }

    public static function findByToken($token)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT c.*, p.full_name, p.email, crs.name AS course_name, crs.edition FROM certificates c JOIN participants p ON p.id = c.participant_id JOIN courses crs ON crs.id = c.course_id WHERE c.token = ? LIMIT 1');
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public static function all($q = '')
    {
        $pdo = Database::connection();
        if ($q) {
            $stmt = $pdo->prepare("SELECT c.*, p.full_name, p.email, crs.name AS course_name FROM certificates c JOIN participants p ON p.id = c.participant_id JOIN courses crs ON crs.id = c.course_id WHERE p.full_name LIKE ? OR crs.name LIKE ? OR c.token LIKE ? ORDER BY c.id DESC");
            $like = '%' . $q . '%';
            $stmt->execute([$like, $like, $like]);
        } else {
            $stmt = $pdo->query('SELECT c.*, p.full_name, p.email, crs.name AS course_name FROM certificates c JOIN participants p ON p.id = c.participant_id JOIN courses crs ON crs.id = c.course_id ORDER BY c.id DESC');
        }
        return $stmt->fetchAll();
    }

    public static function create($data)
    {
        $pdo = Database::connection();
        $token = $data['token'] ?? self::generateToken();
        $stmt = $pdo->prepare('INSERT INTO certificates (participant_id, course_id, doc_type, status, token, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            (int)($data['participant_id'] ?? 0),
            (int)($data['course_id'] ?? 0),
            $data['doc_type'] ?? 'Constancia',
            $data['status'] ?? 'NOT_VERIFIED',
            $token,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function issue($data)
    {
        $data['token'] = self::generateToken();
        $data['status'] = $data['status'] ?? 'NOT_VERIFIED';
        return self::create($data);
    }

    public static function getToken($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT token FROM certificates WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row['token'] ?? null;
    }

    public static function update($id, $data)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE certificates SET participant_id = ?, course_id = ?, doc_type = ?, status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([
            (int)($data['participant_id'] ?? 0),
            (int)($data['course_id'] ?? 0),
            $data['doc_type'] ?? 'Constancia',
            $data['status'] ?? 'NOT_VERIFIED',
            $id,
        ]);
    }

    public static function setStatus($id, $status)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE certificates SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public static function delete($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM certificates WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function exportRows()
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT c.id, p.full_name AS participant, crs.name AS course, c.doc_type, c.status, c.token, c.created_at FROM certificates c JOIN participants p ON p.id = c.participant_id JOIN courses crs ON crs.id = c.course_id ORDER BY c.id DESC');
        return $stmt->fetchAll();
    }

    private static function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '=');
    }
}
