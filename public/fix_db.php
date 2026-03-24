<?php
$host = '127.0.0.1';
$db   = 'tjaechgob_constancias_tja';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $pdo->exec("ALTER TABLE courses ADD COLUMN cert_date DATE NULL;");
        echo "ADDED cert_date<br>";
    } catch (\Throwable $e) {
        echo "ERR ADDING cert_date: " . $e->getMessage() . "<br>";
    }

    try {
        $pdo->exec("ALTER TABLE courses DROP COLUMN start_date;");
        echo "DROPPED start_date<br>";
    } catch (\Throwable $e) {
        echo "ERR DROPPING start_date: " . $e->getMessage() . "<br>";
    }

    try {
        $pdo->exec("ALTER TABLE courses DROP COLUMN end_date;");
        echo "DROPPED end_date<br>";
    } catch (\Throwable $e) {
        echo "ERR DROPPING end_date: " . $e->getMessage() . "<br>";
    }

    $stmt = $pdo->query("DESCRIBE courses");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($cols);
    echo "</pre>";
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
