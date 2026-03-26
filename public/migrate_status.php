<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

use app\Core\Database;

try {
    $pdo = Database::connection();
    
    // Add PENDING_REVIEW to certificates.status enum
    $pdo->exec("ALTER TABLE certificates MODIFY COLUMN status ENUM('VERIFIED', 'NOT_VERIFIED', 'PENDING_REVIEW') NOT NULL DEFAULT 'NOT_VERIFIED'");
    
    echo "Migration successful: status ENUM updated to include PENDING_REVIEW.\n";
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
