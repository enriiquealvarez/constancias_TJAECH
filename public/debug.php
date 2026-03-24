<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=tjaechgob_constancias_tja;charset=utf8', 'root', '');
    $stmt = $pdo->query("SHOW COLUMNS FROM courses LIKE 'background_image'");
    if ($stmt->fetch()) {
        echo "Column exists\n";
    } else {
        echo "Column DOES NOT exist\n";
        $pdo->exec("ALTER TABLE courses ADD COLUMN background_image VARCHAR(255) DEFAULT NULL;");
        echo "Column added now\n";
    }
} catch (\Throwable $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
