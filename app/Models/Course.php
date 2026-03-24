<?php
namespace app\Models;

use app\Core\Database;

class Course
{
    public static function all($q = '')
    {
        $pdo = Database::connection();
        if ($q) {
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE name LIKE ? OR edition LIKE ? ORDER BY id DESC");
            $like = '%' . $q . '%';
            $stmt->execute([$like, $like]);
        } else {
            $stmt = $pdo->query('SELECT * FROM courses ORDER BY id DESC');
        }
        return $stmt->fetchAll();
    }

    public static function create($data)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO courses (name, edition, cert_date, modality, area, background_image, speaker_background_image) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            trim($data['name'] ?? ''),
            trim($data['edition'] ?? ''),
            $data['cert_date'] ?? null,
            trim($data['modality'] ?? ''),
            trim($data['area'] ?? ''),
            $data['background_image'] ?? null,
            $data['speaker_background_image'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update($id, $data)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE courses SET name = ?, edition = ?, cert_date = ?, modality = ?, area = ?, background_image = COALESCE(?, background_image), speaker_background_image = COALESCE(?, speaker_background_image) WHERE id = ?');
        $stmt->execute([
            trim($data['name'] ?? ''),
            trim($data['edition'] ?? ''),
            $data['cert_date'] ?? null,
            trim($data['modality'] ?? ''),
            trim($data['area'] ?? ''),
            $data['background_image'] ?? null,
            $data['speaker_background_image'] ?? null,
            $id,
        ]);
    }

    public static function delete($id)
    {
        $pdo = Database::connection();
        $stmtDelCerts = $pdo->prepare('DELETE FROM certificates WHERE course_id = ?');
        $stmtDelCerts->execute([$id]);
        
        $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ?');
        $stmt->execute([$id]);
    }
}
