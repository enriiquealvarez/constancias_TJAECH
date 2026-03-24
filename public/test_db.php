<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=tjaechgob_constancias_tja;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("DESCRIBE participants type");
print_r($stmt->fetch(PDO::FETCH_ASSOC));

// Now alter it
$pdo->exec("ALTER TABLE participants MODIFY COLUMN type ENUM('INTERNAL', 'EXTERNAL', 'PONENTE') NOT NULL DEFAULT 'INTERNAL'");
echo "Altered successfully!\n";
