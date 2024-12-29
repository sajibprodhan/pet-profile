<?php

namespace Mpdf\QrCode\Output;

use Mpdf\QrCode\QrCode;

class Html {
    /**
     * Generate HTML for a QR code.
     *
     * @param QrCode $qrCode
     * @param int $size Size of each QR code block in pixels (default: 4px)
     * @return string
     */
    public function output( QrCode $qrCode, $size = 4 ) {
        $qrSize = $qrCode->getQrSize();
        $final  = $qrCode->getFinal();

        // Border adjustments
        $minSize = $qrCode->isBorderDisabled() ? 4 : 0;
        $maxSize = $qrCode->isBorderDisabled() ? $qrSize - 4 : $qrSize;

        // Initialize HTML table
        $html = '<table class="qr qrcustomcss" cellpadding="0" cellspacing="0" style="font-size: 1px; border-collapse: collapse;">';

        // Generate rows for QR code
        for ( $y = $minSize; $y < $maxSize; $y++ ) {
            $html .= '<tr style="height: ' . $size . 'px;">'; // Row height
            for ( $x = $minSize; $x < $maxSize; $x++ ) {
                $index = $x + $y * $qrSize; // Calculate array index
                $on    = $final[$index] ?? 0; // Fallback to 0 if index is out of bounds
                $html .= '<td style="width: ' . $size . 'px; height: ' . $size . 'px; background-color: ' . ( $on ? '#000' : '#FFF' ) . ';"></td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}
