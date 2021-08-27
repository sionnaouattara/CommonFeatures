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
use CommonFeatures\Listener\PdfStrategy;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ViewPdfStrategyFactory
 * @package CommonFeatures\Factory
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class ViewPdfStrategyFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|PdfStrategy
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewPdfRenderer = $container->get('ViewPdfRenderer');
        return new PdfStrategy($viewPdfRenderer);
    }
}
