<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $stmt = $pdo->query('SHOW DATABASES');
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Databases found:\n";
    foreach ($dbs as $db) {
        if (strpos($db, 'tjaech') !== false) {
            echo "- " . $db . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
