<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\Factory;

use Dompdf\Dompdf;
use Dompdf\Options;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class DOMPDFFactory
 * @package CommonFeatures\Factory
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class DOMPDFFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Dompdf|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $printerModuleConfig = $container->get('Config')['common_features_module'];

        $options = [
            'temp_dir'                   => $printerModuleConfig['temporary_directory'],
            'font_dir'                   => $printerModuleConfig['font_directory'],
            'font_cache'                 => $printerModuleConfig['font_cache_directory'],
            'chroot'                     => $printerModuleConfig['chroot'],
            'log_output_file'            => $printerModuleConfig['log_output_file'],
            'default_media_type'         => $printerModuleConfig['default_media_type'],
            'default_paper_size'         => $printerModuleConfig['default_paper_size'],
            'default_font'               => $printerModuleConfig['default_font'],
            'dpi'                        => $printerModuleConfig['dpi'],
            'font_height_ratio'          => $printerModuleConfig['font_height_ratio'],
            'is_php_enabled'             => $printerModuleConfig['enable_php'],
            'is_remote_enabled'          => $printerModuleConfig['enable_remote'],
            'is_javascript_enabled'      => $printerModuleConfig['enable_javascript'],
            'is_html5_parser_enabled'    => $printerModuleConfig['enable_html5parser'],
            'is_font_subsetting_enabled' => $printerModuleConfig['enable_fontsubsetting'],
            'debug_png'                  => $printerModuleConfig['debug_png'],
            'debug_keep_temp'            => $printerModuleConfig['debug_keep_temp'],
            'debug_css'                  => $printerModuleConfig['debug_css'],
            'debug_layout'               => $printerModuleConfig['debug_layout'],
            'debug_layout_lines'         => $printerModuleConfig['debug_layout_lines'],
            'debug_layout_blocks'        => $printerModuleConfig['debug_layout_blocks'],
            'debug_layout_inline'        => $printerModuleConfig['debug_layout_inline'],
            'debug_layout_padding_box'   => $printerModuleConfig['debug_layout_padding_box'],
            'pdf_backend'                => $printerModuleConfig['pdf_backend'],
            'pdflib_license'             => $printerModuleConfig['pdflib_license']
        ];

        return new Dompdf(new Options($options));
    }
}
