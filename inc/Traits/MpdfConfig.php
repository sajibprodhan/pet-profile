<?php

namespace Inc\Traits;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

trait MpdfConfig {
    protected function getMpdfConfig(): Mpdf {
        $defaultConfig = ( new ConfigVariables() )->getDefaults();
        $fontConfig    = ( new FontVariables() )->getDefaults();

        return new Mpdf( [
            'fontDir'          => array_merge( $defaultConfig['fontDir'], [
                $this->getPluginPath() . 'assets/fonts',
            ] ),
            'fontdata'         => $fontConfig['fontdata'] + [
                'solaimanlipi' => [
                    'R' => 'SolaimanLipi.ttf',
                    'B' => 'SolaimanLipi-Bold.ttf',
                ],
            ],
            'default_font'     => 'solaimanlipi',
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            // 'orientation' => 'P', // L - landscape, P - portrait
            'debug'            => false,

            // Set margins
            'margin_top'       => 5,
            'margin_bottom'    => 5,
            'margin_left'      => 5,
            'margin_right'     => 5,


        ] );
    }
}
