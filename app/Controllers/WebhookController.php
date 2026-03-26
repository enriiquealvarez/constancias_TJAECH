<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Core\Database;
use app\Models\Participant;
use app\Models\Course;
use app\Models\Certificate;
use app\Core\Mailer;

class WebhookController extends Controller
{
    public function handle()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method !== 'POST') {
            $this->json(['ok' => false, 'message' => 'Metodo no soportado.'], 405);
            return;
        }

        // Check authentication
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (empty($headers) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
             $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
        }
        $authHeader = $headers['Authorization'] ?? '';
        $secret = $GLOBALS['appConfig']['webhook_secret'] ?? '';
        
        if (empty($secret) || $authHeader !== 'Bearer ' . $secret) {
            $this->json(['ok' => false, 'message' => 'No autorizado.'], 403);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) $payload = [];

        $name = trim($payload['participant_name'] ?? '');
        $email = trim($payload['participant_email'] ?? '');
        $courseName = trim($payload['course_name'] ?? '');
        $docType = trim($payload['doc_type'] ?? 'Constancia');

        if (empty($name) || empty($email) || empty($courseName)) {
            $this->json(['ok' => false, 'message' => 'Datos incompletos.'], 422);
            return;
        }

        try {
            $pdo = Database::connection();

            // Find or create participant
            $stmtParticipant = $pdo->prepare("SELECT id FROM participants WHERE email = ? LIMIT 1");
            $stmtParticipant->execute([$email]);
            $participantId = $stmtParticipant->fetchColumn();
            if (!$participantId) {
                $participantId = Participant::create([
                    'full_name' => $name,
                    'email' => $email,
                    'type' => 'EXTERNAL'
                ]);
            }

            // Find or create course
            $stmtCourse = $pdo->prepare("SELECT id FROM courses WHERE name = ? LIMIT 1");
            $stmtCourse->execute([$courseName]);
            $courseId = $stmtCourse->fetchColumn();
            if (!$courseId) {
                $courseId = Course::create([
                    'name' => $courseName,
                    'edition' => date('Y'),
                    'modality' => 'En lnea',
                    'area' => 'General'
                ]);
            }

            // Verify if a certificate already exists to avoid duplicates
            $stmtCert = $pdo->prepare("SELECT id, token FROM certificates WHERE participant_id = ? AND course_id = ? AND doc_type = ? LIMIT 1");
            $stmtCert->execute([$participantId, $courseId, $docType]);
            $existingCert = $stmtCert->fetch();

            if ($existingCert) {
                $certId = $existingCert['id'];
                $token = $existingCert['token'];
            } else {
                // Issue new certificate
                $certId = Certificate::issue([
                    'participant_id' => $participantId,
                    'course_id' => $courseId,
                    'doc_type' => $docType,
                    'status' => 'PENDING_REVIEW'
                ]);
                $token = Certificate::getToken($certId);
            }

            // Send Email (Skipped for webhook, now requires admin review)
            // $mailError = $this->sendEmail($name, $email, $courseName, $docType, $token, $courseId);
            $mailError = null;

            $this->json([
                'ok' => true,
                'message' => 'Certificado registrado para revisión' . ($mailError ? ' PERO EL CORREO FALLÓ: ' . $mailError : ''),
                'mail_error' => $mailError,
                'id' => $certId,
                'token' => $token
            ]);
        } catch (\Throwable $e) {
            error_log('Webhook Error: ' . $e->getMessage());
            $this->json(['ok' => false, 'message' => 'Error interno.'], 500);
        }
    }

    private function sendEmail($name, $email, $courseName, $docType, $token, $courseId) {
        $link = base_url('/c/' . $token);

        $pdo = Database::connection();
        $stmtCourseData = $pdo->prepare("SELECT background_image, speaker_background_image, cert_date FROM courses WHERE id = ?");
        $stmtCourseData->execute([$courseId]);
        $courseData = $stmtCourseData->fetch(\PDO::FETCH_ASSOC);
        $bg = strtolower($docType) === 'reconocimiento' ? ($courseData['speaker_background_image'] ?? $courseData['background_image']) : $courseData['background_image'];
        $certDate = $courseData['cert_date'] ?? null;

        $pdfPath = null;
        try {
            $pdfPath = \app\Core\CertificateGenerator::generate([
                'name' => $name,
                'course' => $courseName,
                'token' => $token,
                'url' => $link,
                'background' => $bg ?: null,
                'cert_date' => $certDate
            ]);
        } catch (\Throwable $e) {
            error_log('Error generating PDF: ' . $e->getMessage());
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
            error_log("Sending webhook email to $email with " . count($attachments) . " attachments");
            Mailer::send($email, $subject, $html, $text, $attachments);
            if ($pdfPath && file_exists($pdfPath)) {
                @unlink($pdfPath); // Clean up temp file
            }
            return null; // Success
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            error_log('Error sending webhook cert email: ' . $msg);
            return $msg;
        }
    }
}
