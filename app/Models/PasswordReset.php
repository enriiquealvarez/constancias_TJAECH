<?php
namespace app\Models;

use app\Core\Database;

class PasswordReset
{
    public static function create($userId, $token, $ttlMinutes = 15)
    {
        $pdo = Database::connection();
        $hash = password_hash($token, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL');
        $stmt->execute([$userId]);
        $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at, created_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE), NOW())');
        $stmt->execute([$userId, $hash, $ttlMinutes]);
    }

    public static function verify($userId, $token)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM password_resets WHERE user_id = ? AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        if (!password_verify($token, $row['token_hash'])) {
            return null;
        }
        return $row;
    }

    public static function markUsed($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}
