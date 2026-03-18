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
        $stmt = $pdo->prepare('INSERT INTO courses (name, edition, start_date, end_date, modality, area) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            trim($data['name'] ?? ''),
            trim($data['edition'] ?? ''),
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            trim($data['modality'] ?? ''),
            trim($data['area'] ?? ''),
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update($id, $data)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE courses SET name = ?, edition = ?, start_date = ?, end_date = ?, modality = ?, area = ? WHERE id = ?');
        $stmt->execute([
            trim($data['name'] ?? ''),
            trim($data['edition'] ?? ''),
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            trim($data['modality'] ?? ''),
            trim($data['area'] ?? ''),
            $id,
        ]);
    }

    public static function delete($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ?');
        $stmt->execute([$id]);
    }
}
