<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=tjaechgob_constancias_tja;charset=utf8', 'root', '');
$pdo->exec("ALTER TABLE courses ADD COLUMN background_image VARCHAR(255) DEFAULT NULL;");
echo "DB Update OK";
