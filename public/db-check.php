<?php
$cfg = require __DIR__ . '/../config/database.php';
$pdo = new PDO(
    "mysql:host={$cfg['host']};dbname={$cfg['database']};charset=utf8mb4",
    $cfg['username'],
    $cfg['password']
);
echo 'DB usada por el sistema: ' . $pdo->query('SELECT DATABASE()')->fetchColumn();
