<?php
require __DIR__ . '/../app/Core/Mailer.php';

try {
    $pdfPath = 'C:\Users\ENRIQU~1\AppData\Local\Temp/cert_69c17b8811a66.pdf';
    $attachments = [];
    if (file_exists($pdfPath)) {
        $attachments[] = [
            'path' => $pdfPath,
            'filename' => 'Prueba.pdf',
            'mime' => 'application/pdf'
        ];
    }
    
    // Read mail config directly to find the configured to email?
    // Let's just send to enriquealvarez or the test email used here.
    // I will use a dummy email or read admin email from env?
    // Actually the mail.php config might have a log mode?
    $cfg = require __DIR__ . '/../config/mail.php';
    if (($cfg['mode'] ?? 'smtp') === 'log') {
       echo "MAILER IS IN LOG MODE!\n";
    }

    echo "Sending email...\n";
    $res = \app\Core\Mailer::send('informatica@tjaech.gob.mx', 'Prueba PDF', '<h1>Prueba</h1>', 'Prueba', $attachments);
    echo "Result: " . ($res ? 'SUCCESS' : 'FAILURE') . "\n";
} catch (\Throwable $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
}
