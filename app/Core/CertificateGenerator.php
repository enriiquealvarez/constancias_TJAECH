<?php
namespace app\Core;

class CertificateGenerator
{
    /**
     * @param array $data ['name' => 'John Doe', 'course' => '...', 'token' => '...', 'url' => '...', 'background' => 'bg.jpg']
     * @return string Path to generated temporary PDF
     */
    public static function generate($data)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $pdf = new \TCPDF('L', 'mm', 'LETTER', true, 'UTF-8', false);
        $pdf->SetCreator('TJA Chiapas');
        $pdf->SetAuthor('TJA Chiapas');
        $pdf->SetTitle('Constancia de Participación');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(FALSE, 0);
        $pdf->AddPage();

        $bgInput = $data['background'] ?? null;
        if ($bgInput) {
            $bgPath = __DIR__ . '/../../public/assets/certificates/' . $bgInput;
            if (file_exists($bgPath)) {
                $pdf->Image($bgPath, 0, 0, 279.4, 215.9, '', '', '', false, 300, '', false, false, 0);
            }
        }

        // (Title Texts: "O T O R G A...", "CONSTANCIA", "A:" are now baked into the background image)

        // Print Text Name
        $pdf->SetFont('times', 'B', 32);
        // RGB Color for name
        $pdf->SetTextColor(60, 60, 60);
        $pdf->SetXY(30, 114);
        $pdf->Cell(237, 15, mb_strtoupper($data['name'] ?? ''), 0, 1, 'C');

        // Print paragraph text
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(20, 20, 20);
        $pdf->SetXY(40, 134);
        $courseName = htmlspecialchars($data['course'] ?? '', ENT_QUOTES, 'UTF-8');
        $htmlText = 'Por su participación en el <b>"Programa de Capacitación: ' . $courseName . '"</b>, con el objetivo de fortalecer las capacidades institucionales y promover el ejercicio responsable, ético y transparente del servicio público.';
        $pdf->writeHTMLCell(217, 8, 40, 134, $htmlText, 0, 1, false, true, 'C', true);

        // Date
        $pdf->SetFont('helvetica', '', 11);
        $currentY = $pdf->GetY();
        // Agregamos un margen de 4mm bajo el texto
        $pdf->SetXY(40, $currentY + 4);
        $certDateStr = $data['cert_date'] ?? null;
        if ($certDateStr && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $certDateStr, $matches)) {
            $year = $matches[1];
            $month = (int)$matches[2];
            $day = (int)$matches[3];
            $dateText = "Tuxtla Gutiérrez, Chiapas, a $day de " . mb_strtolower(self::monthName($month), 'UTF-8') . " de $year";
        } else {
            $dateText = "Tuxtla Gutiérrez, Chiapas, a " . date('d \d\e ') . mb_strtolower(self::monthName(date('n')), 'UTF-8') . date(' \d\e Y');
        }
        $pdf->Cell(217, 8, $dateText, 0, 1, 'C');

        // Print QR Code
        $url = $data['url'] ?? '';
        if ($url) {
            $style = array(
                'border' => false,
                'padding' => 0,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false,
                'module_width' => 1,
                'module_height' => 1
            );
            $pdf->write2DBarcode($url, 'QRCODE,H', 230, 160, 35, 35, $style, 'N');
        }

        $tmpPath = sys_get_temp_dir() . '/' . uniqid('cert_') . '.pdf';
        $pdf->Output($tmpPath, 'F');
        
        return $tmpPath;
    }

    private static function monthName($m) {
        $months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        return $months[$m - 1] ?? '';
    }
}
