<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\View\Renderer;

use Dompdf\Dompdf;
use CommonFeatures\View\Model\PdfModel;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Renderer\RendererInterface as Renderer;
use Laminas\View\Resolver\ResolverInterface as Resolver;

/**
 * Class PdfRender
 * @package CommonFeatures\View\Renderer
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class PdfRender implements Renderer
{
    /**
     * @var Dompdf|null
     */
    private $dompdf = null;

    /**
     * @var Resolver|null
     */
    private $resolver = null;

    /**
     * @var Renderer|null
     */
    private $htmlRenderer = null;

    /**
     * @param Dompdf $dompdf
     * @return $this
     */
    public function setEngine(Dompdf $dompdf)
    {
        $this->dompdf = $dompdf;
        return $this;
    }

    /**
     * @return Dompdf|mixed|null
     */
    public function getEngine()
    {
        return $this->dompdf;
    }

    /**
     * @param Resolver $resolver
     * @return $this|Renderer
     */
    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }

    /**
     * @return Renderer|null
     */
    public function getHtmlRenderer()
    {
        return $this->htmlRenderer;
    }

    /**
     * @param Renderer $htmlRenderer
     * @return $this
     */
    public function setHtmlRenderer(Renderer $htmlRenderer)
    {
        $this->htmlRenderer = $htmlRenderer;
        return $this;
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string|ModelInterface $nameOrModel
     * @param null $values
     * @return string|null
     */
    public function render($nameOrModel, $values = null)
    {
        if (! ($nameOrModel instanceof PdfModel)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects a PdfModel as the first argument; received "%s"',
                __METHOD__,
                (is_object($nameOrModel) ? get_class($nameOrModel) : gettype($nameOrModel))
            ));
        }

        $html = $this->getHtmlRenderer()->render($nameOrModel, $values);

        $paperSize = $nameOrModel->getOption('paperSize');
        $paperOrientation = $nameOrModel->getOption('paperOrientation');
        $basePath = $nameOrModel->getOption('basePath');

        $pdf = $this->getEngine();
        $pdf->setPaper($paperSize, $paperOrientation);
        $pdf->setBasePath($basePath);

        $pdf->loadHtml($html);
        $pdf->render();

        return $pdf->output();
    }
}
