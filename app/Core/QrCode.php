<?php
namespace app\Core;

/**
 * Genera códigos QR (SVG o PNG) con zona de silencio, listos para imprimir.
 * Usa el motor QR que ya incluye TCPDF, sin dependencias adicionales.
 */
class QrCode
{
    private static function matrix($text)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $barcode = new \TCPDF2DBarcode($text, 'QRCODE,H');
        return $barcode->getBarcodeArray();
    }

    public static function svg($text, $module = 10, $margin = 4)
    {
        $m = self::matrix($text);
        $size = ($m['num_cols'] + $margin * 2) * $module;
        $path = '';
        for ($r = 0; $r < $m['num_rows']; $r++) {
            for ($c = 0; $c < $m['num_cols']; $c++) {
                if ($m['bcode'][$r][$c]) {
                    $x = ($c + $margin) * $module;
                    $y = ($r + $margin) * $module;
                    $path .= "M{$x},{$y}h{$module}v{$module}h-{$module}z";
                }
            }
        }
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '" shape-rendering="crispEdges">'
            . '<rect width="100%" height="100%" fill="#ffffff"/>'
            . '<path fill="#000000" d="' . $path . '"/>'
            . '</svg>';
    }

    public static function png($text, $module = 24, $margin = 4)
    {
        if (!function_exists('imagecreate')) {
            throw new \RuntimeException('La extensión GD no está disponible para generar PNG.');
        }
        $m = self::matrix($text);
        $size = ($m['num_cols'] + $margin * 2) * $module;
        $img = imagecreate($size, $size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);
        for ($r = 0; $r < $m['num_rows']; $r++) {
            for ($c = 0; $c < $m['num_cols']; $c++) {
                if ($m['bcode'][$r][$c]) {
                    $x = ($c + $margin) * $module;
                    $y = ($r + $margin) * $module;
                    imagefilledrectangle($img, $x, $y, $x + $module - 1, $y + $module - 1, $black);
                }
            }
        }
        ob_start();
        imagepng($img);
        imagedestroy($img);
        return ob_get_clean();
    }
}
