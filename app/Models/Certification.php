<?php
namespace app\Models;

use app\Core\Database;

/**
 * Certificaciones emitidas por el CEMASC (Órgano Instructor en Mecanismos
 * Alternativos de Solución de Controversias). Independientes de las constancias.
 */
class Certification
{
    const AREA_NAME = 'Órgano Instructor en Mecanismos Alternativos de Solución de Controversias en Materia Administrativa';
    const DEFAULT_SUBJECT = 'Persona Facilitadora en Materia Administrativa';
    const TIMEZONE = 'America/Mexico_City';
    const EXPIRING_DAYS = 30;

    public static function today()
    {
        return new \DateTimeImmutable('today', new \DateTimeZone(self::TIMEZONE));
    }

    /**
     * Estado calculado al momento de la consulta: la certificación deja de estar
     * vigente automáticamente al día siguiente de valid_until.
     */
    public static function state($row, $today = null)
    {
        $today = $today ?: self::today();
        $tz = new \DateTimeZone(self::TIMEZONE);
        $from = new \DateTimeImmutable($row['valid_from'], $tz);
        $until = new \DateTimeImmutable($row['valid_until'], $tz);
        $daysLeft = (int)$today->diff($until)->format('%r%a');

        if (($row['status'] ?? 'ACTIVE') === 'REVOKED') {
            return ['code' => 'REVOKED', 'label' => 'REVOCADA', 'days_left' => $daysLeft];
        }
        if ($today < $from) {
            return ['code' => 'UPCOMING', 'label' => 'AÚN NO VIGENTE', 'days_left' => $daysLeft];
        }
        if ($today > $until) {
            return ['code' => 'EXPIRED', 'label' => 'NO VIGENTE', 'days_left' => $daysLeft];
        }
        return [
            'code' => $daysLeft <= self::EXPIRING_DAYS ? 'EXPIRING' : 'VALID',
            'label' => 'VIGENTE',
            'days_left' => $daysLeft,
        ];
    }

    public static function withState($row, $today = null)
    {
        $row['state'] = self::state($row, $today);
        return $row;
    }

    public static function all($q = '')
    {
        $pdo = Database::connection();
        if ($q !== '') {
            $stmt = $pdo->prepare('SELECT * FROM certifications WHERE full_name LIKE ? OR cert_number LIKE ? OR subject LIKE ? ORDER BY id DESC');
            $like = '%' . $q . '%';
            $stmt->execute([$like, $like, $like]);
        } else {
            $stmt = $pdo->query('SELECT * FROM certifications ORDER BY id DESC');
        }
        $today = self::today();
        return array_map(function ($row) use ($today) {
            return self::withState($row, $today);
        }, $stmt->fetchAll());
    }

    public static function find($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM certifications WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByToken($token)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM certifications WHERE token = ? LIMIT 1');
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public static function nextNumber()
    {
        $pdo = Database::connection();
        $max = (int)($pdo->query('SELECT MAX(CAST(cert_number AS UNSIGNED)) AS m FROM certifications')->fetch()['m'] ?? 0);
        return str_pad((string)($max + 1), 2, '0', STR_PAD_LEFT);
    }

    public static function create($data, $userId)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO certifications (cert_number, full_name, subject, valid_from, valid_until, status, token, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            trim($data['cert_number']),
            trim($data['full_name']),
            trim($data['subject']),
            $data['valid_from'],
            $data['valid_until'],
            'ACTIVE',
            self::generateToken(),
            $userId,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update($id, $data)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE certifications SET cert_number = ?, full_name = ?, subject = ?, valid_from = ?, valid_until = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([
            trim($data['cert_number']),
            trim($data['full_name']),
            trim($data['subject']),
            $data['valid_from'],
            $data['valid_until'],
            $id,
        ]);
    }

    public static function setStatus($id, $status)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE certifications SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public static function delete($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM certifications WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function metrics()
    {
        $counts = ['total' => 0, 'valid' => 0, 'expiring' => 0, 'expired' => 0, 'revoked' => 0, 'upcoming' => 0];
        foreach (self::all() as $row) {
            $counts['total']++;
            switch ($row['state']['code']) {
                case 'VALID': $counts['valid']++; break;
                case 'EXPIRING': $counts['valid']++; $counts['expiring']++; break;
                case 'EXPIRED': $counts['expired']++; break;
                case 'REVOKED': $counts['revoked']++; break;
                case 'UPCOMING': $counts['upcoming']++; break;
            }
        }
        return $counts;
    }

    public static function formatDate($date)
    {
        $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $d = new \DateTimeImmutable($date, new \DateTimeZone(self::TIMEZONE));
        return $d->format('j') . ' de ' . $months[(int)$d->format('n') - 1] . ' de ' . $d->format('Y');
    }

    private static function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '=');
    }
}
