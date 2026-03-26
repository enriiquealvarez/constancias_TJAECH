<?php
namespace app\Core;

use app\Core\Database;
use app\Core\Mailer;
use app\Core\CertificateGenerator;

class NotificationService
{
    public static function sendCertificateEmail($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("
            SELECT c.*, p.full_name, p.email, crs.name AS course_name, crs.background_image, crs.speaker_background_image, crs.cert_date 
            FROM certificates c 
            JOIN participants p ON p.id = c.participant_id 
            JOIN courses crs ON crs.id = c.course_id 
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $cert = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$cert) return "Certificado no encontrado.";

        $name = $cert['full_name'];
        $email = $cert['email'];
        $courseName = $cert['course_name'];
        $docType = $cert['doc_type'];
        $token = $cert['token'];
        $link = base_url('/c/' . $token);
        
        $bg = strtolower($docType) === 'reconocimiento' ? ($cert['speaker_background_image'] ?? $cert['background_image']) : $cert['background_image'];
        $certDate = $cert['cert_date'] ?? null;

        $pdfPath = null;
        try {
            $pdfPath = CertificateGenerator::generate([
                'name' => $name,
                'course' => $courseName,
                'token' => $token,
                'url' => $link,
                'background' => $bg ?: null,
                'cert_date' => $certDate
            ]);
        } catch (\Throwable $e) {
            error_log('Error generating PDF in NotificationService: ' . $e->getMessage());
        }

        $subject = 'Felicidades, aqui esta tu ' . strtolower($docType);
        $html = "<div style='background-color: #f3f4f6; padding: 40px 20px; font-family: Arial, Helvetica, sans-serif; color: #333;'>
    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;'>
        <div style='background-color: #1b3f66; padding: 25px 30px; color: #ffffff;'>
            <div style='font-size: 16px; font-weight: bold; margin-bottom: 5px;'>Sistema de Constancias Oficiales TJAECH</div>
            <div style='font-size: 13px; opacity: 0.9;'>Tribunal de Justicia Administrativa del Estado de Chiapas</div>
        </div>
        <div style='padding: 30px;'>
            <h2 style='margin-top: 0; color: #111827; font-size: 22px;'>¡Felicidades por tu logro!</h2>
            <p style='font-size: 15px; line-height: 1.6; color: #4b5563;'>Hola <strong>{$name}</strong>,</p>
            <p style='font-size: 15px; line-height: 1.6; color: #4b5563;'>Has concluido satisfactoriamente tu evaluación para el programa de capacitación <strong>\"{$courseName}\"</strong>.</p>
            <p style='font-size: 15px; line-height: 1.6; color: #4b5563;'>Adjuntamos a este correo tu <strong>" . strtolower($docType) . " oficial</strong> en formato PDF. También puedes consultarla y verificar su autenticidad ingresando al siguiente enlace:</p>
            
            <div style='margin: 30px 0; text-align: center;'>
                <a href='{$link}' style='display: inline-block; background-color: #1b3f66; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; font-size: 15px;'>Ver Documento Oficial</a>
            </div>

            <div style='background-color: #fcfcfc; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px;'>
                <p style='margin: 0 0 10px 0; font-size: 14px; color: #475569;'>
                    <strong style='color: #1e293b;'>Folio / Token de verificación:</strong> {$token}
                </p>
                <p style='margin: 0; font-size: 14px; color: #475569;'>
                    <strong style='color: #1e293b;'>Contacto:</strong> <a href='mailto:informatica@tjaech.gob.mx' style='color: #2563eb; text-decoration: none;'>informatica@tjaech.gob.mx</a>
                </p>
            </div>

            <p style='font-size: 13px; color: #64748b; margin-top: 30px;'>
                Conserva este mensaje para futuras referencias sobre tu participación.
            </p>
        </div>
        <div style='background-color: #f8fafc; padding: 15px 30px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #64748b; text-align: center;'>
            Instituto de Justicia Administrativa - Tribunal de Justicia Administrativa del Estado de Chiapas
        </div>
    </div>
</div>";
        
        $text = "Felicidades {$name}\n\nHas concluido satisfactoriamente tu evaluacion para {$courseName}.\n\nPuedes ver tu documento oficial aqui: {$link}\n\nTribunal de Justicia Administrativa del Estado de Chiapas";

        try {
            $attachments = [];
            if ($pdfPath && file_exists($pdfPath)) {
                $attachments[] = [
                    'path' => $pdfPath,
                    'filename' => 'Constancia_' . preg_replace('/[^A-Za-z0-9]/', '_', $name) . '.pdf',
                    'mime' => 'application/pdf'
                ];
            }
            Mailer::send($email, $subject, $html, $text, $attachments);
            if ($pdfPath && file_exists($pdfPath)) {
                @unlink($pdfPath); // Clean up temp file
            }
            return null; // Success
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            error_log('Error sending cert email in NotificationService: ' . $msg);
            return $msg;
        }
    }
}
