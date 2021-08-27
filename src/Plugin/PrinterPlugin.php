<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\Plugin;

use ArrayAccess;
use CommonFeatures\View\Model\PdfModel;
use Traversable;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class PrinterPlugin
 * @package CommonFeatures\Plugin
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class PrinterPlugin extends AbstractPlugin
{
    const DISPLAY_INLINE = 'inline';
    const DISPLAY_ATTACHMENT = 'attachment';

    const DEFAULT_FILE_NAME = 'untitled.pdf';

    /**
     * @var PdfModel
     */
    protected $pdfModel;

    /**
     * @var array;
     */
    protected $options;

    /**
     * @var array;
     */
    protected $variables;

    /**
     * PrinterPlugin constructor.
     */
    public function __construct()
    {
        if (! $this->pdfModel instanceof PdfModel) {
            $this->pdfModel = new PdfModel();
        }
    }

    /**
     * @param $name
     * @param $value
     * @return PdfModel
     */
    public function setOption($name, $value)
    {
        $this->pdfModel->setOption($name, $value);
        return $this->pdfModel;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        $name = (string) $name;
        return $this->pdfModel->getOption($name, $default = null);
    }

    /***
     * @param $name
     * @param $value
     * @return PdfModel
     */
    public function setVariable($name, $value)
    {
        $this->pdfModel->getVariables()[(string) $name] = $value;
        return $this->pdfModel;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getVariable($name, $default = null)
    {
        return $this->pdfModel->getVariable($name, $default);
    }

    /**
     * @param $variables
     * @param bool $overwrite
     * @return PdfModel
     */
    public function setVariables($variables, $overwrite = false)
    {
        $this->pdfModel->setVariables($variables, $overwrite);
        return $this->pdfModel;
    }

    /**
     * @return array|ArrayAccess|Traversable
     */
    public function getVariables()
    {
        return  $this->pdfModel->getVariables();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->pdfModel->getOptions();
    }
}
