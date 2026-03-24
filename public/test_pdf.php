<?php
require __DIR__ . '/../app/Core/CertificateGenerator.php';

try {
    $path = \app\Core\CertificateGenerator::generate([
        'name' => 'Luis Alberto',
        'course' => 'Prueba de Emails',
        'token' => 'TESTING123',
        'url' => 'http://localhost:8001/c/TESTING123',
        'background' => null
    ]);
    
    echo "PDF generated at: $path\n";
    if (file_exists($path)) {
        echo "Size: " . filesize($path) . " bytes\n";
    }
} catch (\Throwable $e) {
    echo "Error generating PDF: " . $e->getMessage() . "\n";
}
