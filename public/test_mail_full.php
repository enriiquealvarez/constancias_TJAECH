<?php
require __DIR__ . '/../app/Core/Mailer.php';

try {
    // Check config
    $cfg = require __DIR__ . '/../config/mail.php';
    echo "Mailer mode is: " . $cfg['mode'] . "\n";
    echo "Host: " . $cfg['host'] . "\n";
    
    // Generate a temporary file to act as PDF
    $pdfPath = 'C:\Users\ENRIQU~1\AppData\Local\Temp/test_dummy_cert.pdf';
    file_put_contents($pdfPath, "Dummy PDF Content");

    $attachments = [];
    if (file_exists($pdfPath)) {
        $attachments[] = [
            'path' => $pdfPath,
            'filename' => 'Prueba.pdf',
            'mime' => 'application/pdf'
        ];
    }
    
    echo "Sending email to luenalgo@gmail.com...\n";
    $html = "<h1>Test final</h1>";
    $res = \app\Core\Mailer::send('luenalgo@gmail.com', 'Test Email', $html, 'Test', $attachments);
    if ($res) {
        echo "Email sent successfully!\n";
    } else {
        echo "Email failed to send (Mailer::send returned false).\n";
    }
} catch (\Throwable $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
}
