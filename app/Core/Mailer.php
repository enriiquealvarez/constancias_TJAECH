<?php
namespace app\Core;

class Mailer
{
    public static function send($to, $subject, $html, $text = '', $attachments = [])
    {
        $cfg = require __DIR__ . '/../../config/mail.php';
        if (($cfg['mode'] ?? 'smtp') === 'log') {
            self::logMessage($cfg, $to, $subject, $text ?: strip_tags($html));
            return true;
        }
        if (empty($cfg['host']) || empty($cfg['username']) || empty($cfg['password']) || empty($cfg['from_email'])) {
            return false;
        }
        $smtp = new SmtpClient($cfg);
        return $smtp->send($to, $subject, $html, $text, $attachments);
    }

    private static function logMessage($cfg, $to, $subject, $body)
    {
        $path = $cfg['log_path'] ?? __DIR__ . '/../../storage/mail.log';
        $line = '[' . date('Y-m-d H:i:s') . '] To: ' . $to . ' | Subject: ' . $subject . ' | Body: ' . $body . PHP_EOL;
        @file_put_contents($path, $line, FILE_APPEND);
    }
}

class SmtpClient
{
    private $socket;
    private $cfg;

    public function __construct($cfg)
    {
        $this->cfg = $cfg;
    }

    public function send($to, $subject, $html, $text, $attachments = [])
    {
        $this->connect();
        $this->command('EHLO localhost');

        if ($this->cfg['encryption'] === 'tls') {
            $this->command('STARTTLS');
            if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new \RuntimeException('No se pudo iniciar TLS');
            }
            $this->command('EHLO localhost');
        }

        $this->authLogin();
        $this->command('MAIL FROM:<' . $this->cfg['from_email'] . '>');
        $this->command('RCPT TO:<' . $to . '>');
        $this->command('DATA');

        $boundary = md5(time() . uniqid());
        $headers = [];
        $headers[] = 'From: ' . $this->cfg['from_name'] . ' <' . $this->cfg['from_email'] . '>';
        $headers[] = 'To: <' . $to . '>';
        $headers[] = 'Subject: ' . $subject;
        $headers[] = 'MIME-Version: 1.0';

        if (empty($attachments)) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'Content-Transfer-Encoding: base64';
            $body = implode("\r\n", $headers) . "\r\n\r\n" . chunk_split(base64_encode($html));
        } else {
            $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
            $body = implode("\r\n", $headers) . "\r\n\r\n";
            $body .= "--" . $boundary . "\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= chunk_split(base64_encode($html)) . "\r\n\r\n";

            foreach ($attachments as $att) {
                if (!empty($att['path']) && file_exists($att['path'])) {
                    $content = chunk_split(base64_encode(file_get_contents($att['path'])));
                    $filename = $att['filename'] ?? basename($att['path']);
                    $mime = $att['mime'] ?? 'application/octet-stream';
                    
                    $body .= "--" . $boundary . "\r\n";
                    $body .= "Content-Type: " . $mime . "; name=\"" . $filename . "\"\r\n";
                    $body .= "Content-Transfer-Encoding: base64\r\n";
                    $body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
                    $body .= $content . "\r\n\r\n";
                }
            }
            $body .= "--" . $boundary . "--";
        }
        
        $body .= "\r\n.\r\n";
        $this->write($body);
        $this->expect(250);
        $this->command('QUIT');
        fclose($this->socket);
        return true;
    }

    private function connect()
    {
        $host = $this->cfg['host'];
        $port = (int)$this->cfg['port'];
        $prefix = $this->cfg['encryption'] === 'ssl' ? 'ssl://' : '';
        $this->socket = fsockopen($prefix . $host, $port, $errno, $errstr, 30);
        if (!$this->socket) {
            throw new \RuntimeException('SMTP no disponible: ' . $errstr);
        }
        $this->expect(220);
    }

    private function authLogin()
    {
        $this->command('AUTH LOGIN');
        $this->command(base64_encode($this->cfg['username']));
        $this->command(base64_encode($this->cfg['password']));
    }

    private function command($cmd, $expectedCode = null)
    {
        $this->write($cmd . "\r\n");
        return $this->expect($expectedCode);
    }

    private function write($data)
    {
        fwrite($this->socket, $data);
    }

    private function expect($code = null)
    {
        $response = '';
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        
        $statusCode = (int)substr($response, 0, 3);
        
        // If a specific code was expected, check it.
        if ($code !== null && $statusCode !== (int)$code) {
            throw new \RuntimeException('SMTP error (Expected ' . $code . '): ' . trim($response));
        }
        
        // If no specific code but it's a 4xx or 5xx error, always throw!
        if ($code === null && $statusCode >= 400) {
            throw new \RuntimeException('SMTP error: ' . trim($response));
        }
        
        return $response;
    }
}
