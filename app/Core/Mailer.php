<?php
namespace app\Core;

class Mailer
{
    public static function send($to, $subject, $html, $text = '')
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
        return $smtp->send($to, $subject, $html, $text);
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

    public function send($to, $subject, $html, $text)
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

        $headers = [];
        $headers[] = 'From: ' . $this->cfg['from_name'] . ' <' . $this->cfg['from_email'] . '>';
        $headers[] = 'To: <' . $to . '>';
        $headers[] = 'Subject: ' . $subject;
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        $body = implode("\r\n", $headers) . "\r\n\r\n" . $html . "\r\n.\r\n";
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

    private function command($cmd)
    {
        $this->write($cmd . "\r\n");
        $this->expect();
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
        if ($code !== null && strpos($response, (string)$code) !== 0) {
            throw new \RuntimeException('SMTP error: ' . trim($response));
        }
        return $response;
    }
}
