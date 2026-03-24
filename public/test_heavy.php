<?php
require __DIR__ . '/../app/Core/Mailer.php';
require __DIR__ . '/../app/Core/CertificateGenerator.php';

try {
    echo "Initiating Certificate Generation...\n";
    $pdfPath = \app\Core\CertificateGenerator::generate([
        'name' => 'Luis Alberto Alvarez Gonzalez',
        'course' => 'Prueba de Correo Pesado',
        'token' => 'YbjJL...',
        'url' => 'http://localhost/eval',
        'background' => 'bg_69c1798f8f8e7.jpeg' // Use the heaviest image available
    ]);
    
    echo "PDF Generated at: " . $pdfPath . "\n";
    echo "PDF Size: " . (filesize($pdfPath) / 1024 / 1024) . " MB\n";

    echo "Attempting to send email via SMTP...\n";
    $attachments = [];
    if (file_exists($pdfPath)) {
        $attachments[] = [
            'path' => $pdfPath,
            'filename' => 'Prueba_Completa.pdf',
            'mime' => 'application/pdf'
        ];
    }
    
    $html = "<h1>Test de Envio PDF Real</h1>";
    $res = \app\Core\Mailer::send('luenalgo@gmail.com', 'Test Email PDF Real', $html, 'Test', $attachments);
    if ($res) {
        echo "Email sent successfully!\n";
    } else {
        echo "Email failed to send (Mailer::send returned false).\n";
    }
} catch (\Throwable $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
}
