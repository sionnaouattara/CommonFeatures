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

use Interop\Container\ContainerInterface;
use CommonFeatures\View\Renderer\PdfRender;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Renderer\RendererInterface;

class ViewPdfRendererFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|PdfRender|RendererInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewResolver = $container->get('ViewResolver');
        $viewRender = $container->get('ViewRenderer');
        $domPdf = $container->get('Dompdf\Dompdf');
        $pdfRender = new PdfRender();

        return $pdfRender
            ->setResolver($viewResolver)
            ->setHtmlRenderer($viewRender)
            ->setEngine($domPdf);
    }
}
