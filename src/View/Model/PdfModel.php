<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\View\Model;

use Laminas\View\Model\ViewModel;

/**
 * Class PdfModel
 * @package CommonFeatures\View\Model
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class PdfModel extends ViewModel
{
    const DISPLAY_INLINE = 'inline';
    const DISPLAY_ATTACHMENT = 'attachment';

    const DEFAULT_FILE_NAME = 'untitled.pdf';

    /**
     * Renderer options
     * @var array
     */
    protected $options = [
        'paperSize' => '8x11',
        'paperOrientation' => 'portrait',
        'basePath' => '/',
        'fileName' => self::DEFAULT_FILE_NAME,
        'display' => self::DISPLAY_INLINE
    ];

    /**
     * PDF probably won't need to be captured into a
     * a parent container by default.
     *
     * @var string
     */
    protected $captureTo = null;

    /**
     * PDF is usually terminal
     *
     * @var bool
     */
    protected $terminate = true;
}
